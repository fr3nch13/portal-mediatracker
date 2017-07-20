<?php
App::uses('AppModel', 'Model');
/**
 * Media Model
 *
 * @property MediaDetail $MediaDetail
 * @property MediaType $MediaType
 * @property ReceivedUser $ReceivedUser
 * @property CustodyChain $CustodyChain
 */
class Media extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'media_type_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
			),
		),
		'status' => array(
			'numeric' => array(
				'rule' => array('numeric'),
			),
		),
		'quantity' => array(
			'numeric' => array(
				'rule' => array('numeric'),
			),
		),
	);

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * hasOne associations
 *
 * @var array
 */
	public $hasOne = array(
		'MediaDetail' => array(
			'className' => 'MediaDetail',
			'foreignKey' => 'media_id',
		)
	);

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'MediaStatus' => array(
			'className' => 'MediaStatus',
			'foreignKey' => 'media_status_id',
		),
		'MediaAddedUser' => array(
			'className' => 'User',
			'foreignKey' => 'added_user_id',
		),
		'MediaModifiedUser' => array(
			'className' => 'User',
			'foreignKey' => 'modified_user_id',
		),
		'MediaReceivedUser' => array(
			'className' => 'User',
			'foreignKey' => 'received_user_id',
		),
		'MediaClosedUser' => array(
			'className' => 'User',
			'foreignKey' => 'closed_user_id',
		),
		'MediaOpenedUser' => array(
			'className' => 'User',
			'foreignKey' => 'opened_user_id',
		),
		'ReceivedOrg' => array(
			'className' => 'ReceivedOrg',
			'foreignKey' => 'received_org_id',
		),
		'ObtainReason' => array(
			'className' => 'ObtainReason',
			'foreignKey' => 'obtain_reason_id',
		),
		'OrgGroup' => array(
			'className' => 'OrgGroup',
			'foreignKey' => 'org_group_id',
		)
	);
	
	public $hasAndBelongsToMany = array(
		'MediaType' => array(
			'className' => 'MediaType',
			'joinTable' => 'media_media_types',
			'foreignKey' => 'media_id',
			'associationForeignKey' => 'media_type_id',
			'unique' => 'keepExisting',
		),
	);

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'CustodyChain' => array(
			'className' => 'CustodyChain',
			'foreignKey' => 'media_id',
			'dependent' => true,
		),
		'Upload' => array(
			'className' => 'Upload',
			'foreignKey' => 'media_id',
			'dependent' => true,
		),
	);
	
	public $actsAs = array(
		'Dblogger.Dblogger', // log all changes to the database
		'Utilities.Email',
	);
	
	// define the fields that can be searched
	public $searchFields = array(
		'Media.make',
		'Media.model',
		'Media.serial',
		'MediaDetail.other_ticket',
		'MediaDetail.ticket_ticket',
		'MediaDetail.fo_case_num',
		'MediaDetail.property_tag',
		'MediaDetail.loc_building',
		'MediaDetail.loc_room',
		'MediaDetail.tickets',
		'MediaDetail.details',
		'MediaDetail.owner',
		'MediaDetail.cust_info',
		'MediaStatus.name',
		'ReceivedOrg.name',
		'MediaAddedUser.name',
		'MediaAddedUser.email',
		'MediaReceivedUser.name',
		'MediaReceivedUser.email',
	);
	
	// fields that are boolean and can be toggled
	public $toggleFields = array('state');
	
	// used to map column names to readable states
	public $mappedFields = array(
		'state' => array('name' => 'State', 'options' => array(0 => 'Closed', 1 => 'Open')),
		'media_status_id' => array('name' => 'Media Status', 'value' => 'MediaStatus.name'),
		'received_user_id' => array('name' => 'Received By User', 'value' => 'MediaReceivedUser.email'),
		'received_org_id' => array('name' => 'Received By Org', 'value' => 'ReceivedOrg.name'),
		'added_user_id' => array('name' => 'Created By', 'value' => 'MediaAddedUser.email'),
		'modified_user_id' => array('name' => 'Last Updated By', 'value' => 'MediaModifiedUser.email'),
		'obtain_reason_id' => array('name' => 'Obtained Reason', 'value' => 'ObtainReason.name'),
	);
	
	public $getLatestUpload = false;
	
	public function afterFind($results = array(), $primary = false)
	{
		if($this->getLatestUpload)
		{
			foreach($results as $i => $result)
			{
				$media_id = (isset($result['Media']['id'])?$result['Media']['id']:false);
				if(!$media_id) continue;
				
				$uploadLatest = $this->Upload->find('first', array(
					'recursive' => 0,
					'contain' => array('User'),
					'conditions' => array(
						'Upload.media_id' => $media_id,
					),
					'order' => array(
						'Upload.created' => 'desc',
					),
				));
				$results[$i]['UploadLatest'] = (isset($uploadLatest['Upload'])?$uploadLatest['Upload']:array());
				$results[$i]['UploadLatestUser'] = (isset($uploadLatest['User'])?$uploadLatest['User']:array());
			}
		}
		return parent::afterFind($results, $primary);
	}
	
	public function beforeValidate($options = array())
	{
		// make sure we allow records to be created with no file attached
		if(isset($this->data['Upload']))
		{
			if(isset($this->data['Upload']['file']) and $this->data['Upload']['file']['error'] != 0 or $this->data['Upload']['file']['error'] == 4)
			{
				unset($this->data['Upload']);
			}
		}
		
		return parent::beforeValidate();
	}
	
	public function afterSave($created = false, $options = array())
	{
		if($created and isset($this->data['Media']['id']) and $this->data['Media']['id'] > 0)
		{
			// newly created media, generate the first chain of custody
			$CustodyChainData = array(
				'CustodyChain' => array(
					'media_id' => $this->data['Media']['id'],
					'released_user_id' => 0,
					'released_user_other' => (isset($this->data['MediaDetail']['owner'])?$this->data['MediaDetail']['owner']:''),
					'received_user_id' => (isset($this->data['Media']['received_user_id'])?$this->data['Media']['received_user_id']:0),
					'added_user_id' => (isset($this->data['Media']['added_user_id'])?$this->data['Media']['added_user_id']:0),
					'custody_chain_reason_id' => (isset($this->data['Media']['custody_chain_reason_id'])?$this->data['Media']['custody_chain_reason_id']:0),
				),
			);
			$this->CustodyChain->create();
			$this->CustodyChain->data = $CustodyChainData;
			$this->CustodyChain->save($this->CustodyChain->data);
		}
		return parent::afterSave($created, $options);
	}
	
	public function isOwnedBy($id, $user_id) 
	{
	/*
	 * Checks if a user is the owner of this object
	 * Overrides the one in the AppModel
	 */
		return $this->field('id', array('id' => $id, 'added_user_id' => $user_id)) === $id;
	}
	
	public function sendEditEmail($id = false, $user_id = false)
	{
	/*
	 * Sends an email notification when a closed media is trying to be edited
	 */
	 	if(!$id) return false;
	 	
		// get the counts
		$this->getCounts = array(
			'Upload' => array(
				'all' => array(
					'conditions' => array(
						'Upload.media_id' => $id,
					),
				),
			),
			'CustodyChain' => array(
				'all' => array(
					'conditions' => array(
						'CustodyChain.media_id' => $id,
					),
				),
			),
		);
		
		$this->recursive = 1;
		$media = $this->read(null, $id);
		
		$user = array();
		if($user_id)
		{
			$this->MediaAddedUser->recursive = -1;
			if($user = $this->MediaAddedUser->read(null, $user_id))
			{
				$user = $user['MediaAddedUser'];
			}
		}
		
		$emails = array();
		
		$org_group_id = false;
		
		// Emails specific to this Media Entity
		if(isset($media['MediaAddedUser']['email']) and $media['MediaAddedUser']['email'])
		{
			$emails[$media['MediaAddedUser']['email']] = $media['MediaAddedUser']['email'];
		}
		if(isset($media['MediaModifiedUser']['email']) and $media['MediaModifiedUser']['email'])
		{
			$emails[$media['MediaModifiedUser']['email']] = $media['MediaModifiedUser']['email'];
		}
		if(isset($media['MediaReceivedUser']['email']) and $media['MediaReceivedUser']['email'])
		{
			$emails[$media['MediaReceivedUser']['email']] = $media['MediaReceivedUser']['email'];
		}
		if(isset($media['MediaClosedUser']['email']) and $media['MediaClosedUser']['email'])
		{
			$emails[$media['MediaClosedUser']['email']] = $media['MediaClosedUser']['email'];
		}
		if(isset($media['MediaOpenedUser']['email']) and $media['MediaOpenedUser']['email'])
		{
			$emails[$media['MediaOpenedUser']['email']] = $media['MediaOpenedUser']['email'];
		}
		
		// chain of custody emails
		if(isset($media['CustodyChain']) and count($media['CustodyChain']))
		{
			$coc_user_ids = array();
			foreach($media['CustodyChain'] as $coc)
			{
				if($coc['added_user_id']) $coc_user_ids[$coc['added_user_id']] = $coc['added_user_id'];
				if($coc['released_user_id']) $coc_user_ids[$coc['released_user_id']] = $coc['released_user_id'];
				if($coc['received_user_id']) $coc_user_ids[$coc['received_user_id']] = $coc['received_user_id'];
			}
			
			$coc_emails = $this->MediaAddedUser->find('list', array(
				'recursive' => -1,
				'conditions' => array('id' => $coc_user_ids),
				'fields' => array(
					'email',
				),
			));
			
			foreach($coc_emails as $coc_email)
			{
				$emails[$coc_email] = $coc_email;
			}
		}
		
		// reviewer emails in the same org group as the media
		if(isset($media['Media']['org_group_id']) and $media['Media']['org_group_id'])
		{
			$reviewerEmails = $this->MediaAddedUser->reviewerEmails($media['Media']['org_group_id']);
			foreach($reviewerEmails as $reviewerEmail)
			{
				$emails[$reviewerEmail] = $reviewerEmail;
			}
		}
		
		// all Admin 
		$adminEmails = $this->MediaAddedUser->adminEmails();
		foreach($adminEmails as $adminEmail)
		{
			$emails[$adminEmail] = $adminEmail;
		}
	 	
	 	
	 	// rebuild it to use the EmailBehavior from the Utilities Plugin
	 	$this->Email_reset();
		// set the variables so we can use view templates
		$viewVars = array(
			'user' => $user,
			'media' => $media,
		);
		
		$this->Email_set('to', $emails);
		$this->Email_set('subject', __('Closed %s needs editing - ID: %s', __('Media'), $media['Media']['id']));
		$this->Email_set('viewVars', $viewVars);
		$this->Email_set('template', 'send_edit_email');
		
		return $this->Email_executeFull();
	}

}