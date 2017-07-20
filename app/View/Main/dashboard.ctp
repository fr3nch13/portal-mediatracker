<?php

$dashboard_blocks = array(
	'equipment_overview' => array('controller' => 'media', 'action' => 'db_block_overview', 'plugin' => false),
	'db_block_obtain_reasons' => array('controller' => 'media', 'action' => 'db_block_obtain_reasons', 'plugin' => false),
	'db_block_types' => array('controller' => 'media', 'action' => 'db_block_types', 'plugin' => false),
	'db_block_statuses_closed' => array('controller' => 'media', 'action' => 'db_block_statuses', 1, 'plugin' => false),
	'db_block_statuses' => array('controller' => 'media', 'action' => 'db_block_statuses', 'plugin' => false),
	'db_block_statuses_open' => array('controller' => 'media', 'action' => 'db_block_statuses', 0, 'plugin' => false),
);

echo $this->element('Utilities.page_dashboard', array(
	'page_title' => __('Dashboard: %s', __('Overview')),
	'page_options_html' => $this->element('dashboard_options'),
	'dashboard_blocks' => $dashboard_blocks,
));