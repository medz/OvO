<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwCronDoClearOnline.php 18771 2012-09-27 07:47:26Z gao.wanggao $
 * @package
 */
Wind::import('SRV:cron.srv.base.AbstractCronBase');

class PwCronDoClearOnline extends AbstractCronBase
{
    public function run($cronId)
    {
        $srv = Wekit::load('online.srv.PwOnlineService');
        $ds = Wekit::load('cron.PwCron');
        $srv->clearNotOnline();
    }
}
