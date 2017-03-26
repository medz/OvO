<?php

return array_merge(include base_path('phpwind9/conf/directory.php'), [

/*
 * 全局应用部署目录配置
 */

/**=====配置开始于此=====**/

/*
 * 源代码库目录,路径相对于wekit.php文件所在目录
 */
'APPS' => base_path('phpwind9/src/applications/windidserver'),
'WSRV' => base_path('phpwind9/src/windid/service'),

/*
 * 可访问目录
 */

'PUBLIC' => base_path('phpwind9/windid'),
'THEMES' => base_path('phpwind9/windid/themes'),
'RES'    => base_path('phpwind9/windid/res'),
'ATTACH' => base_path('phpwind9/windid/attachment'),
'HTML'   => base_path('phpwind9/windid/html'),

/**=====配置结束于此=====**/

]);
