<?php
App::uses('AppController', 'Controller');
/**
 * CustodyChainReasons Controller
 *
 * @property CustodyChainReasons $CustodyChainReason
 */
class CustodyChainReasonsController extends AppController 
{

//
	public function admin_index() 
	{
	/**
	 * index method
	 *
	 * Displays all Custody Chain Reasons
	 */
		$this->Prg->commonProcess();
		
/////////////////////////////
		$conditions = array(
		);
		
		$this->CustodyChainReason->recursive = -1;
		$this->paginate['order'] = array('CustodyChainReason.name' => 'asc');
		$this->paginate['conditions'] = $this->CustodyChainReason->conditions($conditions, $this->passedArgs); 
		$this->set('custody_chain_reasons', $this->paginate());
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
			$this->CustodyChainReason->create();
			
			if ($this->CustodyChainReason->saveAssociated($this->request->data))
			{
				$this->Session->setFlash(__('The Custody Chain Reason has been saved'));
				return $this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Session->setFlash(__('The Custody Chain Reason could not be saved. Please, try again.'));
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
		$this->CustodyChainReason->id = $id;
		if (!$this->CustodyChainReason->exists()) 
		{
			throw new NotFoundException(__('Invalid Custody Chain Reason'));
		}
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			if ($this->CustodyChainReason->saveAssociated($this->request->data)) 
			{
				$this->Session->setFlash(__('The Custody Chain Reason has been saved'));
				return $this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Session->setFlash(__('The Custody Chain Reason could not be saved. Please, try again.'));
			}
		}
		else
		{
			$this->request->data = $this->CustodyChainReason->read(null, $id);
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
	 
		$this->CustodyChainReason->id = $id;
		if (!$this->CustodyChainReason->exists()) 
		{
			throw new NotFoundException(__('Invalid Custody Chain Reason'));
		}

		if ($this->CustodyChainReason->delete()) 
		{
			$this->Session->setFlash(__('Custody Chain Reason deleted'));
			return $this->redirect($this->referer());
		}
		
		$this->Session->setFlash(__('Custody Chain Reason was not deleted'));
		return $this->redirect($this->referer());
	}
}
