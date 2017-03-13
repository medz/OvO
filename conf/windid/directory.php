<?php

defined('WEKIT_VERSION') or exit(403);

return [
/*
 * 全局应用部署目录配置
 */

/**=====配置开始于此=====**/

/*
 * 源代码库目录,路径相对于wekit.php文件所在目录
 */
'ROOT' => '..',
'CONF' => '../conf',
'DATA' => '../data',
'SRC'  => '../src/phpwind9',

'APPS'      => '../src/phpwind9/applications/windidserver',
'EXT'       => '../src/phpwind9/extensions',
'WSRV'      => '../src/phpwind9/windid/service',
'REP'       => '../src/phpwind9/repository',
'WINDID'    => '../src/phpwind9/windid',
'ACLOUD'    => '../src/phpwind9/aCloud',
'ADMIN'     => '../src/phpwind9/applications/admin',
'APPCENTER' => '../src/phpwind9applications/appcenter',

/*
 * 可访问目录
 */

'PUBLIC' => '../windid',
'THEMES' => '../windid/themes',
'RES'    => '../windid/res',
'TPL'    => '../template',
'ATTACH' => '../windid/attachment',
'HTML'   => '../windid/html',

/**=====配置结束于此=====**/

];
