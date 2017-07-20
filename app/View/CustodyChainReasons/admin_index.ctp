<?php 
// File: app/View/CustodyChainReasons/index.ctp


$page_options = array(
	$this->Html->link(__('Add Custody Chain Reason'), array('action' => 'add')),
);

// content
$th = array(
	'CustodyChainReason.name' => array('content' => __('Custody Chain Reason'), 'options' => array('sort' => 'CustodyChainReason.name')),
	'actions' => array('content' => __('Actions'), 'options' => array('class' => 'actions')),
);

$td = array();
foreach ($custody_chain_reasons as $i => $custody_chain_reason)
{
	$td[$i] = array(
		$custody_chain_reason['CustodyChainReason']['name'],
		array(
			$this->Html->link(__('Edit'), array('action' => 'edit', $custody_chain_reason['CustodyChainReason']['id'])).
			$this->Html->link(__('Delete'), array('action' => 'delete', $custody_chain_reason['CustodyChainReason']['id']),array('confirm' => 'Are you sure?')), 
			array('class' => 'actions'),
		),
	);
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('Custody Chain Reasons'),
	'page_options' => $page_options,
	'th' => $th,
	'td' => $td,
	));
?>