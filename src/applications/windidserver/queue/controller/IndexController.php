<?php

/**
 * the last known user to change this file in the repository  <$LastChangedBy: jieyin $>
 * @author $Author: jieyin $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: IndexController.php 24792 2013-02-21 08:07:19Z jieyin $
 * @package
 */
class IndexController extends PwBaseController
{
    public function run()
    {
        $nid = $this->getInput('nid', 'post');

        if (!ini_get('safe_mode')) {
            ignore_user_abort(true);
            set_time_limit(0);
        }
        if ($nid) {
            $this->_getNotifyService()->sendByNid($nid);
        }
        if (!$nid || rand(0, 100) == 50) {
            $this->_getNotifyService()->send();
        }
        echo 'success';
        exit;
    }

    private function _getNotifyService()
    {
        return Wekit::load('WSRV:notify.srv.WindidNotifyServer');
    }
}
