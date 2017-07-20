<?php
App::uses('AppModel', 'Model');
/**
 * ReceivedOrg Model
 *
 * @property Media $Media
 */
class ReceivedOrg extends AppModel {

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
		'Media' => array(
			'className' => 'Media',
			'foreignKey' => 'received_org_id',
			'dependent' => false,
		)
	);

}
