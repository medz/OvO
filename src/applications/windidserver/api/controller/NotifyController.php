<?php

Wind::import('APPS:api.controller.OpenBaseController');
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: NotifyController.php 24579 2013-02-01 03:26:06Z gao.wanggao $
 * @package
 */
class NotifyController extends OpenBaseController
{
    public function fetchAction()
    {
        $result = $this->_getNotifyDs()->fetchNotify($this->getInput('nids', 'get'));
        $this->output($result);
    }

    public function batchNotDeleteAction()
    {
        $result = $this->_getNotifyDs()->batchNotDelete($this->getInput('nids', 'post'));
        $this->output($result);
    }

    public function getlogListAction()
    {
        $appid = (int) $this->getInput('appid', 'get');
        $nid = (int) $this->getInput('nid', 'get');
        $limit = (int) $this->getInput('limit', 'get');
        $start = (int) $this->getInput('start', 'get');
        $completet = $this->getInput('completet', 'get');

        $result = $this->_getNotifyLogDs()->getList($appid, $nid, $limit, $start, $complete);
        $this->output($result);
    }

    public function countLogListAction()
    {
        $appid = (int) $this->getInput('appid', 'get');
        $nid = (int) $this->getInput('nid', 'get');
        $completet = $this->getInput('completet', 'get');
        $result = $this->_getNotifyLogDs()->countList($appid, $nid, $complete);
        $this->output($result);
    }

    public function deleteLogCompleteAction()
    {
        $result = $this->_getNotifyLogDs()->deleteComplete();
        $this->output($result);
    }

    public function deleteLogAction()
    {
        $result = $this->_getNotifyLogDs()->deleteLog($this->getInput('logid', 'post'));
        $this->output($result);
    }

    public function logSendAction($logid)
    {
        $result = $this->_getNotifyService()->logSend($this->getInput('logid', 'post'));
        $this->output($result);
    }

    private function _getNotifyDs()
    {
        return Wekit::load('WSRV:notify.WindidNotify');
    }

    private function _getNotifyLogDs()
    {
        return Wekit::load('WSRV:notify.WindidNotifyLog');
    }

    private function _getNotifyService()
    {
        return Wekit::load('WSRV:notify.srv.WindidNotifyServer');
    }
}
