<?php
App::uses('AppModel', 'Model');
/**
 * CustodyChain Model
 *
 * @property Media $Media
 * @property ReleasedUser $ReleasedUser
 * @property ReceivedUser $ReceivedUser
 * @property CustodyChainReason $CustodyChainReason
 */
class CustodyChain extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'media_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
			),
		),
		'released_user_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
			),
		),
		'received_user_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
			),
		),
		'custody_chain_reason_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
			),
		),
	);

	//The Associations below have been created with all possible keys, those that are not needed can be removed


/**
 * hasMany associations
 *
 * @var array
 */
	public $hasOne = array(
		'Upload' => array(
			'className' => 'Upload',
			'foreignKey' => 'custody_chain_id',
			'dependent' => true,
		),
	);
	
/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Media' => array(
			'className' => 'Media',
			'foreignKey' => 'media_id',
		),
		'ChainAddedUser' => array(
			'className' => 'User',
			'foreignKey' => 'added_user_id',
		),
		'ChainReleasedUser' => array(
			'className' => 'User',
			'foreignKey' => 'released_user_id',
		),
		'ChainReceivedUser' => array(
			'className' => 'User',
			'foreignKey' => 'received_user_id',
		),
		'CustodyChainReason' => array(
			'className' => 'CustodyChainReason',
			'foreignKey' => 'custody_chain_reason_id',
		)
	);
	
	public $actsAs = array(
		'Dblogger.Dblogger' // log all changes to the database
	);
	
	// used to map column names to readable states
	public $mappedFields = array(
		'media_id' => array('name' => 'Media', 'value' => 'Media.id'),
		'added_user_id' => array('name' => 'Created By', 'value' => 'ChainAddedUser.email'),
		'released_user_id' => array('name' => 'Media Released By', 'value' => 'ChainReleasedUser.email'),
		'released_user_other' => array('name' => 'Media Released By (Other)'),
		'received_user_id' => array('name' => 'Media Received By', 'value' => 'ChainReceivedUser.email'),
		'received_user_other' => array('name' => 'Media Received By (Other)'),
		'custody_chain_reason_id' => array('name' => 'Reason', 'value' => 'CustodyChainReason.name'),
	);
	
	public function beforeValidate($options = array())
	{
		// make sure we allow records to be created with no file attached
		if(isset($this->data['Upload']))
		{
			// make sure [file] exists
			if(isset($this->data['Upload']['file']))
			{
				$this->data['Upload'] = array_merge($this->data['Upload']['file'], $this->data['Upload']);
				unset($this->data['Upload']['file']);
			}
			
			if($this->data['Upload']['error'] != 0 or $this->data['Upload']['error'] == 4)
			{
				unset($this->data['Upload']);
			}
		}
		
		return parent::beforeValidate();
	}
	
	public function afterFind($results = array(), $primary = false)
	{
		
		foreach($results as $i => $result)
		{
			if(isset($result['MediaStatus']) and !isset($result['MediaStatus']['id']))
			{
				$results[$i]['MediaStatus']['id'] = 0;
				$results[$i]['MediaStatus']['name'] = 'Media tracking initiated';
			}
		}

		return parent::afterFind($results, $primary);
	}
}
