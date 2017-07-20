<?php 
// File: app/View/Uploads/view.ctp
$details = array();
$tmp = array('User' => $upload['User']);
$User = $this->Html->link($tmp['User']['name']. $this->Avatar->view($tmp, 'tiny'), array('controller' => 'users', 'action' => 'view', $tmp['User']['id']), array('escape' => false, 'class' => 'avatar_tiny'));
$details[] = array('name' => __('Owner'), 'value' => $User);
$details[] = array('name' => __('Media Id'), 'value' => $this->Html->link($upload['Media']['id'], array('controller' => 'media', 'action' => 'view', $upload['Media']['id'])). '&nbsp;');
$details[] = array('name' => __('CoC ID'), 'value' => $this->Html->link($upload['CustodyChain']['id'], array('controller' => 'media', 'action' => 'view', $upload['Media']['id'])). '&nbsp;');
$details[] = array('name' => __('MD5'), 'value' => $this->Html->filterLink($upload['Upload']['md5'], array('controller' => 'uploads', 'action' => 'index', 'field' => 'md5', 'value' => $upload['Upload']['md5'])) );
$details[] = array('name' => __('Type'), 'value' => $upload['Upload']['type']);
$details[] = array('name' => __('Mime Type'), 'value' => $upload['Upload']['mimetype']);
$details[] = array('name' => __('Created'), 'value' => $this->Wrap->niceTime($upload['Upload']['created']));


$page_options = array(
	$this->Html->link(__('Download'), array('action' => 'download', $upload['Upload']['id'])),
);
if($upload['Upload']['user_id'] == AuthComponent::user('id'))
{
	$page_options[] = $this->Form->postLink(__('Delete'),array('action' => 'delete', $upload['Upload']['id']),array('confirm' => 'Are you sure?'));			
}

$stats = array(
/* not used
	array(
		'id' => 'tagsUpload',
		'name' => __('Tags'), 
		'value' => $upload['Upload']['counts']['Tagged.all'], 
		'tab' => array('tabs', '5'), // the tab to display
	),
*/
);

$tabs = array(
/* not used
	array(
		'key' => 'tags',
		'title' => __('Tags'),
		'url' => array('plugin' => 'tags', 'controller' => 'tags', 'action' => 'tagged', 'upload', $upload['Upload']['id']),
	),
*/
);
echo $this->element('Utilities.page_view', array(
	'page_title' => __('File'). ': '. $upload['Upload']['filename'],
	'page_options' => $page_options,
	'details' => $details,
	'stats' => $stats,
	'tabs_id' => 'tabs',
	'tabs' => $tabs,
));

?>
