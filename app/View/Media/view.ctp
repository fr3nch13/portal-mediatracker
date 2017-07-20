<?php 
// File: app/View/Media/view.ctp
$media_state2 = ($media['Media']['state']?__('Close'):__('Open'));
$media_state = __('%s - Click to %s', $this->Local->state($media['Media']['state']), $media_state2);
$media_state3 = ($media['Media']['state']?'closing':'opening');

$page_options = array(
	$this->Html->link(__('Edit'), array('action' => 'edit', $media['Media']['id'])),
);
if(in_array(AuthComponent::user('role'), array('admin', 'reviewer')))
{
	$page_options[] = $this->Html->link($media_state, array('action' => 'toggle', 'state', $media['Media']['id']), array('confirm' => Configure::read('Dialogues.'. $media_state3)));
}
if(in_array(AuthComponent::user('role'), array('admin')))
{
	$page_options[] = $this->Html->link(__('Delete'), array('action' => 'delete', $media['Media']['id'], 'admin' => true), array('confirm' => Configure::read('Dialogues.deletemedia')));
}

$details_left = array();
$details_left[] = array('name' => __('ID'), 'value' => $media['Media']['id']);
$details_left[] = array('name' => __('Property Tag'), 'value' => $media['MediaDetail']['property_tag']);
$details_left[] = array('name' => __('Media Status'), 'value' => $media['MediaStatus']['name']);

$tmp = array('User' => $media['MediaAddedUser']);
$MediaAddedUser = $this->Html->link($tmp['User']['name']. $this->Avatar->view($tmp, 'tiny'), array('controller' => 'users', 'action' => 'view', $tmp['User']['id']), array('escape' => false, 'class' => 'avatar_tiny'));  
$details_left[] = array('name' => __('Created By'), 'value' => $MediaAddedUser);
$details_left[] = array('name' => __('Created'), 'value' => $this->Wrap->niceTime($media['Media']['created']));

$details_right = array();

$MediaModifiedUser = '';
if($media['MediaModifiedUser']['id'])
{
	$tmp = array('User' => $media['MediaModifiedUser']);
	$MediaModifiedUser = $this->Html->link($tmp['User']['name']. $this->Avatar->view($tmp, 'tiny'), array('controller' => 'users', 'action' => 'view', $tmp['User']['id']), array('escape' => false, 'class' => 'avatar_tiny'));  
}
$MediaOpenedUser = '';
if($media['MediaOpenedUser']['id'])
{
	$tmp = array('User' => $media['MediaOpenedUser']);
	$MediaOpenedUser = $this->Html->link($tmp['User']['name']. $this->Avatar->view($tmp, 'tiny'), array('controller' => 'users', 'action' => 'view', $tmp['User']['id']), array('escape' => false, 'class' => 'avatar_tiny'));  
}
$MediaClosedUser = '';
if($media['MediaClosedUser']['id'])
{
	$tmp = array('User' => $media['MediaClosedUser']);
	$MediaClosedUser = $this->Html->link($tmp['User']['name']. $this->Avatar->view($tmp, 'tiny'), array('controller' => 'users', 'action' => 'view', $tmp['User']['id']), array('escape' => false, 'class' => 'avatar_tiny'));  
}
$details_right[] = array('name' => __('Media State'), 'value' =>  $this->Local->state($media['Media']['state']));
$details_right[] = array('name' => __('Last Updated'), 'value' => $this->Wrap->niceTime($media['Media']['modified']));
$details_right[] = array('name' => __('Last Updated By'), 'value' => $MediaModifiedUser);
$details_right[] = array('name' => __('Last Opened'), 'value' => $this->Wrap->niceTime($media['Media']['opened']));
$details_right[] = array('name' => __('Last Opened By'), 'value' => $MediaOpenedUser);
$details_right[] = array('name' => __('Last Closed'), 'value' => $this->Wrap->niceTime($media['Media']['closed']));
$details_right[] = array('name' => __('Last Closed By'), 'value' => $MediaClosedUser);


$additional_details = array();

$media_types = Set::classicExtract($media['MediaType'], '{n}.name');
if(!$media_types) $media_types = array('N/A');

$MediaReceivedUser = '';
if($media['MediaReceivedUser']['id'])
{
	$tmp = array('User' => $media['MediaReceivedUser']);
	$MediaReceivedUser = $this->Html->link($tmp['User']['name']. $this->Avatar->view($tmp, 'tiny'), array('controller' => 'users', 'action' => 'view', $tmp['User']['id']), array('escape' => false, 'class' => 'avatar_tiny'));  
}
$additional_details[] = array('name' => __('FO Case #'), 'value' => $media['MediaDetail']['fo_case_num']);
$additional_details[] = array('name' => __('Media Serial #'), 'value' => $media['Media']['serial']);
$additional_details[] = array('name' => __('Media Types'), 'value' => implode(', ', $media_types));
$additional_details[] = array('name' => __('Owner'), 'value' => $media['MediaDetail']['owner']);
$additional_details[] = array('name' => __('Loc (bld-rm)'), 'value' => $media['MediaDetail']['loc_building']. '-'. $media['MediaDetail']['loc_room']);
//$additional_details[] = array('name' => __('Media Type'), 'value' => $media['MediaType']['name']);
$additional_details[] = array('name' => __('Make/Model'), 'value' => $media['Media']['make']. '/'. $media['Media']['model']);
$additional_details[] = array('name' => __('Received By User'), 'value' => $MediaReceivedUser);
$additional_details[] = array('name' => __('Received By Org'), 'value' => $media['ReceivedOrg']['name']);
$additional_details[] = array('name' => __('Obtained Reason'), 'value' => $media['ObtainReason']['name']);
$additional_details[] = array('name' => __('Obtained'), 'value' => $this->Wrap->niceTime($media['Media']['obtained']));

if(in_array(AuthComponent::user('role'), array('admin', 'reviewer')))
{
	$additional_details[] = array('name' => __('Org Group'), 'value' => $media['OrgGroup']['name']);
}

$stats = array(
	array(
		'id' => 'custodyChains',
		'name' => __('Custody Chain'), 
		'value' => $media['Media']['counts']['CustodyChain.all'], 
		'tab' => array('ui-tabs', '1'), // the tab to display
	),
	array(
		'id' => 'uploadsMedia',
		'name' => __('Files'), 
		'value' => $media['Media']['counts']['Upload.all'], 
		'tab' => array('ui-tabs', '2'), // the tab to display
	),
);
		
$tabs = array();
$tabs[] = array(
	'key' => 'custodyChains',
	'title' => __('Chain of Custody'),
	'url' => array('controller' => 'custody_chains', 'action' => 'media', $media['Media']['id']),
);
$tabs[] = array(
	'key' => 'details',
	'title' => __('Additional Details'),
	'content' => $this->element('Utilities.details', array(
		'title' => __('Additional Details'),
		'details' => $additional_details,
	)),
);
$tabs[] = array(
	'key' => 'description',
	'title' => __('Description'),
	'content' => $this->Wrap->descView($media['MediaDetail']['details']),
);
$tabs[] = array(
	'key' => 'cust_info',
	'title' => __('Customer Details'),
	'content' => $this->Wrap->descView($media['MediaDetail']['cust_info']),
);
$tabs[] = array(
	'key' => 'tickets',
	'title' => __('Other Tickets'),
	'content' => $this->Wrap->descView($media['MediaDetail']['tickets']),
);
$tabs[] = array(
	'key' => 'uploadsMedia',
	'title' => __('Files'),
	'url' => array('controller' => 'uploads', 'action' => 'media', $media['Media']['id']),
);


echo $this->element('Utilities.page_compare', array(
	'page_title' => __('%s Details / ID: %s', __('Media'), $media['Media']['id']),
	'page_options' => $page_options,
	'details_left_title' => ' ',
	'details_left' => $details_left,
	'details_right_title' => ' ',
	'details_right' => $details_right,
	'stats' => $stats,
	'tabs_id' => 'tabs',
	'tabs' => $tabs,
));