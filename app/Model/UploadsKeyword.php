<?php
App::uses('AppModel', 'Model');
/**
 * UploadsKeyword Model
 *
 * @property Upload $Upload
 * @property Keyword $Keyword
 */
class UploadsKeyword extends AppModel {
/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'upload_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
			),
		),
		'keyword_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
			),
		),
		'active' => array(
			'boolean' => array(
				'rule' => array('boolean'),
			),
		),
	);

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Upload' => array(
			'className' => 'Upload',
			'foreignKey' => 'upload_id',
		),
		'Keyword' => array(
			'className' => 'Keyword',
			'foreignKey' => 'keyword_id',
		)
	);
	
	// define the fields that can be searched
	public $searchFields = array(
		'Keyword.keyword',
		'Upload.filename',
	);
	
	// valid actions to take against multiselect items
	public $multiselectOptions = array('delete', 'active', 'inactive');
	
	public function saveAssociations($upload_id = false, $keyword_ids = array())
	{
	/*
	 * Saves associations between a upload and keywords
	 * 
	 	$this->UploadsKeyword->saveAssociations($data['Upload']['id'], $this->saveManyIds, $upload_active);
	 */
			// remove the existing records (incase they add a keyword that is already associated with this upload)
			$existing = $this->find('list', array(
				'recursive' => -1,
				'fields' => array('UploadsKeyword.id', 'UploadsKeyword.keyword_id'),
				'conditions' => array(
					'UploadsKeyword.upload_id' => $upload_id,
				),
			));
			
			// get just the new ones
			$keyword_ids = array_diff($keyword_ids, $existing);
			
			// build the proper save array
			$data = array();
			foreach($keyword_ids as $keyword => $keyword_id)
			{
				$data[] = array('upload_id' => $upload_id, 'keyword_id' => $keyword_id, 'active' => 1);
			}
			return $this->saveMany($data);
	}
	
	function add($data)
	{
	/*
	 * Save relations with a upload
	 */
		if(isset($data[$this->alias]['keywords']) and isset($data[$this->alias]['upload_id']))
		{
			$keywords = $data[$this->alias]['keywords'];
			if(is_string($keywords))
			$keywords = split("\n", $keywords);
			
			// clean them up and format them for a saveMany()
			$_keywords = array();
			$active = array();
			foreach($keywords as $i => $keyword)
			{
				$keyword = trim($keyword);
				if(!$keyword) continue;
				$_keywords[$keyword] = array('keyword' => $keyword); // format and make unique
			}
			sort($_keywords);
			
			// save only the new keywords
			$this->Keyword->saveMany($_keywords);
			
			// retrieve and save all of the new associations
			$this->saveAssociations($data[$this->alias]['upload_id'], $this->Keyword->saveManyIds);
		}
		return true;
	}
}
