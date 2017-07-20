<?php 
// File: app/View/Users/view.ctp
$page_options = array(
);

$details = array(
	array('name' => __('Email'), 'value' => $this->Html->link($user['User']['email'], 'mailto:'. $user['User']['email'])),
	array('name' => __('AD Account'), 'value' => $user['User']['adaccount']),
);

$stats = array(
	array(
		'id' => 'uploadsUser',
		'name' => __('All  %s', __('Files')), 
		'tip' => __('All %s this user has uploaded.', __('Files')),
		'value' => $user['User']['counts']['Upload.all'], 
		'tab' => array('tabs', '1'), // the tab to display
	),
	array(
		'id' => 'AllMedia',
		'name' => __('All %s', __('Media')), 
		'tip' => __('All %s they are involved with.', __('Media')),
		'value' => $user['User']['counts']['Media.all'], 
		'tab' => array('tabs', '2'), // the tab to display
	),
	array(
		'id' => 'OpenMedia',
		'name' => __('Open %s', __('Media')), 
		'tip' => __('All %s they are involved with that is still open.', __('Media')),
		'value' => $user['User']['counts']['Media.open'], 
		'tab' => array('tabs', '2'), // the tab to display
	),
	array(
		'id' => 'CustodyChain',
		'name' => __('All %s', __('Custody Chains')), 
		'tip' => __('All %s they are involved with.', __('Custody Chains')),
		'value' => $user['User']['counts']['CustodyChain.all'], 
		'tab' => array('tabs', '3'), // the tab to display
	),
);

$tabs = array(
	array(
		'key' => 'uploadsUser',
		'title' => __('Files'),
		'url' => array('controller' => 'uploads', 'action' => 'user', $user['User']['id']),
	),
	array(
		'key' => 'Media',
		'title' => __('Media'),
		'url' => array('controller' => 'media', 'action' => 'user', $user['User']['id']),
	),
	array(
		'key' => 'CustodyChain',
		'title' => __('Custody Chains'),
		'url' => array('controller' => 'custody_chains', 'action' => 'user', $user['User']['id']),
	),
);

echo $this->element('Utilities.page_view', array(
	'page_title' => __('%s: %s', __('User'), $user['User']['name']),
	'page_options' => $page_options,
	'details' => $details,
	'stats' => $stats,
	'tabs' => $tabs,
));