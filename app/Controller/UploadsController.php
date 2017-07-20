<?php
App::uses('AppController', 'Controller');
/**
 * Uploads Controller
 *
 * @property Upload $Upload
 */
class UploadsController extends AppController 
{

	public function isAuthorized($user = array())
	{
	/*
	 * Checks permissions for a user when trying to access a upload
	 */
		// All registered users can add, view, and download
		if (in_array($this->action, array('add', 'view', 'download')))
		{
			return true;
		}
		
		// The owner of a upload can view, edit and delete it
		if (in_array($this->action, array('edit', 'delete'))) 
		{
			$uploadId = $this->request->params['pass'][0];
			if ($this->Upload->isOwnedBy($uploadId, AuthComponent::user('id')))
			{
				return true;
			}
		}
		
		// The owner of a upload can view, edit and delete it
		if (in_array($this->action, array('toggle'))) 
		{
			$uploadId = $this->request->params['pass'][1];
			if ($this->Upload->isOwnedBy($uploadId, AuthComponent::user('id')))
			{
				return true;
			}
		}

		return parent::isAuthorized($user);
	}
	
//
	public function index() 
	{
	/**
	 * index method
	 *
	 * Displays all files
	 */
		
		$this->Prg->commonProcess();
		
/////////////////////////////
		$conditions = array();
		if(AuthComponent::user('role') != 'admin')
		{
			$conditions['Upload.org_group_id'] = AuthComponent::user('org_group_id');
		}
		
		
//		$this->Upload->clearCache();
		$this->Upload->recursive = 0;
		$this->paginate['order'] = array('Upload.created' => 'desc');
		$this->paginate['conditions'] = $this->Upload->conditions($conditions, $this->passedArgs); 
		$this->set('uploads', $this->paginate());
	}

//
	public function mine() 
	{
	/**
	 * index method
	 *
	 * Displays my uploads
	 */
		
		$this->Prg->commonProcess();
		
		$conditions = array('Upload.user_id' => AuthComponent::user('id'));
		
//		$this->Upload->clearCache();
		$this->Upload->recursive = 0;
		$this->paginate['order'] = array('Upload.created' => 'desc');
		$this->paginate['conditions'] = $this->Upload->conditions($conditions, $this->passedArgs); 
		$this->set('uploads', $this->paginate());
	}
	
//
	public function user($user_id = null) 
	{
	/**
	 * index method
	 *
	 * Displays a user's uploads
	 */
		if (!$user_id) {
			throw new NotFoundException(__('Invalid user'));
		}
		
		$this->Prg->commonProcess();
		
		$conditions = array(
			'Upload.user_id' => $user_id,
		);
		
//		$this->Upload->clearCache();
		$this->Upload->recursive = 0;
		$this->paginate['order'] = array('Upload.created' => 'desc');
		$this->paginate['conditions'] = $this->Upload->conditions($conditions, $this->passedArgs); 
		$this->set('uploads', $this->paginate());
	}
	
//
	public function media($media_id = null) 
	{
	/**
	 * index method
	 *
	 * Displays a user's uploads
	 */
		if (!$media_id) {
			throw new NotFoundException(__('Invalid Media'));
		}
		
		$this->Prg->commonProcess();
		
		$conditions = array(
			'Upload.media_id' => $media_id,
		);
		
//		$this->Upload->clearCache();
		$this->Upload->recursive = 0;
		$this->Upload->contain('User', 'CustodyChain');
		$this->paginate['order'] = array('Upload.created' => 'desc');
		$this->paginate['conditions'] = $this->Upload->conditions($conditions, $this->passedArgs); 
		$this->set('uploads', $this->paginate());
	}
	
//
	public function view($id = null) 
	{
	/**
	 * view method
	 *
	 * @param string $id
	 * @return void
	 */
		$this->Upload->id = $id;
		if (!$this->Upload->exists())
		{
			throw new NotFoundException(__('Invalid File'));
		}
		
		// get the counts
		$this->Upload->getCounts = array();
		
//		$this->Upload->clearCache();
		$this->Upload->recursive = 0;
		$this->Upload->contain('User', 'Media', 'CustodyChain');
		$this->set('upload', $this->Upload->read(null, $id));
	}

//
	public function download($id = false, $modelClass = false, $filename = false) 
	{
	/**
	 * download method
	 *
	 * @param string $id
	 * @return void
	 */
		$this->Upload->id = $id;
		if (!$this->Upload->exists()) 
		{
			throw new NotFoundException(__('Invalid File'));
		}
		
		$upload = $this->Upload->read(null, $id);
		
		$paths = $this->Upload->paths($id);
		
        $params = array(
            'download' => true,
            'id' => $upload['Upload']['filename'],
            'path'  => $paths['sys'],
			'mimeType' => array(
				$upload['Upload']['type'] => $upload['Upload']['mimetype'],
			),
        );
		$this->viewClass = 'Media';
        $this->set($params);
	}

//
	public function add($media_id = 0, $custody_chain_id = 0) 
	{
	/**
	 * add method
	 *
	 * @return void
	 */
		if ($this->request->is('post'))
		{
			$this->Upload->create();
			$this->request->data['Upload']['user_id'] = AuthComponent::user('id');
			$this->request->data['Upload']['org_group_id'] = AuthComponent::user('org_group_id');
			
			if ($this->Upload->saveAssociated($this->request->data))
			{
				$redirect = array('action' => 'view', $this->Upload->id);
				
				if($this->data['Upload']['media_id'])
				{
					$redirect = array('controller' => 'media', 'action' => 'view', $this->data['Upload']['media_id']);
				}
				
				$this->Session->setFlash(__('The file(s) has been saved'));
				return $this->redirect($redirect);
			}
			else
			{
				$this->Session->setFlash(__('The file could not be saved. Please, try again.'));
			}
		}
		else
		{
			$this->request->data = array(
				'Upload' => array(
					'media_id' => $media_id,
					'custody_chain_id' => $custody_chain_id,
				)
			);
		}
		
		$media = false;
		if($media_id)
		{
			$media = $this->Upload->Media->find('first', array(
				'conditions' => array('Media.id' => $media_id),
				'recursive' => 0,
				'contain' => array('MediaDetail'),
			));
		}
		$this->set('media', $media);
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
		$this->Upload->id = $id;
		if (!$this->Upload->exists()) 
		{
			throw new NotFoundException(__('Invalid File'));
		}

		if ($this->Upload->delete()) 
		{
			$this->Session->setFlash(__('File deleted'));
			return $this->redirect($this->referer());
		}
		
		$this->Session->setFlash(__('File was not deleted'));
		return $this->redirect($this->referer());
	}
	
/*** Admin Functions ***/


	public function admin_index() 
	{
	/**
	 * index method
	 *
	 * Displays all public Categories
	 */
		
		$this->Prg->commonProcess();
		
		$conditions = array();
		
		$this->Upload->recursive = 0;
		
		$this->paginate['order'] = array('Upload.created' => 'desc');
		$this->paginate['conditions'] = $this->Upload->conditions($conditions, $this->passedArgs); 
		$this->set('uploads', $this->paginate());
	}
	
//
	public function admin_user($user_id = null) 
	{
	/**
	 * index method
	 *
	 * Displays a report's uploads
	 */
		if (!$user_id) {
			throw new NotFoundException(__('Invalid user'));
		}
		
		$this->Prg->commonProcess();
		
		$conditions = array(
			'Upload.user_id' => $user_id,
		);
		
		$this->Upload->recursive = 0;
		$this->paginate['order'] = array('Upload.created' => 'desc');
		$this->paginate['conditions'] = $this->Upload->conditions($conditions, $this->passedArgs); 
		$this->set('uploads', $this->paginate());
	}

//
	public function admin_group($id = 0)
	{
	/*
	 * List of Users in the whole system filtered by group. 
	 * Used to manage Users
	 */
	 
		$this->Prg->commonProcess();
		
		$conditions = array('Upload.org_group_id' => $id);
		
		$this->Upload->recursive = 0;
		$this->paginate['order'] = array('Upload.created' => 'desc');
		$this->paginate['conditions'] = $this->Upload->conditions($conditions, $this->passedArgs); 
		$this->set('uploads', $this->paginate());
	}
	
//
	public function admin_view($id = null) 
	{
	/**
	 * view method
	 *
	 * @param string $id
	 * @return void
	 */
		$this->Upload->id = $id;
		if (!$this->Upload->exists())
		{
			throw new NotFoundException(__('Invalid File'));
		}
		
		// get the counts
		$this->Upload->getCounts = array(
		);
		
//		$this->Upload->clearCache();
		$this->Upload->recursive = 0;
		$this->Upload->contain('User', 'Media', 'CustodyChain');
		$this->set('upload', $this->Upload->read(null, $id));
	}

//
	public function admin_download($id = false, $modelClass = false) 
	{
	/**
	 * download method
	 *
	 * @param string $id
	 * @return void
	 */
		$this->Upload->id = $id;
		if (!$this->Upload->exists()) 
		{
			throw new NotFoundException(__('Invalid File'));
		}
		
		$upload = $this->Upload->read(null, $id);
		
		$paths = $this->Upload->paths($id);
		
        $params = array(
            'download' => true,
            'id' => $upload['Upload']['filename'],
            'path'  => $paths['sys'],
			'mimeType' => array(
				$upload['Upload']['type'] => $upload['Upload']['mimetype'],
			),
        );
		$this->viewClass = 'Media';
        $this->set($params);
	}

//
	public function admin_delete($id = null) 
	{
	/**
	 * delete method
	 *
	 * @param string $id
	 * @return void
	 */
		if (!$this->request->is('post')) 
		{
			throw new MethodNotAllowedException();
		}
		$this->Upload->id = $id;

		if (!$this->Upload->exists()) 
		{
			throw new NotFoundException(__('Invalid File'));
		}

		if ($this->Upload->delete()) 
		{
			$this->Session->setFlash(__('File deleted'));
			return $this->redirect($this->referer());
		}
		
		$this->Session->setFlash(__('File was not deleted'));
		return $this->redirect($this->referer());
	}
}
