<?php
$stats = array(
	'total' => array('name' => __('Total'), 'value' => count($_media), 'color' => 'FFFFFF'),
	'MediaType.0' => array('name' => __('No Type'), 'value' => 0, 'color' => '000000'),
);

foreach($mediaTypes as $mediaType_id => $mediaType_name)
{
	$mediaType_name = preg_replace('/(Government|Contractor)\s+Furnished\s+Media/i', '$1', $mediaType_name);
	$stats['MediaType.'.$mediaType_id] = array(
		'name' => $mediaType_name,
		'value' => 0,
		'color' => substr(md5($mediaType_name), 0, 6),
	);
}

foreach($_media as $media)
{
	foreach($media['MediaType'] as $mediaType)
	{
		if($mediaType['id'])
		{
			$mediaType_id = $mediaType['id'];
			
			if(!isset($stats['MediaType.'.$mediaType_id]))
			{
				$stats['MediaType.'.$mediaType_id] = array(
					'name' => $mediaType['name'],
					'value' => 0,
					'color' => substr(md5($mediaType['name']), 0, 6),
					
				);
			}
			$stats['MediaType.'.$mediaType_id]['value']++;
		}
		else
		{
			$stats['MediaType.0']['value']++;
		}
	}
}
$stats = Hash::sort($stats, '{s}.value', 'desc');

$pie_data = array(array(__('Type'), __('num %s', __('Media')) ));
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
	'title' => __('%s by %s', __('Tracked Media'), __('Type')),
	'content' => $content,
));