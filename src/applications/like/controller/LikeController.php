<?php


/**
 * the last known user to change this file in the repository  <$LastChangedBy: jinlong.panjl $>.
 *
 * @author $Author: jinlong.panjl $ foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: LikeController.php 6265 2012-03-20 01:15:06Z jinlong.panjl $
 */
class LikeController extends PwBaseController
{
    public function run()
    {
        //seo设置
        Wind::import('SRV:seo.bo.PwSeoBo');
        $seoBo = PwSeoBo::getInstance();
        $seoBo->init('like', 'hot');
        Wekit::setV('seo', $seoBo);
    }

    public function dataAction()
    {
        $cron = false;
        $_data = array();
        $page = (int) $this->getInput('page', 'get');
        $pageid = (int) $this->getInput('pageid', 'get');
        $moduleid = (int) $this->getInput('moduleid', 'get');
        $start = (int) $this->getInput('start', 'get');
        $start >= 100 && $start = 100;
        $module = Wekit::load('design.PwDesignModule')->getModule($moduleid);
        $perpage = 20;
        if (!$module) {
            $this->showMessage('operate.fail');
        } //返回成功信息
        $time = Pw::getTime();
        Wind::import('SRV:design.bo.PwDesignPageBo');
        $pageBo = new PwDesignPageBo();
        $ds = Wekit::load('design.PwDesignData');
        $vo = Wekit::load('design.srv.vo.PwDesignDataSo');
        $vo->setModuleId($moduleid);
        $vo->setReservation(0);
        $vo->orderbyViewOrder(true);
        $vo->orderbyDataid(true);
        $data = $ds->searchData($vo, $perpage, $start);
        $this->_getLikeContentService();
        foreach ($data as $k => $v) {
            $_data[$k] = unserialize($v['extend_info']);
            $_data[$k]['fromtype'] = ($v['from_app'] == 'thread') ? PwLikeContent::THREAD : 0;
            $_data[$k]['fromid'] = $v['from_id'];
            if ($v['end_time'] > 0 && $v['end_time'] < $time) {
                $cron = true;
            }
            foreach ($_data[$k] as $_k => $_v) {
                $_data[$k][$_k] = WindSecurity::escapeHTML($_v);
            }
        }
        if ($cron || count($data) < 1) {
            $pageBo->updateDesignCron(array($moduleid));
        }
        $this->setOutput($_data, 'html');
        $this->showMessage('operate.success');
    }

    public function getLastAction()
    {
        $fromid = (int) $this->getInput('fromid', 'get');
        $typeid = (int) $this->getInput('typeid', 'get');
        $_users = array();
        $like = $this->_getLikeContentService()->getInfoByTypeidFromid($typeid, $fromid);
        !$like && $this->showError('BBS:like.fail');
        $uids = $like['users'] ? explode(',', $like['users']) : array();
        $userInfos = Wekit::load('user.PwUser')->fetchUserByUid($uids);
        foreach ($userInfos as $user) {
            if (!$user['uid']) {
                continue;
            }
            $_users[$user['uid']]['uid'] = $user['uid'];
            $_users[$user['uid']]['username'] = $user['username'];
            $_users[$user['uid']]['avatar'] = Pw::getAvatar($user['uid']);
        }
        $this->setOutput($_users, 'data');
        $this->showMessage('BBS:like.success');
    }

    private function _getLikeContentService()
    {
        return Wekit::load('like.PwLikeContent');
    }
}
