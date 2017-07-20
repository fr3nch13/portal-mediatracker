<?php
App::uses('AppController', 'Controller');
/**
 * CustodyChains Controller
 *
 * @property CustodyChains $CustodyChain
 */
class CustodyChainsController extends AppController 
{
	public function media($media_id = false) 
	{
	/**
	 * index method
	 *
	 * Displays all Custody Chains
	 */
		$this->Prg->commonProcess();
		
/////////////////////////////
		$conditions = array(
			'CustodyChain.media_id' => $media_id,
		);
		
//		$this->CustodyChain->clearCache();
		$this->CustodyChain->recursive = 0;
		$this->CustodyChain->contain('ChainAddedUser', 'ChainReleasedUser', 'ChainReceivedUser', 'CustodyChainReason', 'Upload');
		$this->paginate['order'] = array('CustodyChain.created' => 'desc');
		$this->paginate['conditions'] = $this->CustodyChain->conditions($conditions, $this->passedArgs); 
		$custody_chains = $this->paginate();
		$this->set('custody_chains', $custody_chains);
	}
	
	public function user($user_id = false) 
	{
	/**
	 * index method
	 *
	 * Displays all Custody Chains for a user
	 */
		$this->Prg->commonProcess();
		
/////////////////////////////
		$conditions = array(
			'OR' => array(
				'CustodyChain.added_user_id' => $user_id,
				'CustodyChain.received_user_id' => $user_id,
				'CustodyChain.released_user_id' => $user_id,
			),
		);
		
		$this->CustodyChain->recursive = 0;
		$this->CustodyChain->contain('ChainAddedUser', 'ChainReleasedUser', 'ChainReceivedUser', 'CustodyChainReason', 'Upload', 'Media');
		$this->paginate['order'] = array('CustodyChain.created' => 'desc');
		$this->paginate['conditions'] = $this->CustodyChain->conditions($conditions, $this->passedArgs); 
		$this->set('custody_chains', $this->paginate());
	}
	
	public function add($media_id = false) 
	{
		if (!$media_id) 
		{
			throw new NotFoundException(__('Invalid Media'));
		}
		
		if (!$media = $this->CustodyChain->Media->find('first', array(
			'conditions' => array('Media.id' => $media_id),
			'recursive' => 0,
			'contain' => array('MediaDetail'),
		))) 
		{
			throw new NotFoundException(__('Invalid Media'));
		}
		
		$this->CustodyChain->Media->id = $media_id;
			
		if ($this->request->is('post'))
		{
			$this->CustodyChain->create();
			// track who added the chain of custody
			$this->request->data['CustodyChain']['added_user_id'] = AuthComponent::user('id');
			// track on the media who 'modified' the media, by adding this coc
			$this->request->data['Media']['modified_user_id'] = AuthComponent::user('id');
			
			if ($this->CustodyChain->saveAssociated($this->request->data))
			{
				$redirect = array('controller' => 'media', 'action' => 'index');
				if(isset($this->request->data['CustodyChain']['media_id']))
				{
					$redirect = array('controller' => 'media', 'action' => 'view', $this->request->data['CustodyChain']['media_id']);					
				}
				
				$this->Session->setFlash(__('The Custody Chain has been saved'));
				return $this->redirect($redirect);
			}
			else
			{
				$this->Session->setFlash(__('The Custody Chain could not be saved. Please, try again.'));
			}
		}
		else
		{
			$last_chain = $this->CustodyChain->find('first', array(
				'conditions' => array('CustodyChain.media_id' => $media_id),
				'order' => array('CustodyChain.created' => 'desc'),
			));
			
			$this->request->data = array(
				'CustodyChain' => array(
					'media_id' => $media_id,
					'released_user_id' => (isset($last_chain['CustodyChain']['received_user_id'])?$last_chain['CustodyChain']['received_user_id']:0),
					'released_user_other' => (isset($last_chain['CustodyChain']['received_user_other'])?$last_chain['CustodyChain']['received_user_other']:''),
					'received_user_id' => AuthComponent::user('id'),
				),
				'Upload' => array('media_id' => $media_id),
				'Media' => array('id' => $media_id, 'media_status_id' => $media['Media']['media_status_id']),
			);
			
			$this->set('last_chain', $last_chain);
		}
		
		// get the media
		$this->set('media', $media);
		
		// get the obtain_reasons
		$this->set('custody_chain_reasons', $this->CustodyChain->CustodyChainReason->find('list', array('order' => 'CustodyChainReason.name')));
		
		// get a list of users
		$this->set('users', $this->CustodyChain->ChainReleasedUser->find('list', array('order' => 'ChainReleasedUser.name')));
		
		// get the media statuses
		$this->set('media_statuses', $this->CustodyChain->Media->MediaStatus->find('list', array('order' => 'MediaStatus.name')));
	}

//
	public function delete($id = null) 
	{
	/**
	 * delete method
	 *
	 * @param string $id
	 * @return void
	 */
		$this->CustodyChain->id = $id;
		if (!$this->CustodyChain->exists()) 
		{
			throw new NotFoundException(__('Invalid Custody Chain'));
		}

		if ($this->CustodyChain->delete()) 
		{
			$this->Session->setFlash(__('Custody Chain deleted'));
			return $this->redirect($this->referer());
		}
		
		$this->Session->setFlash(__('Custody Chain was not deleted'));
		return $this->redirect($this->referer());
	}
	
	
	public function admin_user($user_id = false) 
	{
	/**
	 * index method
	 *
	 * Displays all Custody Chains for a user
	 */
		$this->Prg->commonProcess();
		
/////////////////////////////
		$conditions = array(
			'OR' => array(
				'CustodyChain.added_user_id' => $user_id,
				'CustodyChain.received_user_id' => $user_id,
				'CustodyChain.released_user_id' => $user_id,
			),
		);
		
		$this->CustodyChain->recursive = 0;
		$this->CustodyChain->contain('ChainAddedUser', 'ChainReleasedUser', 'ChainReceivedUser', 'CustodyChainReason', 'Upload', 'Media');
		$this->paginate['order'] = array('CustodyChain.created' => 'desc');
		$this->paginate['conditions'] = $this->CustodyChain->conditions($conditions, $this->passedArgs); 
		$this->set('custody_chains', $this->paginate());
	}
}