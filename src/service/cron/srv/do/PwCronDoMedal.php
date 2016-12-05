<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>.
 *
 * @author $Author$ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id$
 */
Wind::import('SRV:cron.srv.base.AbstractCronBase');

class PwCronDoMedal extends AbstractCronBase
{
    public function run($cronId)
    {
        $perpage = 100;
        $time = Pw::getTime();
        $ds = $this->_getMedalUserDs();
        $this->_getMedalLogDs()->deleteInfos($time, PwMedalLog::STATUS_AWARDED);
        $count = $ds->countExpiredMedalUser($time);
        if (!$count) {
            return false;
        }
        $page = ceil($count / $perpage);
        $service = $this->_getMedalService();
        for ($i = 1; $i <= $page; $i++) {
            list($start, $perpage) = Pw::page2limit($page, $perpage);
            $list = $ds->getExpiredMedalUser($time, $start, $perpage);
            foreach ($list as $v) {
                $service->updateMedalUser($v['uid']);
            }
            $ds->deleteMedalUsersBycount();
            sleep(2);
        }
    }

    private function _getMedalService()
    {
        return Wekit::load('medal.srv.PwMedalService');
    }

    private function _getMedalLogDs()
    {
        return Wekit::load('medal.PwMedalLog');
    }

    private function _getMedalUserDs()
    {
        return Wekit::load('medal.PwMedalUser');
    }
}
