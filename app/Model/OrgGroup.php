<?php
App::uses('AppModel', 'Model');
/**
 * OrgGroup Model
 *
 * @property User $User
 */
class OrgGroup extends AppModel 
{

	public $displayField = 'name';
	
	public $validate = array(
		'name' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
			),
		),
	);
	
	public $hasMany = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'org_group_id',
			'dependent' => false,
		),
		'Media' => array(
			'className' => 'Media',
			'foreignKey' => 'org_group_id',
			'dependent' => false,
		)
	);
	
	public function beforeDelete($cascade = true)
	{
		// set the org_group_id for the has many to 0
		foreach($this->hasMany as $model => $info)
		{
			$this->{$model}->updateAll(
				array($model. '.'. $info['foreignKey'] => 0),
				array($model. '.'. $info['foreignKey'] => $this->id)
			);
		}
		return parent::beforeDelete($cascade = true);
	}
	
	public function read($fields = null, $id = null)
	{
		if($id == 0)
		{
			return $this->Common_readGlobalObject();
		}
		return parent::read($fields, $id);
	}
}
