<?php 
// File: app/View/MediaStatuses/index.ctp


$page_options = array(
	$this->Html->link(__('Add Media Status'), array('action' => 'add')),
);

// content
$th = array(
	'MediaStatus.name' => array('content' => __('Media Status'), 'options' => array('sort' => 'MediaStatus.name')),
	'actions' => array('content' => __('Actions'), 'options' => array('class' => 'actions')),
);

$td = array();
foreach ($media_statuses as $i => $media_status)
{
	$td[$i] = array(
		$media_status['MediaStatus']['name'],
		array(
			$this->Html->link(__('Edit'), array('action' => 'edit', $media_status['MediaStatus']['id'])).
			$this->Html->link(__('Delete'), array('action' => 'delete', $media_status['MediaStatus']['id']),array('confirm' => 'Are you sure?')), 
			array('class' => 'actions'),
		),
	);
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('Media Statuses'),
	'page_options' => $page_options,
	'th' => $th,
	'td' => $td,
	));
?>