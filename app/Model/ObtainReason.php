<?php
App::uses('AppModel', 'Model');
/**
 * ObtainReason Model
 *
 * @property MediaDetail $MediaDetail
 */
class ObtainReason extends AppModel {

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
			'foreignKey' => 'obtain_reason_id',
			'dependent' => false,
		)
	);

}
