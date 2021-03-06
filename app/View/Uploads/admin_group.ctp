<?php 
// File: app/View/Uploads/admin_group.ctp

$page_options = array(
//	$this->Html->link(__('Add a File'), array('action' => 'add')),
);
// content
$th = array();
	$th['Upload.type'] = array('content' => __('Type'), 'options' => array('sort' => 'Upload.type'));
	$th['Upload.filename'] = array('content' => __('File Name'), 'options' => array('sort' => 'Upload.filename'));
	$th['User.name'] = array('content' => __('Owner'), 'options' => array('sort' => 'User.name'));
	$th['Media.id'] = array('content' => __('Media ID'), 'options' => array('sort' => 'Media.id'));
	$th['CustodyChain.id'] = array('content' => __('Chain Of Custody ID'), 'options' => array('sort' => 'CustodyChain.id'));
	$th['Upload.created'] = array('content' => __('Uploaded'), 'options' => array('sort' => 'Upload.created'));
	$th['actions'] = array('content' => __('Actions'), 'options' => array('class' => 'actions'));

$td = array();
foreach ($uploads as $i => $upload)
{
	$tmp = array('User' => $upload['User']);
	$User = $this->Html->link($tmp['User']['name']. $this->Avatar->view($tmp, 'tiny'), array('controller' => 'users', 'action' => 'view', $tmp['User']['id']), array('escape' => false, 'class' => 'avatar_tiny'));  

	$td[$i] = array();
	$td[$i]['Upload.type'] = $this->Wrap->fileIcon($upload['Upload']['type']);
	$td[$i]['Upload.filename'] = $this->Html->link($upload['Upload']['filename'], array('controller' => 'uploads', 'action' => 'download', $upload['Upload']['id']));
	$td[$i]['User.name'] = $User;
	$td[$i]['Media.id'] = $this->Html->link($upload['Media']['id'], array('controller' => 'media', 'action' => 'view', $upload['Media']['id']));
	$td[$i]['CustodyChain.id'] = $this->Html->link($upload['CustodyChain']['id'], array('controller' => 'media', 'action' => 'view', $upload['Media']['id']));
	$td[$i]['Upload.created'] = $this->Wrap->niceTime($upload['Upload']['created']);
	
	$actions = $this->Html->link(__('View'), array('action' => 'view', $upload['Upload']['id']));
	$actions .= $this->Html->link(__('Download'), array('action' => 'download', $upload['Upload']['id']));
	$actions .= $this->Form->postLink(__('Delete'),array('action' => 'delete', $upload['Upload']['id']),array('confirm' => 'Are you sure?'));
	
	$td[$i]['actions'] = array(
		$actions,
		array('class' => 'actions'),
	);
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('All Files'),
	'page_options' => $page_options,
	'search_placeholder' => __('files'),
	'th' => $th,
	'td' => $td,
	));
?>