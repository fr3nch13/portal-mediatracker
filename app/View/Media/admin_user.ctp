<?php 
// File: app/View/Media/index.ctp


$page_options = array(
//	$this->Html->link(__('Add Media'), array('action' => 'add')),
);

// content
$th = array(
	'Media.id' => array('content' => __('ID'), 'options' => array('sort' => 'Media.id')),
	'MediaDetail.ticket_ticket' => array('content' => __('Example'), 'options' => array('sort' => 'MediaDetail.ticket_ticket')),
	'MediaDetail.other_ticket' => array('content' => __('Other Ticket'), 'options' => array('sort' => 'MediaDetail.other_ticket')),
	'MediaDetail.property_tag' => array('content' => __('Property Tag'), 'options' => array('sort' => 'MediaDetail.property_tag')),
	'MediaStatus.name' => array('content' => __('Status'), 'options' => array('sort' => 'MediaStatus.name')),
	'MediaAddedUser.name' => array('content' => __('Tracking Created By'), 'options' => array('sort' => 'MediaAddedUser.name')),
	'Media.created' => array('content' => __('Created'), 'options' => array('sort' => 'Media.created')),
	'UploadLatest.name' => array('content' => __('Last CoC File')),
	'UploadLatestUser.name' => array('content' => __('Last CoC File Added By')),
	'Media.state' => array('content' => __('State'), 'options' => array('sort' => 'Media.state')),
	'MediaModifiedUser.name' => array('content' => __('Last Updated By'), 'options' => array('sort' => 'MediaModifiedUser.name')),
	'actions' => array('content' => __('Actions'), 'options' => array('class' => 'actions')),
);

$td = array();
foreach ($_media as $i => $media)
{
	$uploadLatest = '&nbsp;';
	if(isset($media['UploadLatest']['filename']))
	{
		$uploadLatest = $this->Html->link($media['UploadLatest']['filename'], array('controller' => 'uploads', 'action' => 'download', $media['UploadLatest']['id'], 'admin' => false));
	}
	$uploadLatestUser = '&nbsp;';
	if(isset($media['UploadLatestUser']['name']))
	{
		$tmp = array('User' => $media['UploadLatestUser']);
		$uploadLatestUser = $this->Html->link($tmp['User']['name']. $this->Avatar->view($tmp, 'tiny'), array('controller' => 'users', 'action' => 'view', $tmp['User']['id']), array('escape' => false, 'class' => 'avatar_tiny'));  
	}
	$MediaModifiedUser = '&nbsp;';
	if(isset($media['MediaModifiedUser']['name']))
	{
		$tmp = array('User' => $media['MediaModifiedUser']);
		$MediaModifiedUser = $this->Html->link($tmp['User']['name']. $this->Avatar->view($tmp, 'tiny'), array('controller' => 'users', 'action' => 'view', $tmp['User']['id']), array('escape' => false, 'class' => 'avatar_tiny'));  
	}
	
	$tmp = array('User' => $media['MediaAddedUser']);
	$MediaAddedUser = $this->Html->link($tmp['User']['name']. $this->Avatar->view($tmp, 'tiny'), array('controller' => 'users', 'action' => 'view', $tmp['User']['id']), array('escape' => false, 'class' => 'avatar_tiny'));  
	
	$td[$i] = array(
		$this->Html->link($media['Media']['id'], array('action' => 'view', $media['Media']['id'], 'admin' => false)),
		$this->Html->link($media['MediaDetail']['ticket_ticket'], array('action' => 'view', $media['Media']['id'], 'admin' => false)),
		$media['MediaDetail']['other_ticket'],
		$media['MediaDetail']['property_tag'],
		$media['MediaStatus']['name'],
		$MediaAddedUser,
		$this->Wrap->niceTime($media['Media']['created']),
		$uploadLatest,
		$uploadLatestUser,
		$this->Local->state($media['Media']['state']),
		$MediaModifiedUser,
		array(
			$this->Html->link(__('View'), array('action' => 'view', $media['Media']['id'], 'admin' => false)). 
			$this->Html->link(__('Add CoC'), array('controller' => 'custody_chains', 'action' => 'add', $media['Media']['id'], 'admin' => false)), 
			array('class' => 'actions'),
		),
	);
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('All Media'),
	'page_options' => $page_options,
	'th' => $th,
	'td' => $td,
	));
?>