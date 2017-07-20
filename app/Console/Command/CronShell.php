<?php

class CronShell extends AppShell
{
	// the models to use
	public $uses = array('User', 'LoginHistory', 'Vector', 'Dblogger.Dblog', 'Media', 'OrgGroup');
	
	public function startup() 
	{
		$this->clear();
		$this->out('Cron Shell');
		$this->hr();
		return parent::startup();
	}
	
	public function getOptionParser()
	{
	/*
	 * Parses out the options/arguments.
	 * http://book.cakephp.org/2.0/en/console-and-shells.html#configuring-options-and-generating-help
	 */
	
		$parser = parent::getOptionParser();
		
		$parser->description(__d('cake_console', 'The Cron Shell runs all needed cron jobs'));
		
		$parser->addSubcommand('failed_logins', array(
			'help' => __d('cake_console', 'Emails a list of failed logins to the admins and users every 10 minutes'),
			'parser' => array(
				'options' => array(
					'minutes' => array(
						'help' => __d('cake_console', 'Change the time frame from 10 minutes ago.'),
						'short' => 'm',
						'default' => 10,
					),
				),
			),
		));
		
		$parser->addSubcommand('change_log', array(
			'help' => __d('cake_console', 'Sends an email when a change is made to whomever is involved with the change.'),
			'parser' => array(
				'options' => array(
					'minutes' => array(
						'help' => __d('cake_console', 'Change the time frame from 5 minutes ago.'),
						'short' => 'm',
						'default' => 5,
					),
				),
			),
		));
		$parser->addSubcommand('notify_emails', array(
			'help' => __d('cake_console', 'Sends an email of %s with an %s state.', __('Media'), __('open')),
			'parser' => array(
				'options' => array(
				),
			),
		));
		
		return $parser;
	}
	
	public function failed_logins()
	{
	/*
	 * Emails a list of failed logins to the admins every 5 minutes
	 * Only sends an email if there was a failed login
	 * Everything is taken care of in the Task
	 */
		$FailedLogins = $this->Tasks->load('Utilities.FailedLogins')->execute($this);
	}
	
	public function change_log()
	{
	/*
	 * Sends an email when a change is made
	 * Send an email to whomever is involved with the change
	 */
	 	Configure::write('debug', 1);
		$minutes = '5';
		if(isset($this->params['minutes']))
		{
			$minutes = $this->params['minutes'];
		}
		
		/////////// get the list of changes
		$logs = $this->Dblog->latest($minutes);
		if(!$logs)
		{
			$this->out(__('No logged changes'));
			return false;
		}
		
		$this->out(__('Found %s logged changes.', count($logs)), 1, Shell::QUIET);
		
		// list of models that is considered a user we can email
//		$userModels = array('MediaAddedUser', 'MediaReceivedUser', 'ChainReceivedUser', 'ChainReleasedUser');
		
		// build a cache of users
		$user_cache = array();
		$user_ids = array();
		
		
		/////////// add the user_info to the user_cache
		foreach($logs as $log)
		{
			// only email changes when a media or custody chain is affected
			if(!in_array($log['Dblog']['model'], array('Media', 'MediaDetail', 'CustodyChain'))) continue;
			if(isset($log['Media']))
			{
				$user_ids[$log['Media']['added_user_id']] = $log['Media']['added_user_id'];
				$user_ids[$log['Media']['modified_user_id']] = $log['Media']['modified_user_id'];
				$user_ids[$log['Media']['received_user_id']] = $log['Media']['received_user_id'];
				
			}
			if(isset($log['CustodyChain']))
			{
				$user_ids[$log['CustodyChain']['released_user_id']] = $log['CustodyChain']['released_user_id'];
				$user_ids[$log['CustodyChain']['received_user_id']] = $log['CustodyChain']['received_user_id'];
				$user_ids[$log['CustodyChain']['added_user_id']] = $log['CustodyChain']['added_user_id'];
			}
		}
		
		$user_cache = $this->User->changeLogList($user_ids);
		
		/////////// sort the logs into 1 of 3 different types: created, updated, closed
		$logs_created = array();
		$logs_updated = array();
		$logs_closed = array();
		foreach($logs as $log)
		{	
			// only email changes when a media or custody chain is affected
			if(!in_array($log['Dblog']['model'], array('Media', 'MediaDetail', 'CustodyChain'))) continue;			
			
			// create a log key
			$log_key = $log['Dblog']['model']. '-'. $log['Dblog']['model_id']. '-'. $log['Dblog']['id'];
			
			// attach the MediaAddedUser
			if(!isset($log['MediaAddedUser']))
			{
				$log['MediaAddedUser'] = array();
				
				if(isset($log['Media']['added_user_id']) and $log['Media']['added_user_id'] > 0)
				{
					if(!isset($user_cache[$log['Media']['added_user_id']]))
					{
						$user_cache[$log['Media']['added_user_id']] = array();
						$user = $this->User->find('first', array(
							'recursive' => 0,
							'conditions' => array(
								'User.id' => $log['Media']['added_user_id'],
								'User.active' => true,
							),
						));
						if($user) $user_cache[$log['Media']['added_user_id']] = $user;
					}
					$log['MediaAddedUser'] = $user_cache[$log['Media']['added_user_id']]['User'];
				}
			}
			
			// attach the MediaReceivedUser
			if(!isset($log['MediaReceivedUser']))
			{
				$log['MediaReceivedUser'] = array();
				
				if(isset($log['Media']['received_user_id']) and $log['Media']['received_user_id'] > 0)
				{
					if(!isset($user_cache[$log['Media']['received_user_id']]))
					{
						$user_cache[$log['Media']['received_user_id']] = array();
						$user = $this->User->find('first', array(
							'recursive' => 0,
							'conditions' => array(
								'User.id' => $log['Media']['received_user_id'],
								'User.active' => true,
							),
						));
						if($user) $user_cache[$log['Media']['received_user_id']] = $user;
					}
					$log['MediaReceivedUser'] = $user_cache[$log['Media']['received_user_id']]['User'];
				}
			}
			
			// attach the MediaModifiedUser
			if(!isset($log['MediaModifiedUser']))
			{
				$log['MediaModifiedUser'] = array();
				
				if(isset($log['Media']['modified_user_id']) and $log['Media']['modified_user_id'] > 0)
				{
					if(!isset($user_cache[$log['Media']['modified_user_id']]))
					{
						$user_cache[$log['Media']['modified_user_id']] = array();
						$user = $this->User->find('first', array(
							'recursive' => 0,
							'conditions' => array(
								'User.id' => $log['Media']['modified_user_id'],
								'User.active' => true,
							),
						));
						if($user) $user_cache[$log['Media']['modified_user_id']] = $user;
					}
					$log['MediaModifiedUser'] = $user_cache[$log['Media']['modified_user_id']]['User'];
				}
			}
			
			// track all user_ids for this entry
			$log['user_ids'] = array();
			foreach($log as $modelName => $modelValues)
			{
				// if email is set, and an id is set, it's a user
				if(isset($modelValues['email']) and isset($modelValues['id']))
				{
					$log['user_ids'][$modelValues['id']] = $modelValues['id'];
				}
			}
			
			// map the fields
			$log = $this->Dblog->mapFields($log);
			
			// new entries
			if($log['Dblog']['new'] == 1)
			{
				$logs_created[$log_key] = $log;
				continue;
			}
			
			// closed entries
			$changes = unserialize($log['Dblog']['changes']);
			if(isset($changes['state']) and $changes['state'] == 0)
			{
				$logs_closed[$log_key] = $log;
				continue;
			}
			
			// updated entries
			$logs_updated[$log_key] = $log;
		}
		
		/////////// seperate the users into groups based on their settings
		// list of users that want emails all of the time when created
		$users_created_all = array();
		// list of users that want emails only when mentioned
		$users_created_mentioned = array();
		// list of users that want emails all of the time when updated
		$users_updated_all = array();
		// list of users that want emails only when mentioned
		$users_updated_mentioned = array();
		// list of users that want emails all of the time when closed
		$users_closed_all = array();
		// list of users that want emails only when mentioned
		$users_closed_mentioned = array();
		
		foreach($user_cache as $user_id => $user)
		{
			// make sure the user are all active
			if(!$user['User']['active'])
				continue;
			if($user['UsersSetting']['email_new'] == 2) $users_created_all[$user_id] = $user['User'];
			if($user['UsersSetting']['email_new'] == 1) $users_created_mentioned[$user_id] = $user['User'];
			if($user['UsersSetting']['email_updated'] == 2) $users_updated_all[$user_id] = $user['User'];
			if($user['UsersSetting']['email_updated'] == 1) $users_updated_mentioned[$user_id] = $user['User'];
			if($user['UsersSetting']['email_closed'] == 2) $users_closed_all[$user_id] = $user['User'];
			if($user['UsersSetting']['email_closed'] == 1) $users_closed_mentioned[$user_id] = $user['User'];
		}
		
		// build the emails
		$emails = array();
		
		// map the created log to the users that want an email when one is created
		foreach($logs_created as $log_id => $log)
		{
			$media_id = 0;
			if(isset($log['Media']['id']))
			{
				$media_id = $log['Media']['id'];
			}
			else
			{
				$changes = unserialize($log['Dblog']['changes']);
				if(isset($changes['media_id'])) $media_id = $changes['media_id'];
			}
		
			// build one email for each user that want all
			foreach($users_created_all as $user_id => $user_created_all)
			{
				// make sure there is an entry into the email array
				$email_address = $user_created_all['email'];
				if(!isset($emails[$email_address]))
				{
					$emails[$email_address] = array(
						'email' => $email_address,
						'name' => $user_created_all['name'],
						'log_count_updated' => 0,
						'log_count_new' => 0,
						'log_count_closed' => 0,
						'log_count_deleted' => 0,
						'logs' => array(),
					);
				}
				
				($log['Dblog']['deleted']?$emails[$email_address]['log_count_deleted']++:$emails[$email_address]['log_count_new']++);
				
				// add this log to the email
				$emails[$email_address]['logs'][$log_id] = array(
					'model' => $log['Dblog']['model'],
					'model_id' => $log['Dblog']['model_id'],
					'user_id' => $log['Dblog']['user_id'],
					'user' => (isset($log['DblogUser']['email'])?$log['DblogUser']['email']:''),
					'media_id' => $media_id,
					'status' => ($log['Dblog']['deleted']?4:1),
					'timestamp' => $log['Dblog']['created'],
					'message' => ($log['Dblog']['mapped_readable']?$log['Dblog']['mapped_readable']:str_replace(';;;', "\n", $log['Dblog']['readable'])),
				);
			}
			
			// build one email for each user that want all
			foreach($users_created_mentioned as $user_id => $user_created_mentioned)
			{
				if(!in_array($user_id, $log['user_ids'])) continue;
				
				// make sure there is an entry into the email array
				$email_address = $user_created_mentioned['email'];
				if(!isset($emails[$email_address]))
				{
					$emails[$email_address] = array(
						'email' => $email_address,
						'name' => $user_created_mentioned['name'],
						'log_count_updated' => 0,
						'log_count_new' => 0,
						'log_count_closed' => 0,
						'log_count_deleted' => 0,
						'logs' => array(),
					);
				}
				
				($log['Dblog']['deleted']?$emails[$email_address]['log_count_deleted']++:$emails[$email_address]['log_count_new']++);
				
				// add this log to the email
				$emails[$email_address]['logs'][$log_id] = array(
					'model' => $log['Dblog']['model'],
					'model_id' => $log['Dblog']['model_id'],
					'user_id' => $log['Dblog']['user_id'],
					'user' => (isset($log['DblogUser']['email'])?$log['DblogUser']['email']:''),
					'media_id' => $media_id,
					'status' => ($log['Dblog']['deleted']?4:1),
					'timestamp' => $log['Dblog']['created'],
					'message' => ($log['Dblog']['mapped_readable']?$log['Dblog']['mapped_readable']:str_replace(';;;', "\n", $log['Dblog']['readable'])),
				);
			}
		}
		
		// map the updated log to the users that want an email when one is updated
		foreach($logs_updated as $log_id => $log)
		{
			$media_id = 0;
			if(isset($log['Media']['id']))
			{
				$media_id = $log['Media']['id'];
			}
			else
			{
				$changes = unserialize($log['Dblog']['changes']);
				if(isset($changes['media_id'])) $media_id = $changes['media_id'];
			}
			
			// build one email for each user that want all
			foreach($users_updated_all as $user_id => $user_updated_all)
			{
				// make sure there is an entry into the email array
				$email_address = $user_updated_all['email'];
				if(!isset($emails[$email_address]))
				{
					$emails[$email_address] = array(
						'email' => $email_address,
						'name' => $user_updated_all['name'],
						'log_count_updated' => 0,
						'log_count_new' => 0,
						'log_count_closed' => 0,
						'log_count_deleted' => 0,
						'logs' => array(),
					);
				}
				
				($log['Dblog']['deleted']?$emails[$email_address]['log_count_deleted']++:$emails[$email_address]['log_count_updated']++);
				
				// add this log to the email
				$emails[$email_address]['logs'][$log_id] = array(
					'model' => $log['Dblog']['model'],
					'model_id' => $log['Dblog']['model_id'],
					'user_id' => $log['Dblog']['user_id'],
					'user' => (isset($log['DblogUser']['email'])?$log['DblogUser']['email']:''),
					'media_id' => $media_id,
					'status' => ($log['Dblog']['deleted']?4:2),
					'timestamp' => $log['Dblog']['created'],
					'message' => ($log['Dblog']['mapped_readable']?$log['Dblog']['mapped_readable']:str_replace(';;;', "\n", $log['Dblog']['readable'])),
				);
			}
			
			// build one email for each user that want all
			foreach($users_updated_mentioned as $user_id => $user_updated_mentioned)
			{
				if(!in_array($user_id, $log['user_ids'])) continue;
				
				// make sure there is an entry into the email array
				$email_address = $user_updated_mentioned['email'];
				if(!isset($emails[$email_address]))
				{
					$emails[$email_address] = array(
						'email' => $email_address,
						'name' => $user_updated_mentioned['name'],
						'log_count_updated' => 0,
						'log_count_new' => 0,
						'log_count_closed' => 0,
						'log_count_deleted' => 0,
						'logs' => array(),
					);
				}
				
				($log['Dblog']['deleted']?$emails[$email_address]['log_count_deleted']++:$emails[$email_address]['log_count_updated']++);
				
				// add this log to the email
				$emails[$email_address]['logs'][$log_id] = array(
					'model' => $log['Dblog']['model'],
					'model_id' => $log['Dblog']['model_id'],
					'user_id' => $log['Dblog']['user_id'],
					'user' => (isset($log['DblogUser']['email'])?$log['DblogUser']['email']:''),
					'media_id' => $media_id,
					'status' => ($log['Dblog']['deleted']?4:2),
					'timestamp' => $log['Dblog']['created'],
					'message' => ($log['Dblog']['mapped_readable']?$log['Dblog']['mapped_readable']:str_replace(';;;', "\n", $log['Dblog']['readable'])),
				);
			}
		}
		
		// map the closed log to the users that want an email when one is closed
		foreach($logs_closed as $log_id => $log)
		{
			$media_id = 0;
			if(isset($log['Media']['id']))
			{
				$media_id = $log['Media']['id'];
			}
			else
			{
				$changes = unserialize($log['Dblog']['changes']);
				if(isset($changes['media_id'])) $media_id = $changes['media_id'];
			}
			// build one email for each user that want all
			foreach($users_closed_all as $user_id => $user_closed_all)
			{
				// make sure there is an entry into the email array
				$email_address = $user_closed_all['email'];
				if(!isset($emails[$email_address]))
				{
					$emails[$email_address] = array(
						'email' => $email_address,
						'name' => $user_closed_all['name'],
						'log_count_updated' => 0,
						'log_count_new' => 0,
						'log_count_closed' => 0,
						'log_count_deleted' => 0,
						'logs' => array(),
					);
				}
				
				($log['Dblog']['deleted']?$emails[$email_address]['log_count_deleted']++:$emails[$email_address]['log_count_closed']++);
				
				// add this log to the email
				$emails[$email_address]['logs'][$log_id] = array(
					'model' => $log['Dblog']['model'],
					'model_id' => $log['Dblog']['model_id'],
					'user_id' => $log['Dblog']['user_id'],
					'user' => (isset($log['DblogUser']['email'])?$log['DblogUser']['email']:''),
					'media_id' => $media_id,
					'status' => ($log['Dblog']['deleted']?4:3),
					'timestamp' => $log['Dblog']['created'],
					'message' => ($log['Dblog']['mapped_readable']?$log['Dblog']['mapped_readable']:str_replace(';;;', "\n", $log['Dblog']['readable'])),
				);
			}
			
			// build one email for each user that want all
			foreach($users_closed_mentioned as $user_id => $user_closed_mentioned)
			{
				if(!in_array($user_id, $log['user_ids'])) continue;
				
				// make sure there is an entry into the email array
				$email_address = $user_closed_mentioned['email'];
				if(!isset($emails[$email_address]))
				{
					$emails[$email_address] = array(
						'email' => $email_address,
						'name' => $user_closed_mentioned['name'],
						'log_count_updated' => 0,
						'log_count_new' => 0,
						'log_count_closed' => 0,
						'log_count_deleted' => 0,
						'logs' => array(),
					);
				}
				
				($log['Dblog']['deleted']?$emails[$email_address]['log_count_deleted']++:$emails[$email_address]['log_count_closed']++);
				
				// add this log to the email
				$emails[$email_address]['logs'][$log_id] = array(
					'model' => $log['Dblog']['model'],
					'model_id' => $log['Dblog']['model_id'],
					'user_id' => $log['Dblog']['user_id'],
					'user' => (isset($log['DblogUser']['email'])?$log['DblogUser']['email']:''),
					'media_id' => $media_id,
					'status' => ($log['Dblog']['deleted']?4:3),
					'timestamp' => $log['Dblog']['created'],
					'message' => ($log['Dblog']['mapped_readable']?$log['Dblog']['mapped_readable']:str_replace(';;;', "\n", $log['Dblog']['readable'])),
				);
			}
		}
		
		// this keeps the logs in the proper order by object, then log order (uses key)
		foreach($emails as $email_address => $email_info)
		{
			if(isset($emails[$email_address]['logs']))
			{
				ksort($emails[$email_address]['logs']);
			}
		}
		
		// load the email task
		$Email = $this->Tasks->load('Utilities.Email');
		$Email->set('template', 'change_log');
		
		$message_status_template = "%s - %s";
		$log_status_map = array(
			1 => __('Added'),
			2 => __('Updated'),
			3 => __('Closed'),
			4 => __('Deleted'),
		);
		
		foreach($emails as $email_address => $email_info)
		{
			$subject = __('Changes made. New: %s, Updated: %s, Closed: %s, Deleted: %s',
				$email_info['log_count_new'],
				$email_info['log_count_updated'],
				$email_info['log_count_closed'],
				$email_info['log_count_deleted']
			);
		
			// set the variables so we can use view templates
			$viewVars = array(
				'subject' => $subject,
				'log_status_map' => $log_status_map,
				'logs' => $email_info['logs'],
			);
			
			$Email->set('to', array($email_info['email'] => $email_info['name']));
			$Email->set('subject', $subject); 
			$Email->set('viewVars', $viewVars);
			
			// send the email
			if(!$results = $Email->executeFull())
			{
				$this->out(__('Unabled to send Email to: %s - Subject: %s', $email_info['email'], $subject), 1, Shell::QUIET);
			}
			else
			{
				$this->out(__('Email sent to: %s - Subject: %s', $email_info['email'], $subject), 1, Shell::QUIET);
			}
		}
	}
	
	public function send_open_media_emails()
	{
	/*
	 * Sends an email on a set day/time for all open media assigned to an org group
	 */
	 	Configure::write('debug', 1);
	 	
		$hour = date('H');
		$day = strtolower(date('D'));
		
		$this->out(__('Finding %ss that needs to have notifications sent. Day: %s - Hour: %s', __('Org Group'), $day , $hour), 1, Shell::QUIET);
		
		/////////// get the list of changes
		$org_groups = $this->OrgGroup->find('all', array(
			'conditions' => array(
				'OrgGroup.sendemail' => true,
				'OrgGroup.'.$day => true,
				'OrgGroup.notify_time' => $hour,
			),
		));
		
		if(!$org_groups)
		{
			$this->out(__('No %s marked for notification at %s.', __('Org Group'), date('g a')), 1, Shell::QUIET);
			return false;
		}
		
		$this->out(__('Found %s %s%s to send at %s.', count($org_groups), __('Org Group'), (count($org_groups)>1?'s':''), date('g a')), 1, Shell::QUIET);
		
		// load the email task
		$Email = $this->Tasks->load('Utilities.Email');
		$Email->set('template', 'open_media_emails');
		
		// Each one gets an email to be sent out
		foreach($org_groups as $org_group)
		{
			$_media = $this->Media->find('all', array(
				'recursive' => 0,
				'conditions' => array(
					'Media.org_group_id' => $org_group['OrgGroup']['id'],
					'Media.state' => 1,
				),
				'order' => array('Media.created' => 'asc'),
			));
			
			if(!$_media)
			{
				$this->out(__('No open %s was found assigned to the %s "%s".', __('Media'), __('Org Group'), $org_group['OrgGroup']['name']), 1, Shell::QUIET);
				continue;
			}
			
			$this->out(__('Found %s open %s assigned to the %s "%s".', count($_media), __('Media'), __('Org Group'), $org_group['OrgGroup']['name']), 1, Shell::QUIET);
			
			// get the email addresses of all of the active users for this org group
			$emails = $this->User->emails(false, true, $org_group['OrgGroup']['id']);
			
			if(!$emails)
			{
				$this->out(__('No active %s were found assigned to the %s "%s".', __('Users'), __('Org Group'), $org_group['OrgGroup']['name']), 1, Shell::QUIET);
				continue;
			}
			
			$this->out(__('Found %s active %s assigned to the %s "%s".', count($emails), __('Users'), __('Org Group'), $org_group['OrgGroup']['name']), 1, Shell::QUIET);
			
			// set the variables so we can use view templates
			$viewVars = array(
				'instructions' => trim($org_group['OrgGroup']['instructions']),
				'org_group' => $org_group,
				'_media' => $_media,
			);
			
			$Email->set('subject', __('Open %s count: %s', __('Media'), count($_media)));
			$Email->set('viewVars', $viewVars);
			//$Email->config_vars(array('log' => true));
			
			foreach($emails as $email)
			{
				$Email->set('to', $email);
				
				// send the email
				if(!$results = $Email->executeFull())
				{
					$this->out(__('Unable to send notification email for %s "%s" to %s.', __('Org Group'), $org_group['OrgGroup']['name'], $email), 1, Shell::QUIET);
				}
				else
				{
					$this->out(__('Sent notification email for %s "%s" to %s.', __('Org Group'), $org_group['OrgGroup']['name'], $email), 1, Shell::QUIET);
				}
			}
		}
	}
}
?>