<?php 
// File: app/View/CustodyChains/user.ctp

$page_options = array(
);

// content
$th = array(
	'CustodyChain.id' => array('content' => __('ID'), 'options' => array('sort' => 'CustodyChain.id')),
	'Media.id' => array('content' => __('Media ID'), 'options' => array('sort' => 'Media.id')),
	'CustodyChain.created' => array('content' => __('Time'), 'options' => array('sort' => 'CustodyChain.created')),
	'ChainReleasedUser.name' => array('content' => __('Media Released By'), 'options' => array('sort' => 'ChainReleasedUser.name')),
	'ChainReceivedUser.name' => array('content' => __('Media Received By'), 'options' => array('sort' => 'ChainReceivedUser.name')),
	'CustodyChainReason.name' => array('content' => __('Reason'), 'options' => array('sort' => 'CustodyChainReason.name')),
	'Upload.filename' => array('content' => __('File'), 'options' => array('sort' => 'Upload.filename')),
	'ChainAddedUser.name' => array('content' => __('CoC Created By'), 'options' => array('sort' => 'ChainAddedUser.name')),
	'actions' => array('content' => __('Actions'), 'options' => array('class' => 'actions')),
);

$td = array();
$i = 0;
$role = AuthComponent::user('role');
foreach ($custody_chains as $custody_chain)
{
	$released_by = ' ';
	if($custody_chain['CustodyChain']['released_user_id'])
	{
		$tmp = array('User' => $custody_chain['ChainReleasedUser']);
		$released_by = $this->Html->link($tmp['User']['name']. $this->Avatar->view($tmp, 'tiny'), array('controller' => 'users', 'action' => 'view', $tmp['User']['id']), array('escape' => false, 'class' => 'avatar_tiny'));
	}
	else
	{
		$released_by .= $custody_chain['CustodyChain']['released_user_other'];
	}
	$received_by = ' ';
	if($custody_chain['CustodyChain']['received_user_id'])
	{
		$tmp = array('User' => $custody_chain['ChainReceivedUser']);
		$received_by .= $this->Html->link($tmp['User']['name']. $this->Avatar->view($tmp, 'tiny'), array('controller' => 'users', 'action' => 'view', $tmp['User']['id']), array('escape' => false, 'class' => 'avatar_tiny'));
	}
	else
	{
		$received_by .= $custody_chain['CustodyChain']['received_user_other'];
	}
	$options = '';
	$options .= $this->Html->link(__('View'), array('controller' => 'uploads', 'action' => 'view', $custody_chain['Upload']['id']));
	if($custody_chain['Upload']['filename'])
	{
		$options .= $this->Html->link(__('Download'), array('controller' => 'uploads', 'action' => 'download', $custody_chain['Upload']['id']));
	}
	if(in_array(AuthComponent::user('role'), array('admin', 'reviewer')))
	{
		$options .= $this->Html->link(__('Delete'), array('action' => 'delete', $custody_chain['CustodyChain']['id']),array('confirm' => Configure::read('Dialogues.deletecoc')));
	}
	
	$tmp = array('User' => $custody_chain['ChainAddedUser']);
	
	$td[$i] = array(
		$custody_chain['CustodyChain']['id'],
		$this->Html->link($custody_chain['Media']['id'], array('controller' => 'media', 'action' => 'view', $custody_chain['Media']['id'], 'admin' => false)),
		$this->Wrap->niceTime($custody_chain['CustodyChain']['created']),
		$released_by,
		$received_by,
		$custody_chain['CustodyChainReason']['name'],
		$this->Html->link($custody_chain['Upload']['filename'], array('controller' => 'uploads', 'action' => 'download', $custody_chain['Upload']['id'])),
		$this->Html->link($tmp['User']['name']. $this->Avatar->view($tmp, 'tiny'), array('controller' => 'users', 'action' => 'view', $tmp['User']['id']), array('escape' => false, 'class' => 'avatar_tiny')),
		array(
			$options, 
			array('class' => 'actions'),
		),
	);

	$i++;
	$td[$i] = array(
		'&nbsp;', 
		__('Notes: '). $custody_chain['CustodyChain']['notes'],
	);

	$i++;
}
echo $this->element('Utilities.page_index', array(
	'page_title' => __('Chain of Custody'),
	'search_placeholder' => __('Chain of Custody'),
	'page_options' => $page_options,
	'th' => $th,
	'td' => $td,
	));
?>