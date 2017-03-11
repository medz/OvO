<?php

defined('WEKIT_VERSION') or exit(403);

return array(
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

'APPS'      => '../src/phpwind9/applications',
'EXT'       => '../src/extensions',
'SRV'       => '../src/phpwind9/service',
'REP'       => '../src/phpwind9/repository',
'WINDID'    => '../src/phpwind9/windid',
'ADMIN'     => '../src/phpwind9/applications/admin',
'APPCENTER' => '../src/phpwind9/applications/appcenter',
/*
 * 可访问目录
 */

'PUBLIC' => '..',
'THEMES' => '../themes',
'TPL'    => '../template',
'ATTACH' => '../attachment',
'HTML'   => '../html',

/**=====配置结束于此=====**/

);
