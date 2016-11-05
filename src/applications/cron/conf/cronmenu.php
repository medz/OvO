<?php
defined('WEKIT_VERSION') or exit(403);
return array(
	'operations' => array('运营', array()), 
	'cron_operations' => array('计划任务', 'cron/cron/*', '', '', 'operations'), 
);