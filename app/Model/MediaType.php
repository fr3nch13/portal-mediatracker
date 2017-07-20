<?php
App::uses('AppModel', 'Model');
/**
 * MediaType Model
 *
 * @property Media $Media
 */
class MediaType extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'name';


	//The Associations below have been created with all possible keys, those that are not needed can be removed

	
	public $hasAndBelongsToMany = array(
		'Media' => array(
			'className' => 'Media',
			'joinTable' => 'media_media_types',
			'foreignKey' => 'media_id',
			'associationForeignKey' => 'media_type_id',
			'unique' => 'keepExisting',
		),
	);
}
