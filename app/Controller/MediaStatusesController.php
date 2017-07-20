<?php
App::uses('AppController', 'Controller');
/**
 * MediaStatuses Controller
 *
 * @property MediaStatuses $MediaStatus
 */
class MediaStatusesController extends AppController 
{

//
	public function admin_index() 
	{
	/**
	 * index method
	 *
	 * Displays all Media Statuses
	 */
		$this->Prg->commonProcess();
		
/////////////////////////////
		$conditions = array(
		);
		
		$this->MediaStatus->recursive = -1;
		$this->paginate['order'] = array('MediaStatus.name' => 'asc');
		$this->paginate['conditions'] = $this->MediaStatus->conditions($conditions, $this->passedArgs); 
		$this->set('media_statuses', $this->paginate());
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
			$this->MediaStatus->create();
			
			if ($this->MediaStatus->saveAssociated($this->request->data))
			{
				$this->Session->setFlash(__('The Media Status has been saved'));
				return $this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Session->setFlash(__('The Media Status could not be saved. Please, try again.'));
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
		$this->MediaStatus->id = $id;
		if (!$this->MediaStatus->exists()) 
		{
			throw new NotFoundException(__('Invalid Media Status'));
		}
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			if ($this->MediaStatus->saveAssociated($this->request->data)) 
			{
				$this->Session->setFlash(__('The Media Status has been saved'));
				return $this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Session->setFlash(__('The Media Status could not be saved. Please, try again.'));
			}
		}
		else
		{
			$this->request->data = $this->MediaStatus->read(null, $id);
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
		$this->MediaStatus->id = $id;
		if (!$this->MediaStatus->exists()) 
		{
			throw new NotFoundException(__('Invalid Media Status'));
		}

		if ($this->MediaStatus->delete()) 
		{
			$this->Session->setFlash(__('Media Status deleted'));
			return $this->redirect($this->referer());
		}
		
		$this->Session->setFlash(__('Media Status was not deleted'));
		return $this->redirect($this->referer());
	}
}
