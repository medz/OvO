<?php
/**
 * 自定义html属性的实现
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>.
 *
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwDesignDoPropertyService.php 16219 2012-08-21 07:11:22Z gao.wanggao $
 */
class PwDesignDoPropertyService
{
    public function displayHtml($hook, $vProperty, $decorator)
    {
        list($tpl, $hookname) = explode('|', $hook);
        if (! $tpl || ! $hookname) {
            return '';
        }
        PwHook::template($hookname, 'TPL:design.property.'.$tpl, true, ['property' => $vProperty, 'decorator' => $decorator]);
    }
}
