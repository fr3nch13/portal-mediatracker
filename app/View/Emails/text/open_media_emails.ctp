<?php 
// File: app/View/Emails/text/open_media_emails.ctp

$this->Html->setFull(true);
$this->Html->asText(true);

$page_options = array(
	$this->Html->link(__('View these %s', __('Media')), array('controller' => 'media', 'action' => 'open')),
);


// content
$th = array(
	'Media.id' => array('content' => __('ID'), 'options' => array('sort' => 'Media.id')),
	'MediaDetail.ticket_ticket' => array('content' => __('Example'), 'options' => array('sort' => 'MediaDetail.ticket_ticket')),
	'MediaDetail.other_ticket' => array('content' => __('Other Ticket'), 'options' => array('sort' => 'MediaDetail.other_ticket')),
	'MediaDetail.property_tag' => array('content' => __('Property Tag'), 'options' => array('sort' => 'MediaDetail.property_tag')),
	'MediaStatus.name' => array('content' => __('Status'), 'options' => array('sort' => 'MediaStatus.name')),
	'MediaAddedUser.name' => array('content' => __('Tracking Created By'), 'options' => array('sort' => 'MediaAddedUser.name')),
//	'Media.obtained' => array('content' => __('Obtained'), 'options' => array('sort' => 'Media.obtained')),
	'Media.created' => array('content' => __('Created'), 'options' => array('sort' => 'Media.created')),
	'UploadLatest.name' => array('content' => __('Last CoC File')),
	'UploadLatestUser.name' => array('content' => __('Last CoC File Added By')),
	'MediaModifiedUser.name' => array('content' => __('Last Updated By'), 'options' => array('sort' => 'MediaModifiedUser.name')),
	'actions' => array('content' => __('Actions'), 'options' => array('class' => 'actions')),
);

$td = array();
foreach ($_media as $i => $media)
{
	$uploadLatest = '&nbsp;';
	$uploadLatestUser = '&nbsp;';
	if(isset($media['UploadLatest']['filename']))
	{
		$uploadLatest = $this->Html->link($media['UploadLatest']['filename'], array('controller' => 'uploads', 'action' => 'download', $media['UploadLatest']['id']));
	}
	if(isset($media['UploadLatestUser']['name']))
	{
		$tmp = array('User' => $media['UploadLatestUser']);
		$uploadLatestUser = $this->Html->link($tmp['User']['name'], array('controller' => 'users', 'action' => 'view', $tmp['User']['id']), array('escape' => false, 'class' => 'avatar_tiny'));  
	}
	$MediaModifiedUser = '&nbsp;';
	if(isset($media['MediaModifiedUser']['name']))
	{
		$tmp = array('User' => $media['MediaModifiedUser']);
		$MediaModifiedUser = $this->Html->link($tmp['User']['name'], array('controller' => 'users', 'action' => 'view', $tmp['User']['id']), array('escape' => false, 'class' => 'avatar_tiny'));  
	}
	
	$tmp = array('User' => $media['MediaAddedUser']);
	$MediaAddedUser = $this->Html->link($tmp['User']['name'], array('controller' => 'users', 'action' => 'view', $tmp['User']['id']), array('escape' => false, 'class' => 'avatar_tiny'));  
	
	$td[$i] = array(
		$this->Html->link($media['Media']['id'], array('action' => 'view', $media['Media']['id'])),
		$this->Html->link($media['MediaDetail']['ticket_ticket'], array('action' => 'view', $media['Media']['id'])),
		$media['MediaDetail']['other_ticket'],
		$media['MediaDetail']['property_tag'],
		$media['MediaStatus']['name'],
		$MediaAddedUser,
//		$this->Wrap->niceTime($media['Media']['obtained']),
		$this->Wrap->niceTime($media['Media']['created']),
		$uploadLatest,
		$uploadLatestUser,
		$MediaModifiedUser,
		array(
			$this->Html->link(__('View'), array('action' => 'view', $media['Media']['id'])). 
			$this->Html->link(__('Add CoC'), array('controller' => 'custody_chains', 'action' => 'add', $media['Media']['id'])),
//			$this->Html->link(__('Close'), array('action' => 'toggle', 'state', $media['Media']['id']), array('confirm' => 'Are you sure?')), 
			array('class' => 'actions'),
		),
	);
}


echo $this->element('Utilities.email_text_index', array(
	'page_title' => __('Open Media'),
	'instructions' => $org_group['OrgGroup']['instructions'],
	'page_options' => $page_options,
	'th' => $th,
	'td' => $td,
));