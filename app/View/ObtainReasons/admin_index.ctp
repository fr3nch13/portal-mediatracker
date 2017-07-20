<?php 
// File: app/View/ObtainReasons/index.ctp


$page_options = array(
	$this->Html->link(__('Add Obtain Reason'), array('action' => 'add')),
);

// content
$th = array(
	'ObtainReason.name' => array('content' => __('Obtain Reason'), 'options' => array('sort' => 'ObtainReason.name')),
	'actions' => array('content' => __('Actions'), 'options' => array('class' => 'actions')),
);

$td = array();
foreach ($obtain_reasons as $i => $obtain_reason)
{
	$td[$i] = array(
		$obtain_reason['ObtainReason']['name'],
		array(
			$this->Html->link(__('Edit'), array('action' => 'edit', $obtain_reason['ObtainReason']['id'])).
			$this->Html->link(__('Delete'), array('action' => 'delete', $obtain_reason['ObtainReason']['id']),array('confirm' => 'Are you sure?')), 
			array('class' => 'actions'),
		),
	);
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('Obtain Reasons'),
	'page_options' => $page_options,
	'th' => $th,
	'td' => $td,
	));
?>