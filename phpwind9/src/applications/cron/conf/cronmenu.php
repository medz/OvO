<?php

defined('WEKIT_VERSION') or exit(403);

return [
    'operations'      => ['运营', []],
    'cron_operations' => ['计划任务', 'cron/cron/*', '', '', 'operations'],
];
