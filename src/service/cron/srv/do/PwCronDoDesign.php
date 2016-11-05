<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwCronDoDesign.php 17721 2012-09-08 07:45:19Z gao.wanggao $
 * @package
 */
Wind::import('SRV:cron.srv.base.AbstractCronBase');

class PwCronDoDesign extends AbstractCronBase
{
    public function run($cronId)
    {
        Wind::import('SRV:design.srv.data.PwAutoData');
        $ds = Wekit::load('design.PwDesignCron');
        $list = $ds->getAllCron();
        foreach ($list as $v) {
            $srv = new PwAutoData($v['module_id']);
            $srv->addAutoData();
            $ds->deleteCron($v['module_id']);
            sleep(2);
        }
    }
}
