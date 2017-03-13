<?php

defined('WEKIT_VERSION') or exit(403);

return [
    'appcenter'              => ['应用', []],
    'appcenter_test'         => ['test', 'appcenter/app/*', '', '', 'appcenter'],
    'appcenter_server_check' => ['服务检测', 'appcenter/server/*?operate=check', '', '', 'appcenter'],
    'appcenter_server'       => ['服务中心', 'appcenter/server/*', '', '', 'appcenter'],
    'appcenter_appList'      => ['应用中心', 'appcenter/appcenter/*', '', '', 'appcenter'],
    'appcenter_index'        => ['我的应用', 'appcenter/spp/*', '', '', 'appcenter'],
    'appcenter_siteStyle'    => ['我的模板', 'appcenter/style/*', '', '', 'appcenter'],
];
