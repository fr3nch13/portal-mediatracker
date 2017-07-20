<?php

$stats = array(
	'total' => array('name' => __('Total'), 'value' => count($_media)),
);
$soon = strtotime('+1 week');
$now = time();

foreach($_media as $media)
{
	if(!isset($stats['open']))
		$stats['open'] = array('name' => __('Open'), 'value' => 0);
	
	if(!isset($stats['closed']))
		$stats['closed'] = array('name' => __('Closed'), 'value' => 0);
	
	if($media['Media']['state'])
		$stats['open']['value']++;
	else
		$stats['closed']['value']++;
}

$content = $this->element('Utilities.object_dashboard_stats', array(
	'title' => false,
	'details' => $stats,
));

echo $this->element('Utilities.object_dashboard_block', array(
	'title' => __('%s - Overview', __('Tracked Media')),
	'content' => $content,
));