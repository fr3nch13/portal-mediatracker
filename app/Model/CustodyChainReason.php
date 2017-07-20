<?php
App::uses('AppModel', 'Model');
/**
 * CustodyChainReason Model
 *
 * @property CustodyChain $CustodyChain
 */
class CustodyChainReason extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'name';


	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'CustodyChain' => array(
			'className' => 'CustodyChain',
			'foreignKey' => 'custody_chain_reason_id',
			'dependent' => false,
		)
	);

	public function afterFind($results = array(), $primary = false)
	{
		
		foreach($results as $i => $result)
		{
			if(isset($result['CustodyChainReason']) and !isset($result['CustodyChainReason']['id']))
			{
				$results[$i]['CustodyChainReason']['id'] = 0;
				$results[$i]['CustodyChainReason']['name'] = 'Media Tracking Initiated';
			}
		}

		return parent::afterFind($results, $primary);
	}
}
