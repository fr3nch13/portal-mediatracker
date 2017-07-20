<?php
App::uses('AppController', 'Controller');
/**
 * ObtainReasons Controller
 *
 * @property ObtainReasons $ObtainReason
 */
class ObtainReasonsController extends AppController 
{

//
	public function admin_index() 
	{
	/**
	 * index method
	 *
	 * Displays all Obtain Reasons
	 */
		$this->Prg->commonProcess();
		
/////////////////////////////
		$conditions = array(
		);
		
		$this->ObtainReason->recursive = -1;
		$this->paginate['order'] = array('ObtainReason.name' => 'asc');
		$this->paginate['conditions'] = $this->ObtainReason->conditions($conditions, $this->passedArgs); 
		$this->set('obtain_reasons', $this->paginate());
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
			$this->ObtainReason->create();
			
			if ($this->ObtainReason->saveAssociated($this->request->data))
			{
				$this->Session->setFlash(__('The Obtain Reason has been saved'));
				return $this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Session->setFlash(__('The Obtain Reason could not be saved. Please, try again.'));
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
		$this->ObtainReason->id = $id;
		if (!$this->ObtainReason->exists()) 
		{
			throw new NotFoundException(__('Invalid Obtain Reason'));
		}
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			if ($this->ObtainReason->saveAssociated($this->request->data)) 
			{
				$this->Session->setFlash(__('The Obtain Reason has been saved'));
				return $this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Session->setFlash(__('The Obtain Reason could not be saved. Please, try again.'));
			}
		}
		else
		{
			$this->request->data = $this->ObtainReason->read(null, $id);
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
	 
		$this->ObtainReason->id = $id;
		if (!$this->ObtainReason->exists()) 
		{
			throw new NotFoundException(__('Invalid Obtain Reason'));
		}

		if ($this->ObtainReason->delete()) 
		{
			$this->Session->setFlash(__('Obtain Reason deleted'));
			return $this->redirect($this->referer());
		}
		
		$this->Session->setFlash(__('Obtain Reason was not deleted'));
		return $this->redirect($this->referer());
	}
}
