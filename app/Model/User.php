<?php
// app/Model/User.php

App::uses('AppModel', 'Model');

class User extends AppModel
{
	public $name = 'User';
	
	public $displayField = 'name';
	
	public $validate = array(
		'email' => array(
			'required' => array(
				'rule' => array('email'),
				'message' => 'A valid email adress is required',
			)
		),
		'role' => array(
			'valid' => array(
				'rule' => array('notBlank'),
				'message' => 'Please enter a valid role',
				'allowEmpty' => false,
			),
		),
	);
	
	public $hasOne = array(
		'UsersSetting' => array(
			'className' => 'UsersSetting',
			'foreignKey' => 'user_id',
		)
	);
	
	public $hasMany = array(
		'Upload' => array(
			'className' => 'Upload',
			'foreignKey' => 'user_id',
			'dependent' => true,
		),
		'LoginHistory' => array(
			'className' => 'LoginHistory',
			'foreignKey' => 'user_id',
			'dependent' => true,
		),
		'MediaAddedUser' => array(
			'className' => 'Media',
			'foreignKey' => 'added_user_id',
			'dependent' => false,
		),
		'MediaModifiedUser' => array(
			'className' => 'Media',
			'foreignKey' => 'modified_user_id',
			'dependent' => false,
		),
		'MediaReceivedUser' => array(
			'className' => 'Media',
			'foreignKey' => 'received_user_id',
			'dependent' => false,
		),
		'MediaClosedUser' => array(
			'className' => 'Media',
			'foreignKey' => 'closed_user_id',
			'dependent' => false,
		),
		'MediaOpenedUser' => array(
			'className' => 'Media',
			'foreignKey' => 'opened_user_id',
			'dependent' => false,
		),
		'ChainAddedUser' => array(
			'className' => 'CustodyChain',
			'foreignKey' => 'added_user_id',
			'dependent' => false,
		),
		'ChainReceivedUser' => array(
			'className' => 'CustodyChain',
			'foreignKey' => 'received_user_id',
			'dependent' => false,
		),
		'ChainReleasedUser' => array(
			'className' => 'CustodyChain',
			'foreignKey' => 'released_user_id',
			'dependent' => false,
		),
	);
	
	public $belongsTo = array(
		'OrgGroup' => array(
			'className' => 'OrgGroup',
			'foreignKey' => 'org_group_id',
		),
	);
	
	public $actsAs = array(
		// log all changes to the database
		'Snapshot.Stat' => array(
			'entities' => array(
				'all' => array(),
				'created' => array(),
				'modified' => array(),
				'active' => array(
					'conditions' => array(
						'User.active' => true,
					),
				),
			),
		),
    );
	
	// define the fields that can be searched
	public $searchFields = array(
		'User.name',
		'User.email',
	);
	
	// fields that are boolean and can be toggled
	public $toggleFields = array('active');
	
	// the path to the config file.
	public $configPath = false;
	
	// Any error relating to the config
	public $configError = false;
	
	// used to store info, because the photo name is changed.
	public $afterdata = false;
	
	public function beforeSave($options = array())
	{
		// hash the password before saving it to the database
		if (isset($this->data[$this->alias]['password']))
		{
			$this->data[$this->alias]['password'] = AuthComponent::password($this->data[$this->alias]['password']);
		}
		return parent::beforeSave($options);
	}
	
	public function afterSave($created = false, $options = array())
	{
		// existing users
		if(!$created)
		{
			// update the org items for Upload MediaAddedUser
			if(isset($this->data['User']['org_group_id']) and $this->data['User']['org_group_id'] > 0)
			{
				$this->Upload->updateAll(
					array('Upload.org_group_id' => $this->data['User']['org_group_id']),
					array('Upload.user_id' => $this->id)
				);
				$this->MediaAddedUser->updateAll(
					array('MediaAddedUser.org_group_id' => $this->data['User']['org_group_id']),
					array('MediaAddedUser.added_user_id' => $this->id)
				);
			}
		}
		$this->afterdata = $this->data;
		
		return parent::afterSave($created, $options);
	}
	
	public function loginAttempt($input = false, $success = false, $user_id = false)
	{
	/*
	 * Once a user is logged in, tack it in the database
	 */
		$email = false;
		if(isset($input['User']['email'])) 
		{
			$email = $input['User']['email'];
			if(!$user_id)
			{
				$user_id = $this->field('id', array('email' => $email));
			}
		}
		
		$data = array(
			'email' => $email,
			'user_agent' => env('HTTP_USER_AGENT'),
			'ipaddress' => env('REMOTE_ADDR'),
			'success' => $success,
			'user_id' => $user_id,
			'timestamp' => date('Y-m-d H:i:s'),
		);
		
		$this->LoginHistory->create();
		return $this->LoginHistory->save($data);
	}
	
	public function lastLogin($user_id = null)
	{
		if($user_id)
		{
			$this->id = $user_id;
			return $this->saveField('lastlogin', date('Y-m-d H:i:s'));
		}
		return false;
	}
	
	public function adminEmails()
	{
	/*
	 * Returns a list of emails address for admin users
	 */
		
		return $this->emails('admin', true);
	}
	
	public function reviewerEmails($org_group_id = false)
	{
	/*
	 * Returns a list of emails address for admin users
	 */
		
		return $this->emails('reviewer', true, $org_group_id);
	}
	
	public function emails($role = false, $active = true, $org_group_id = false)
	{
		$conditions = array(
			'active' => $active,
		);
		
		if($role)
		{
			$conditions['role'] = $role;
		}
		
		if($org_group_id)
		{
			$conditions['org_group_id'] = $org_group_id;
		}
		
		return $this->find('list', array(
			'recursive' => -1,
			'conditions' => $conditions,
			'fields' => array(
				'email',
			),
		));
	}
	
	public function userList($user_ids = array(), $recursive = 0)
	{
	/*
	 * Lists users out with the keys being the user_id
	 */
		// fill the user cache
		$_users = $this->find('all', array(
			'recursive' => $recursive,
			'conditions' => array(
				'User.id' => $user_ids,
			),
		));
		
		$users = array();
		
		foreach($_users as $user)
		{
			$user_id = $user['User']['id'];
			$users[$user_id] = $user; 
		}
		unset($_users);
		return $users;
	}
	
	public function changeLogList($user_ids = array(), $recursive = 0)
	{
	/*
	 * Lists users out with the keys being the user_id, and user email settings set to 2, for the change_log cron job
	 */
		// fill the user cache
		$_users = $this->find('all', array(
			'recursive' => $recursive,
			'conditions' => array(
				'or' => array(
					'User.id' => $user_ids,
					'UsersSetting.email_new' => 2,
					'UsersSetting.email_updated' => 2,
					'UsersSetting.email_closed' => 2,
				),
			),
		));

			$users = array();

			foreach($_users as $user)
			{
				$user_id = $user['User']['id'];
				$users[$user_id] = $user;
			}
			unset($_users);
			return $users;
	}
}

