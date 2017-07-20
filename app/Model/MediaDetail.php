<?php
App::uses('AppModel', 'Model');
/**
 * MediaDetail Model
 *
 * @property Media $Media
 * @property ObtainReason $ObtainReason
 */
class MediaDetail extends AppModel {

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
		'obtain_reason_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
			),
		),
		'property_tag' => array(
			'alphaNumeric' => array(
				'rule'     => array('minLength', 3),
				'required' => true,
				'allowEmpty' => false,
				'message' => 'This is a required field.',
            ),
		),
		'ticket_ticket' => array(
			'alphaNumeric' => array(
				'rule'     => 'alphaNumeric',
				'required' => true,
				'allowEmpty' => false,
				'message' => 'This field is required, and only allows numbers and letters.',
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
		'Media' => array(
			'className' => 'Media',
			'foreignKey' => 'media_id',
		),
	);
}
