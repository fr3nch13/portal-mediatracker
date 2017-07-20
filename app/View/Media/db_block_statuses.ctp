<?php
$stats = array(
	'total' => array('name' => __('Total'), 'value' => count($_media), 'color' => 'FFFFFF'),
	'MediaStatus.0' => array('name' => __('Media Tracking Initiated'), 'value' => 0, 'color' => '000000'),
);

foreach($mediaStatuses as $mediaStatus_id => $mediaStatus_name)
{
	$stats['MediaStatus.'.$mediaStatus_id] = array(
		'name' => $mediaStatus_name,
		'value' => 0,
		'color' => substr(md5($mediaStatus_name), 0, 6),
	);
}

foreach($_media as $media)
{
	if($media['MediaStatus']['id'])
	{
		$mediaStatus_id = $media['MediaStatus']['id'];
		if(!isset($stats['MediaStatus.'.$mediaStatus_id]))
		{
			$stats['MediaStatus.'.$mediaStatus_id] = array(
				'name' => $media['MediaStatus']['name'],
				'value' => 0,
				'color' => substr(md5($media['MediaStatus']['name']), 0, 6),
				
			);
		}
		$stats['MediaStatus.'.$mediaStatus_id]['value']++;
	}
	else
	{
		$stats['MediaStatus.0']['value']++;
	}	
}
$stats = Hash::sort($stats, '{s}.value', 'desc');

$pie_data = array(array(__('Status'), __('num %s', __('Media')) ));
$pie_options = array('slices' => array());
foreach($stats as $i => $stat)
{
	if($i == 'total')
	{
		$stats[$i]['pie_exclude'] = true;
		$stats[$i]['color'] = 'FFFFFF';
		continue;
	}
	if(!$stat['value'])
	{
		unset($stats[$i]);
		continue;
	}
	$pie_data[] = array($stat['name'], $stat['value'], $i);
	$pie_options['slices'][] = array('color' => '#'. $stat['color']);
}

$content = $this->element('Utilities.object_dashboard_chart_pie', array(
	'title' => '',
	'data' => $pie_data,
	'options' => $pie_options,
));

$content .= $this->element('Utilities.object_dashboard_stats', array(
	'title' => '',
	'details' => $stats,
));

echo $this->element('Utilities.object_dashboard_block', array(
	'title' => __('%s %s by %s', ($state === null?__('All'):($state?__('Open'):__('Closed'))), __('Tracked Media'), __('Statuses')),
	'content' => $content,
));