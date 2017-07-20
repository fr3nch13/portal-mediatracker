<?php 
// File: app/View/OrgGroups/index.ctp


$page_options = array(
//	$this->Html->link(__('Add Org Group'), array('action' => 'add')),
);

// content
$th = array(
	'OrgGroup.name' => array('content' => __('Org Group'), 'options' => array('sort' => 'OrgGroup.name')),
	'OrgGroup.sendemail' => array('content' => __('Send Email?'), 'options' => array('sort' => 'OrgGroup.sendemail')),
	'OrgGroup.mon' => array('content' => __('Mon'), 'options' => array('sort' => 'OrgGroup.mon')),
	'OrgGroup.tue' => array('content' => __('Tues'), 'options' => array('sort' => 'OrgGroup.tue')),
	'OrgGroup.wed' => array('content' => __('Wed'), 'options' => array('sort' => 'OrgGroup.wed')),
	'OrgGroup.thu' => array('content' => __('Thurs'), 'options' => array('sort' => 'OrgGroup.thu')),
	'OrgGroup.fri' => array('content' => __('Fri'), 'options' => array('sort' => 'OrgGroup.fri')),
	'OrgGroup.sat' => array('content' => __('Sat'), 'options' => array('sort' => 'OrgGroup.sat')),
	'OrgGroup.sun' => array('content' => __('Sun'), 'options' => array('sort' => 'OrgGroup.sun')),
	'OrgGroup.notify_time' => array('content' => __('Send Email At'), 'options' => array('sort' => 'OrgGroup.notify_time')),
	'actions' => array('content' => __('Actions'), 'options' => array('class' => 'actions')),
);

$td = array();
foreach ($org_groups as $i => $org_group)
{
	$td[$i] = array(
		$org_group['OrgGroup']['name'],
		$this->Wrap->yesNo($org_group['OrgGroup']['sendemail']),
		$this->Wrap->check($org_group['OrgGroup']['mon']),
		$this->Wrap->check($org_group['OrgGroup']['tue']),
		$this->Wrap->check($org_group['OrgGroup']['wed']),
		$this->Wrap->check($org_group['OrgGroup']['thu']),
		$this->Wrap->check($org_group['OrgGroup']['fri']),
		$this->Wrap->check($org_group['OrgGroup']['sat']),
		$this->Wrap->check($org_group['OrgGroup']['sun']),
		$this->Local->emailTimes(($org_group['OrgGroup']['notify_time']?$org_group['OrgGroup']['notify_time']:null)),
		array(
			$this->Html->link(__('View'), array('action' => 'view', $org_group['OrgGroup']['id'])).
			$this->Html->link(__('Email Options'), array('action' => 'email_options', $org_group['OrgGroup']['id'])), 
			array('class' => 'actions'),
		),
	);
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('Org Groups'),
	'page_options' => $page_options,
	'th' => $th,
	'td' => $td,
));