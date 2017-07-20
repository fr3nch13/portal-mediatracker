<?php 
// File: app/View/Media/Emails/html/send_edit_email.ctp

$this->Html->setFull(true);

$page_options = array(
	$this->Html->link(__('View'), array('action' => 'view', $media['Media']['id'])),
);

$details_blocks = array();

$details_blocks[1] = array();
$details_blocks[1]['details'][] = array('name' => __('ID'), 'value' => $media['Media']['id']);
$details_blocks[1]['details'][] = array('name' => __('Example Ticket'), 'value' => $media['MediaDetail']['other_ticket']);
$details_blocks[1]['details'][] = array('name' => __('Property Tag'), 'value' => $media['MediaDetail']['property_tag']);
$details_blocks[1]['details'][] = array('name' => __('Media Status'), 'value' => $media['MediaStatus']['name']);

$tmp = array('User' => $media['MediaAddedUser']);
$MediaAddedUser = $this->Html->link($tmp['User']['name'], array('controller' => 'users', 'action' => 'view', $tmp['User']['id']), array('escape' => false, 'class' => 'avatar_tiny'));  
$details_blocks[1]['details'][] = array('name' => __('Created By'), 'value' => $MediaAddedUser);
$details_blocks[1]['details'][] = array('name' => __('Created'), 'value' => $this->Wrap->niceTime($media['Media']['created']));

$details_blocks[2] = array();

$MediaModifiedUser = '';
if($media['MediaModifiedUser']['id'])
{
	$tmp = array('User' => $media['MediaModifiedUser']);
	$MediaModifiedUser = $this->Html->link($tmp['User']['name'], array('controller' => 'users', 'action' => 'view', $tmp['User']['id']), array('escape' => false, 'class' => 'avatar_tiny'));  
}
$MediaOpenedUser = '';
if($media['MediaOpenedUser']['id'])
{
	$tmp = array('User' => $media['MediaOpenedUser']);
	$MediaOpenedUser = $this->Html->link($tmp['User']['name'], array('controller' => 'users', 'action' => 'view', $tmp['User']['id']), array('escape' => false, 'class' => 'avatar_tiny'));  
}
$MediaClosedUser = '';
if($media['MediaClosedUser']['id'])
{
	$tmp = array('User' => $media['MediaClosedUser']);
	$MediaClosedUser = $this->Html->link($tmp['User']['name'], array('controller' => 'users', 'action' => 'view', $tmp['User']['id']), array('escape' => false, 'class' => 'avatar_tiny'));  
}
$details_blocks[2]['details'][] = array('name' => __('Media State'), 'value' =>  $this->Local->state($media['Media']['state']));
$details_blocks[2]['details'][] = array('name' => __('Last Updated'), 'value' => $this->Wrap->niceTime($media['Media']['modified']));
$details_blocks[2]['details'][] = array('name' => __('Last Updated By'), 'value' => $MediaModifiedUser);
$details_blocks[2]['details'][] = array('name' => __('Last Opened'), 'value' => $this->Wrap->niceTime($media['Media']['opened']));
$details_blocks[2]['details'][] = array('name' => __('Last Opened By'), 'value' => $MediaOpenedUser);
$details_blocks[2]['details'][] = array('name' => __('Last Closed'), 'value' => $this->Wrap->niceTime($media['Media']['closed']));
$details_blocks[2]['details'][] = array('name' => __('Last Closed By'), 'value' => $MediaClosedUser);


$stats = array(
	array(
		'id' => 'custodyChains',
		'name' => __('Custody Chain'), 
		'value' => $media['Media']['counts']['CustodyChain.all'], 
	),
	array(
		'id' => 'uploadsMedia',
		'name' => __('Files'), 
		'value' => $media['Media']['counts']['Upload.all'], 
	),
);

$page_subtitle = __('Edit attempt made by %s', (isset($user['name'])?$user['name']:(isset($user['email'])?$user['name']:__('unknown'))));

echo $this->element('Utilities.email_html_view_columns', array(
	'page_title' => __('Media Details'),
	'page_subtitle' => $page_subtitle,
	'page_options' => $page_options,
	'details_blocks' => $details_blocks,
	'stats' => $stats,
));
$this->Html->setFull(false);