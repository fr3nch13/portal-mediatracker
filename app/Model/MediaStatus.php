<?php
App::uses('AppModel', 'Model');
/**
 * MediaStatus Model
 *
 * @property Media $Media
 */
class MediaStatus extends AppModel {

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
			'foreignKey' => 'media_status_id',
			'dependent' => false,
		)
	);

	public function afterFind($results = array(), $primary = false)
	{
		
		foreach($results as $i => $result)
		{
			if(isset($result['MediaStatus']) and !isset($result['MediaStatus']['id']))
			{
				$results[$i]['MediaStatus']['id'] = 0;
				$results[$i]['MediaStatus']['name'] = 'Media Tracking Initiated';
			}
		}

		return parent::afterFind($results, $primary);
	}
}
