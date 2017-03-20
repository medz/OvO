<?php

return [
/*
 * 全局应用部署目录配置
 */

/**=====配置开始于此=====**/

/*
 * 源代码库目录,路径相对于wekit.php文件所在目录
 */
'ROOT' => base_path('phpwind9'),
'CONF' => base_path('phpwind9/conf'),
'DATA' => base_path('phpwind9/data'),
'SRC'  => base_path('phpwind9/src'),

'APPS'      => base_path('phpwind9/src/applications'),
'EXT'       => base_path('phpwind9/src/extensions'),
'SRV'       => base_path('phpwind9/src/service'),
'REP'       => base_path('phpwind9/src/repository'),
'WINDID'    => base_path('phpwind9/src/windid'),
'ADMIN'     => base_path('phpwind9/src/applications/admin'),
'APPCENTER' => base_path('phpwind9/src/applications/appcenter'),
/*
 * 可访问目录
 */

'PUBLIC' => base_path('phpwind9'),
'THEMES' => base_path('phpwind9/themes'),
'TPL'    => base_path('phpwind9/template'),
'ATTACH' => base_path('phpwind9/attachment'),
'HTML'   => base_path('phpwind9/html'),

/**=====配置结束于此=====**/

];
