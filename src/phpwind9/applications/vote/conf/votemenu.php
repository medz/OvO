<?php

defined('WEKIT_VERSION') or exit(403);

return [
    'appcenter'      => ['应用中心', []],
    'appcenter_vote' => ['投票管理', 'vote/manage/*', '', '', 'appcenter'],
];
