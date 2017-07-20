<?php
App::uses('AppModel', 'Model');
/**
 * Keyword Model
 *
 */
class Keyword extends AppModel 
{
/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'keyword';
/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'keyword' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'required' => true,
			),
		),
	);
	
	
	public $hasAndBelongsToMany = array(
		'Upload' => array(
			'className' => 'Upload',
			'joinTable' => 'uploads_keywords',
			'foreignKey' => 'upload_id',
			'associationForeignKey' => 'keyword_id',
			'unique' => 'keepExisting',
			'with' => 'UploadsKeyword',
		),
	);
	
	// define the fields that can be searched
	public $searchFields = array(
		'Keyword.keyword',
	);
	
	// track the ids of the items that are added
	public $saveManyIds = array();
	
	public function saveMany($data = NULL, $options = array())
	{
	/*
	 * Filter out the keywords that already exist based on the name column
	 */
	 	$return = false;
	 	
	 	// reset the ids array
	 	$this->saveManyIds = array();
	 	
	 	if($data)
	 	{
	 		$data = Set::flatten($data);
	 		
	 		//
	 		$existing = $this->find('list', array(
				'fields' => array('Keyword.keyword', 'Keyword.id'),
				'conditions' => array('Keyword.keyword' => $data),
			));
			
			// some do exist, filter them out
			if($existing)
			{
				// track the existing keyword_ids
				$this->saveManyIds = $existing;
				// get just the new ones
				$data = array_diff($data, array_keys($existing));
			}
			
			// some are still new, unflatten the array
			if($data)
			{
				$_data = array();
				foreach ($data as $key => $value) 
				{
					$_data = Set::insert($_data, $key, $value);
				}
				
				$return = parent::saveMany($_data, $options);
				
				unset($_data);
				
				// get the ids of the new records
				if($return)
				{
					$new = $this->find('list', array(
						'fields' => array('Keyword.keyword', 'Keyword.id'),
						'conditions' => array('Keyword.keyword' => $data),
					));
					if($new)
					{
						// add the new ids to the id tracking
						$this->saveManyIds = array_merge($this->saveManyIds, $new);
					}
				}
			}
	 	}
		return $return;
	}
}
