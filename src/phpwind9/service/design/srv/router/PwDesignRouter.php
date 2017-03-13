<?php
/**
 * 门户页面类型扩展
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>.
 *
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwDesignRouter.php 28949 2013-05-31 05:43:05Z gao.wanggao $
 */
class PwDesignRouter
{
    public function get()
    {
        $path = Wind::getRealPath('SRV:design.srv.router.router');
        $sysPage = @include $path;
        $config = Wekit::C('site', 'design.router');
        if (is_array($config)) {
            $sysPage = array_merge($sysPage, $config);
        }

        return $sysPage;
    }

    /**
     * 增加一个门户页面类型
     * Enter description here ...
     *
     * @param string $m
     * @param string $c
     * @param string $a
     * @param string $name
     * @param string $unique 页面唯一标识，如果不需要单独设置，请留空
     */
    public function set($m, $c, $a, $name = '', $unique = '')
    {
        if (!$name && !$unique) {
            return false;
        }
        $router = Wekit::C('site', 'design.router');
        $router[$m.'/'.$c.'/'.$a] = [$name, $unique];
        $config = new PwConfigSet('site');
        $config->set('design.router', $router)->flush();

        return true;
    }
}
