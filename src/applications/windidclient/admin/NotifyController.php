<?php

Wind::import('ADMIN:library.AdminBaseController');

/**
 * the last known user to change this file in the repository  <$LastChangedBy: jieyin $>
 * @author $Author: jieyin $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: NotifyController.php 28921 2013-05-30 07:50:23Z jieyin $
 * @package
 */
class NotifyController extends AdminBaseController
{
    public function run()
    {
        $perPage = 10;
        $uids = $appids = $nids = array();
        list($clientid, $complete, $page) = $this->getInput(array('clientid', 'complete', 'page'));
        $page = $page > 1 ? $page : 1;
        $complete = ($complete === '') ? null : $complete;
        list($start, $limit) = Pw::page2limit($page, $perPage);
        $list = $this->_getNotifyDs()->getlogList($clientid, 0, $limit,  $start, $complete);
        $count = $this->_getNotifyDs()->countLogList($clientid, 0, $complete);
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
        $users = $this->_getUserDs()->fecthUser($uids);
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
        $nDs = $this->_getNotifyDs();
        $perPage = 100;
        $count = $nDs->countLogList(0, 0, 0);
        $totalPage = ceil($count / $perPage);
        $nids = array();
        for ($page = 1; $page <= $totalPage; $page++) {
            list($start, $limit) = Pw::page2limit($page, $perPage);
            $list = $nDs->getLogList(0, 0, $limit,  $start, 0);
            foreach ($list as $k => $v) {
                $nids[] = $v['nid'];
            }
        }
        $nDs->deleteLogComplete();
        $nDs->batchNotDelete($nids);
        $this->showMessage('WINDID:success');
    }

    public function resendAction()
    {
        $logid = (int) $this->getInput('logid', 'get');
        if ($this->_getNotifyDs()->logSend($logid)) {
            $this->showMessage('ADMIN:success');
        }
        $this->showError('ADMIN:fail');
    }

    public function deleteAction()
    {
        $logid = (int) $this->getInput('logid', 'post');
        if (!$logid) {
            $this->showError('operate.fail');
        }
        if ($this->_getNotifyDs()->deleteLog($logid)) {
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
        return WindidApi::api('app');
    }

    private function _getUserDs()
    {
        return WindidApi::api('user');
    }

    private function _getNotifyDs()
    {
        return WindidApi::api('notify');
    }
}
