<?php 
// File: app/View/OrgGroups/admin_view.ctp

$page_options = array(
	$this->Html->link(__('Email Options'), array('action' => 'email_options', $org_group['OrgGroup']['id'])),
);

$days = array();
foreach($org_group['OrgGroup'] as $k => $v)
{
	if(in_array($k, array('mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun')))
	{
		if($v)
		{
			$days[$k] = Inflector::humanize($k);
		}
	}
}
$days = implode(', ', $days);

$details = array(

	array('name' => __('Created'), 'value' => $this->Wrap->niceTime($org_group['OrgGroup']['created'])),
	array('name' => __('Modified'), 'value' => $this->Wrap->niceTime($org_group['OrgGroup']['modified'])),
	array('name' => __('Send Email?'), 'value' => $this->Wrap->yesNo($org_group['OrgGroup']['sendemail'])),
	array('name' => __('Send Email At'), 'value' => $this->Local->emailTimes(($org_group['OrgGroup']['notify_time']?$org_group['OrgGroup']['notify_time']:null))),
	array('name' => __('Send Email On'), 'value' => $days),
);

$stats = array();
$tabs = array();
$stats[] = array(
	'id' => 'Users',
	'name' => __('Users'), 
	'ajax_count_url' => array('controller' => 'users', 'action' => 'group', $org_group['OrgGroup']['id'], 'admin' => true),
	'tab' => array('tabs', count($tabs)+1), // the tab to display
);
$tabs[] = array(
	'key' => 'Users',
	'title' => __('Users'),
	'url' => array('controller' => 'users', 'action' => 'group', $org_group['OrgGroup']['id'], 'admin' => true),
);

$stats[] = array(
	'id' => 'Media',
	'name' => __('Media'), 
	'ajax_count_url' => array('controller' => 'media', 'action' => 'group', $org_group['OrgGroup']['id'], 'admin' => true),
	'tab' => array('tabs', count($tabs)+1), // the tab to display
);
$tabs[] = array(
	'key' => 'Media',
	'title' => __('Media'),
	'url' => array('controller' => 'media', 'action' => 'group', $org_group['OrgGroup']['id'], 'admin' => true),
);

echo $this->element('Utilities.page_view', array(
	'page_title' => __('Org Group'). ': '. $org_group['OrgGroup']['name'],
	'page_options' => $page_options,
	'details' => $details,
	'stats' => $stats,
	'tabs_id' => 'tabs',
	'tabs' => $tabs,
));