<?php
$stats = array(
	'total' => array('name' => __('Total'), 'value' => count($_media), 'color' => 'FFFFFF'),
	'ObtainReason.0' => array('name' => __('No Obtain Reason'), 'value' => 0, 'color' => '000000'),
);

foreach($obtainReasons as $obtainReason_id => $obtainReason_name)
{
	$stats['ObtainReason.'.$obtainReason_id] = array(
		'name' => $obtainReason_name,
		'value' => 0,
		'color' => substr(md5($obtainReason_name), 0, 6),
	);
}

foreach($_media as $media)
{
	if($media['ObtainReason']['id'])
	{
		$obtainReason_id = $media['ObtainReason']['id'];
		if(!isset($stats['ObtainReason.'.$obtainReason_id]))
		{
			$stats['ObtainReason.'.$obtainReason_id] = array(
				'name' => $media['ObtainReason']['name'],
				'value' => 0,
				'color' => substr(md5($media['ObtainReason']['name']), 0, 6),
				
			);
		}
		$stats['ObtainReason.'.$obtainReason_id]['value']++;
	}
	else
	{
		$stats['ObtainReason.0']['value']++;
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
	'title' => __('%s by %s', __('Tracked Media'), __('Obtain Reasons')),
	'content' => $content,
));