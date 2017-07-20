<?php
App::uses('AppController', 'Controller');
/**
 * ReceivedOrgs Controller
 *
 * @property ReceivedOrgs $ReceivedOrg
 */
class ReceivedOrgsController extends AppController 
{

//
	public function admin_index() 
	{
	/**
	 * index method
	 *
	 * Displays all Received Orgs
	 */
		$this->Prg->commonProcess();
		
/////////////////////////////
		$conditions = array(
		);
		
		$this->ReceivedOrg->recursive = -1;
		$this->paginate['order'] = array('ReceivedOrg.name' => 'asc');
		$this->paginate['conditions'] = $this->ReceivedOrg->conditions($conditions, $this->passedArgs); 
		$this->set('received_orgs', $this->paginate());
	}
	
	public function admin_add() 
	{
	/**
	 * add method
	 *
	 * @return void
	 */
		if ($this->request->is('post'))
		{
			$this->ReceivedOrg->create();
			
			if ($this->ReceivedOrg->saveAssociated($this->request->data))
			{
				$this->Session->setFlash(__('The Received Org has been saved'));
				return $this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Session->setFlash(__('The Received Org could not be saved. Please, try again.'));
			}
		}
	}
	
	public function admin_edit($id = null) 
	{
	/**
	 * edit method
	 *
	 * @param string $id
	 * @return void
	 */
		$this->ReceivedOrg->id = $id;
		if (!$this->ReceivedOrg->exists()) 
		{
			throw new NotFoundException(__('Invalid Received Org'));
		}
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			if ($this->ReceivedOrg->saveAssociated($this->request->data)) 
			{
				$this->Session->setFlash(__('The Received Org has been saved'));
				return $this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Session->setFlash(__('The Received Org could not be saved. Please, try again.'));
			}
		}
		else
		{
			$this->request->data = $this->ReceivedOrg->read(null, $id);
		}
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
	 
		$this->ReceivedOrg->id = $id;
		if (!$this->ReceivedOrg->exists()) 
		{
			throw new NotFoundException(__('Invalid Received Org'));
		}

		if ($this->ReceivedOrg->delete()) 
		{
			$this->Session->setFlash(__('Received Org deleted'));
			return $this->redirect($this->referer());
		}
		
		$this->Session->setFlash(__('Received Org was not deleted'));
		return $this->redirect($this->referer());
	}
}
