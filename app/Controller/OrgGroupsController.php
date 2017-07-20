<?php
App::uses('AppController', 'Controller');

class OrgGroupsController extends AppController 
{
	public function admin_index() 
	{
		$this->Prg->commonProcess();
		
		$conditions = array(
		);
		
		$this->OrgGroup->recursive = -1;
		$this->paginate['order'] = array('OrgGroup.name' => 'asc');
		$this->paginate['conditions'] = $this->OrgGroup->conditions($conditions, $this->passedArgs); 
		$this->set('org_groups', $this->paginate());
	}

	public function admin_view($id = 0)
	{
		$this->OrgGroup->recursive = 0;
		$this->set('org_group', $this->OrgGroup->read(null, $id));
	}
	
	public function admin_add() 
	{
	}
	
	public function admin_edit($id = null) 
	{
	}
	
	public function admin_email_options($id = null) 
	{
		$this->OrgGroup->id = $id;
		if (!$this->OrgGroup->exists()) 
		{
			throw new NotFoundException(__('Invalid Org Group'));
		}
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			if ($this->OrgGroup->saveAssociated($this->request->data)) 
			{
				$this->Session->setFlash(__('The email options for the Org Group has been updated.'));
				return $this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Session->setFlash(__('The email options for the Org Group could not be updated. Please, try again.'));
			}
		}
		else
		{
			$this->request->data = $this->OrgGroup->read(null, $id);
		}
	}
}
