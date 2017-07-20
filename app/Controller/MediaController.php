<?php
App::uses('AppController', 'Controller');
/**
 * Media Controller
 *
 * @property Media $Media
 */
class MediaController extends AppController 
{

	public function isAuthorized($user = array())
	{
	/*
	 * Checks permissions for a user when trying to access a media
	 */
		// All registered users can add and view media
		if (in_array($this->action, array('add', 'view', 'edit'))) 
		{
			return true;
		}
		
		// only the reviewer can change the state and delete
		if (in_array($this->action, array('toggle', 'delete'))) 
		{
			if(in_array(AuthComponent::user('role'), array('admin', 'reviewer')))
			{
				return true;
			}
		}

		return parent::isAuthorized($user);
	}
	
	public function db_block_overview()
	{
		$_media = $this->Media->find('all');
		$this->set(compact('_media'));
	}
	
	public function db_block_obtain_reasons()
	{
		$obtainReasons = $this->Media->ObtainReason->find('list');
		$_media = $this->Media->find('all', array(
			'contain' => array('ObtainReason'),
		));
		$this->set(compact('_media', 'obtainReasons'));
	}
	
	public function db_block_statuses($state = null)
	{
		$mediaStatuses = $this->Media->MediaStatus->find('list');
		
		$conditions = array();
		if($state !== null)
		{
			$conditions['Media.state'] = $state;
		}
		
		$_media = $this->Media->find('all', array(
			'contain' => array('MediaStatus'),
			'conditions' => $conditions,
		));
		$this->set(compact('_media', 'mediaStatuses', 'state'));
	}
	
	public function db_block_types()
	{
		$mediaTypes = $this->Media->MediaType->find('list');
		$_media = $this->Media->find('all', array(
			'contain' => array('MediaType'),
		));
		$this->set(compact('_media', 'mediaTypes'));
	}

	public function index() 
	{
		$this->Prg->commonProcess();
		
		$conditions = array();
		if(!in_array(AuthComponent::user('role'), array('admin')))
		{
			$conditions['Media.org_group_id'] = AuthComponent::user('org_group_id');
		}
		
		$this->Media->recursive = 0;
		$this->Media->getLatestUpload = true;
		$this->paginate['order'] = array('Media.created' => 'desc');
		$this->paginate['conditions'] = $this->Media->conditions($conditions, $this->passedArgs); 
		$this->set('_media', $this->paginate());
	}

	public function open() 
	{
		$conditions = array('Media.state' => 1);
		if(!in_array(AuthComponent::user('role'), array('admin')))
		{
			$conditions['Media.org_group_id'] = AuthComponent::user('org_group_id');
		}
		
		$this->Media->recursive = 0;
		
		if ($this->request->is('requested')) 
		{
			$_media = $this->Media->find('all', array(
				'recursive' => 0,
				'conditions' => $conditions,
				'contain' => array('MediaDetail'),
			));
			
			// format for the menu_items
			$items = array();
			
			foreach($_media as $media)
			{
				$title = $media['Media']['id']. '-';
				
				$items[] = array(
					'title' => $media['Media']['id']. ' : '. (trim($media['MediaDetail']['ticket_ticket'])?$media['MediaDetail']['ticket_ticket']:__('(Empty)')). ' : '. (trim($media['MediaDetail']['other_ticket'])?$media['MediaDetail']['other_ticket']:__('(Empty)')). ' : '. (trim($media['MediaDetail']['property_tag'])?$media['MediaDetail']['property_tag']:__('(Empty)')),
					'url' => array('controller' => 'media', 'action' => 'view', $media['Media']['id'], 'admin' => false, 'plugin' => false)
				);
			}
			return $items;
		}
		else
		{
			$this->Prg->commonProcess();
			$this->Media->recursive = 0;
			$this->Media->getLatestUpload = true;
			$this->paginate['order'] = array('Media.created' => 'desc');
			$this->paginate['conditions'] = $this->Media->conditions($conditions, $this->passedArgs); 
			$this->set('_media', $this->paginate());
		}
	}

	public function closed() 
	{
		$this->Prg->commonProcess();
		
		$conditions = array('Media.state' => 0);
		if(!in_array(AuthComponent::user('role'), array('admin')))
		{
			$conditions['Media.org_group_id'] = AuthComponent::user('org_group_id');
		}
		
		$this->Media->recursive = 0;
		$this->Media->getLatestUpload = true;
		$this->paginate['order'] = array('Media.created' => 'desc');
		$this->paginate['conditions'] = $this->Media->conditions($conditions, $this->passedArgs); 
		
		$this->set('_media', $this->paginate());
	}

	public function user($user_id = false) 
	{
		$this->Prg->commonProcess();
		
/////////////////////////////
		$conditions = array(
			'OR' => array(
				'Media.added_user_id' => $user_id,
				'Media.modified_user_id' => $user_id,
				'Media.received_user_id' => $user_id,
			),
		);
		
		$this->Media->recursive = 0;
		$this->Media->getLatestUpload = true;
		$this->paginate['order'] = array('Media.created' => 'desc');
		$this->paginate['conditions'] = $this->Media->conditions($conditions, $this->passedArgs); 
		$this->set('_media', $this->paginate());
	}
	
	public function view($id = null) 
	{
		$this->Media->id = $id;
		if (!$this->Media->exists())
		{
			throw new NotFoundException(__('Invalid Media'));
		}
		
		// get the counts
		$this->Media->getCounts = array(
			'Upload' => array(
				'all' => array(
					'conditions' => array(
						'Upload.media_id' => $id,
					),
				),
			),
			'CustodyChain' => array(
				'all' => array(
					'conditions' => array(
						'CustodyChain.media_id' => $id,
					),
				),
			),
		);
		
		$this->Media->recursive = 1;
		$this->Media->contain(array('MediaDetail', 'MediaOpenedUser', 'MediaClosedUser', 'MediaAddedUser', 'MediaModifiedUser', 'MediaStatus', 'MediaReceivedUser', 'ReceivedOrg', 'ObtainReason', 'MediaType', 'OrgGroup'));
		$this->set('media', $this->Media->read(null, $id));
	}

	public function add() 
	{
		if ($this->request->is('post'))
		{
			$this->Media->create();
			$this->request->data['Media']['added_user_id'] = AuthComponent::user('id');
			$this->request->data['Media']['org_group_id'] = AuthComponent::user('org_group_id');
			$this->request->data['MediaDetail']['added_user_id'] = AuthComponent::user('id');
			
			if ($this->Media->saveAssociated($this->request->data))
			{
				$redirect = array('action' => 'view', $this->Media->id);
				if($this->Media->saveRedirect)
				{
					$redirect = $this->Media->saveRedirect;
				}
				$this->Flash->success(__('The %s has been saved', __('Media')));
				$this->bypassReferer = true;
				return $this->redirect($redirect);
			}
			else
			{
				$this->Flash->error(__('The %s could not be saved. Please, try again.', __('Media')));
			}
		}
		
		// get the users
		$this->set('users', $this->Media->MediaReceivedUser->find('list', array('order' => 'MediaReceivedUser.name')));
		// get the media types
		$this->set('media_types', $this->Media->MediaType->find('list', array('order' => 'MediaType.name')));
		
		// get the media statuses
		// only on the edit, default is 0 which is initial
//		$this->set('media_statuses', $this->Media->MediaStatus->find('list', array('order' => 'MediaStatus.name')));
		// get the received_orgs
		$this->set('received_orgs', $this->Media->ReceivedOrg->find('list', array('order' => 'ReceivedOrg.name')));
		// get the obtain_reasons
		$this->set('obtain_reasons', $this->Media->ObtainReason->find('list', array('order' => 'ObtainReason.name')));
		// get the obtain_reasons
		$this->set('custody_chain_reasons', $this->Media->CustodyChain->CustodyChainReason->find('list', array('order' => 'CustodyChainReason.name')));
	}

	public function edit($id = null) 
	{
		$this->Media->id = $id;
		if (!$this->Media->exists())
		{
			throw new NotFoundException(__('Invalid %s', __('Media')));
		}
		
		if ($this->request->is('post') || $this->request->is('put'))
		{
			$this->request->data['Media']['modified_user_id'] = AuthComponent::user('id');
			$this->request->data['MediaDetail']['modified_user_id'] = AuthComponent::user('id');
			
			if ($this->Media->saveAssociated($this->request->data))
			{
				$this->Flash->success(__('The %s has been updated', __('Media')));
				return $this->redirect(array('action' => 'view', $this->Media->id));
			}
			else
			{
				$this->Flash->error(__('The %s could not be saved. Please, try again.', __('Media')));
			}
		}
		else
		{
			if(!$this->Media->field('state'))
			{
				$this->Flash->error(__('The %s can\'t be edited when in a closed state. Have an Admin, or Reviewer open it.', __('Media')));
				// send an email to the users involved, and admins
				$this->Media->sendEditEmail($this->Media->id, AuthComponent::user('id'));
				return $this->redirect(array('action' => 'view', $this->Media->id));
			}
			
			$this->Media->recursive = 1;
			$this->Media->contain(array('MediaDetail', 'MediaAddedUser', 'MediaStatus', 'MediaReceivedUser', 'ReceivedOrg', 'ObtainReason', 'MediaType'));
			$this->request->data = $this->Media->read(null, $this->Media->id);
		}
		
		// get the users
		$this->set('users', $this->Media->MediaReceivedUser->find('list', array('order' => 'MediaReceivedUser.name')));
		// get the media types
		$this->set('media_types', $this->Media->MediaType->find('list', array('order' => 'MediaType.name')));
		// get the media statuses
		$this->set('media_statuses', $this->Media->MediaStatus->find('list', array('order' => 'MediaStatus.name')));
		// get the received_orgs
		$this->set('received_orgs', $this->Media->ReceivedOrg->find('list', array('order' => 'ReceivedOrg.name')));
		// get the obtain_reasons
		$this->set('obtain_reasons', $this->Media->ObtainReason->find('list', array('order' => 'ObtainReason.name')));
		// get the obtain_reasons
		$this->set('custody_chain_reasons', $this->Media->CustodyChain->CustodyChainReason->find('list', array('order' => 'CustodyChainReason.name')));
		// get the obtain_reasons
		$this->set('org_groups', $this->Media->OrgGroup->find('list', array('order' => 'OrgGroup.name')));
	}
	
	public function validate_ticket()
	{
		$this->autoRender = FALSE;
		$this->layout = 'Utilities.ajax_nodebug';
		if($this->RequestHandler->isAjax())
		{
			$ticket_ticket = (isset($this->request->data['value'])?$this->request->data['value']:false);
			
			$id = false;
			if($ticket_ticket)
			{
				$media = $this->Media->find('first', array(
					'conditions' => array(
						'MediaDetail.ticket_ticket' => $ticket_ticket,
					),
					'recursive' => 0,
				));
			}
			if($media)
			{
				$this->autoRender = true;
				$this->set('media', $media);
			}

       }
    }
	
	public function toggle($field = null, $id = null)
	{
	/*
	 * Toggle an object's boolean settings (like active)
	 */
	 	$extra = array();
	 	$flashMsg = 'The %s has been updated.';
	 	if($field == 'state')
	 	{
	 		$value = $this->Media->field($field, array('id' => $id));
	 		// we're closing
	 		if($value === true)
	 		{
	 			$extra = array(
	 				'closed' => date('Y-m-d H:i:s'),
	 				'closed_user_id' => AuthComponent::user('id'),
	 			);
	 			$flashMsg = 'The %s has been closed.';
	 		}
	 		// we're opening
	 		elseif($value === false)
	 		{
	 			$extra = array(
	 				'opened' => date('Y-m-d H:i:s'),
	 				'opened_user_id' => AuthComponent::user('id'),
	 			);
	 			$flashMsg = 'The %s has been opened.';
	 		}
	 	}
		if ($this->Media->toggleRecord($id, $field, $extra))
		{
			$this->Flash->success(__($flashMsg, __('Media')));
		}
		else
		{
			$this->Flash->error($this->Media->modelError);
		}
		
		return $this->redirect($this->referer());
	}

	public function admin_user($user_id = false) 
	{
		$this->Prg->commonProcess();
		
		$conditions = array(
			'OR' => array(
				'Media.added_user_id' => $user_id,
				'Media.modified_user_id' => $user_id,
				'Media.received_user_id' => $user_id,
				'Media.opened_user_id' => $user_id,
				'Media.closed_user_id' => $user_id,
			),
		);
		
		$this->Media->recursive = 0;
		$this->Media->getLatestUpload = true;
		$this->paginate['order'] = array('Media.created' => 'desc');
		$this->paginate['conditions'] = $this->Media->conditions($conditions, $this->passedArgs); 
		$this->set('_media', $this->paginate());
	}

	public function admin_group($id = 0)
	{
		$this->Prg->commonProcess();
		
		$conditions = array('Media.org_group_id' => $id);
		
		$this->Media->recursive = 0;
		$this->paginate['order'] = array('Media.created' => 'asc');
		$this->paginate['conditions'] = $this->Media->conditions($conditions, $this->passedArgs); 
		$this->set('_media', $this->paginate());
	}

	public function admin_delete($id = null) 
	{
		$this->Media->id = $id;
		if (!$this->Media->exists()) 
		{
			throw new NotFoundException(__('Invalid %s', __('Media')));
		}

		if ($this->Media->delete()) 
		{
			$this->Flash->success(__('%s Deleted', __('Media')));
			return $this->redirect(array('controller' => 'media', 'action' => 'index', 'admin' => false));
		}
		
		$this->Flash->error(__('%s was not deleted', __('Media')));
		return $this->redirect($this->referer());
	}
}