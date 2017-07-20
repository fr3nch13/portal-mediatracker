<?php 
// File: app/View/ReceivedOrgs/index.ctp


$page_options = array(
	$this->Html->link(__('Add Received Org'), array('action' => 'add')),
);

// content
$th = array(
	'ReceivedOrg.name' => array('content' => __('Received Org'), 'options' => array('sort' => 'ReceivedOrg.name')),
	'actions' => array('content' => __('Actions'), 'options' => array('class' => 'actions')),
);

$td = array();
foreach ($received_orgs as $i => $received_org)
{
	$td[$i] = array(
		$received_org['ReceivedOrg']['name'],
		array(
			$this->Html->link(__('Edit'), array('action' => 'edit', $received_org['ReceivedOrg']['id'])).
			$this->Html->link(__('Delete'), array('action' => 'delete', $received_org['ReceivedOrg']['id']),array('confirm' => 'Are you sure?')), 
			array('class' => 'actions'),
		),
	);
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('Received Orgs'),
	'page_options' => $page_options,
	'th' => $th,
	'td' => $td,
	));
?>