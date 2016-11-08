<?php

Wind::import('APPS:windid.admin.WindidBaseController');
/**
 * the last known user to change this file in the repository  <$LastChangedBy: jieyin $>
 * @author $Author: jieyin $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: NotifyController.php 24773 2013-02-21 02:59:06Z jieyin $
 * @package
 */
class NotifyController extends WindidBaseController
{
    public function run()
    {
        $perPage = 10;
        $uids = $appids = $nids = array();
        list($clientid, $complete, $page) = $this->getInput(array('clientid', 'complete', 'page'));
        $page = $page > 1 ? $page : 1;
        $complete = ($complete === '') ? null : $complete;
        list($start, $limit) = Pw::page2limit($page, $perPage);
        $list = $this->_getLogDs()->getList($clientid, 0, $limit, $start, $complete);
        $count = $this->_getLogDs()->countList($clientid, 0, $complete);
        foreach ($list as $k => $v) {
            $appids[] = $v['appid'];
            $nids[] = $v['nid'];
        }
        $apps = $this->_getAppDs()->getList();
        $notifys = $this->_getNotifyDs()->fetchNotify(array_unique($nids));
        foreach ($notifys as $v) {
            $param = unserialize($v['param']);
            isset($param['uid']) && $uids[] = $param['uid'];
        }
        $users = $this->_getUserDs()->fetchUserByUid($uids);
        foreach ($list as $k => $v) {
            $list[$k]['client'] = $apps[$v['appid']]['name'];
            $list[$k]['fromclient'] = $notifys[$v['nid']]['appid'] == 0 ? 'server' : $apps[$notifys[$v['nid']]['appid']]['name'];
            $operation = $notifys[$v['nid']]['operation'];
            $param = unserialize($notifys[$v['nid']]['param']);
            $uid = $param['uid'];
            $username = $users[$uid]['username'];
            $operation = $this->getAlias($operation);
            $operation = sprintf($operation, $username);
            $list[$k]['operation'] = $operation;
            $list[$k]['time'] = $notifys[$v['nid']]['timestamp'];
        }
        $totalPage = ceil($count / $perPage);
        $page > $totalPage && $page = $totalPage;
        $clientid && $args['clientid'] = $clientid;
        isset($complete) && $args['complete'] = $complete;
        $this->setOutput($args, 'args');
        $this->setOutput($page, 'page');
        $this->setOutput($perPage, 'perPage');
        $this->setOutput($count, 'count');
        $this->setOutput($list, 'list');
        $this->setOutput($apps, 'apps');
    }


    public function clearAction()
    {
        $perPage = 100;
        $count = $this->_getLogDs()->countList(0, 0, 0);
        $totalPage = ceil($count / $perPage);
        $logDs = $this->_getLogDs();
        $nDs = $this->_getNotifyDs();
        $nids = array();
        for ($page = 1; $page <= $totalPage; $page++) {
            list($start, $limit) = Pw::page2limit($page, $perPage);
            $list = $logDs->getList(0, 0, $limit, $start, 0);
            foreach ($list as $k => $v) {
                $nids[] = $v['nid'];
            }
        }
        $this->_getLogDs()->deleteComplete();
        $this->_getNotifyDs()->batchNotDelete($nids);
        $this->showMessage('WINDID:success', 'windid/notify/run');
    }

    public function resendAction()
    {
        $logid = (int) $this->getInput('logid', 'get');
        if ($this->_getNotifyService()->logSend($logid)) {
            $this->showMessage('ADMIN:success');
        }
        $this->showError('ADMIN:fail');
    }

    protected function getAlias($operation)
    {
        $config = include Wind::getRealPath('WSRV:base.WindidNotifyConf.php', true);

        return isset($config[$operation]['alias']) ? $config[$operation]['alias'] : '';
    }

    private function _getAppDs()
    {
        return Wekit::load('WSRV:app.WindidApp');
    }

    private function _getUserDs()
    {
        return Wekit::load('WSRV:user.WindidUser');
    }

    private function _getLogDs()
    {
        return Wekit::load('WSRV:notify.WindidNotifyLog');
    }

    private function _getNotifyDs()
    {
        return Wekit::load('WSRV:notify.WindidNotify');
    }

    private function _getNotifyService()
    {
        return Wekit::load('WSRV:notify.srv.WindidNotifyServer');
    }
}
