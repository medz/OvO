<?php

defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('LIB:utility.PwDelayRun');

/**
 * 通用配置服务
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwConfigService.php 23911 2013-01-17 05:54:43Z jieyin $
 * @package forum
 */

class PwConfigService
{
    /**
     * 当任意配置项被修改时，调用该服务更新缓存文件(Hook调用)
     *
     * @param string $namespace
     */
    public function updateConfig($namespace)
    {
        if (in_array($namespace, array('site', 'credit', 'bbs', 'attachment', 'components', 'seo', 'nav', 'windid'))) {
            PwDelayRun::getInstance()->call(array(
                Wekit::load('cache.srv.PwCacheUpdateService'),
                'updateConfig',
            ));
        }
    }
}
