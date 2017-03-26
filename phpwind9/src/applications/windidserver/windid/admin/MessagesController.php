<?php

Wind::import('APPS:windid.admin.WindidBaseController');
/**
 * the last known user to change this file in the repository  <$LastChangedBy: jieyin $>.
 *
 * @author $Author: jieyin $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: MessagesController.php 23833 2013-01-16 06:41:42Z jieyin $
 */
class MessagesController extends WindidBaseController
{
    private $perpage = 20;
    private $perstep = 10;

    public function run()
    {
        list($page, $perpage, $username, $starttime, $endtime, $keyword) = $this->getInput(['page', 'perpage', 'username', 'starttime', 'endtime', 'keyword']);
        $starttime && $pwStartTime = Pw::str2time($starttime);
        $endtime && $pwEndTime = Pw::str2time($endtime);
        $page = $page ? $page : 1;
        $perpage = $perpage ? $perpage : $this->perpage;
        list($start, $limit) = Pw::page2limit($page, $perpage);
        if ($username) {
            $userinfo = $this->_getUserDs()->getUserByName($username);
            $fromUid = $userinfo['uid'] ? $userinfo['uid'] : 0;
        }
        Wind::import('WSRV:message.srv.vo.WindidMessageSo');
        $vo = new WindidMessageSo();
        $endtime && $vo->setEndTime($endtime);
        $fromUid && $vo->setFromUid($fromUid);
        $keyword && $vo->setKeyword($keyword);
        $starttime && $vo->setStarttime($starttime);
        $messages = $this->_getMessageDs()->searchMessage($vo, $start, $limit);
        $count = $this->_getMessageDs()->countMessage($vo);
        foreach ($messages as $k => $v) {
            $uids[] = $v['from_uid'];
        }
        $users = $this->_getUserDs()->fetchUserByUid($uids);
        foreach ($messages as $k => $v) {
            $messages[$k]['username'] = $users[$v['from_uid']]['username'];
        }

        $this->setOutput($count, 'count');
        $this->setOutput($page, 'page');
        $this->setOutput($perpage, 'perpage');
        $this->setOutput(['keyword' => $keyword, 'username' => $username, 'starttime' => $starttime, 'endtime' => $endtime], 'args');
        $this->setOutput($messages, 'messages');
    }

    /**
     * 删除消息.
     */
    public function deleteMessagesAction()
    {
        $ids = $this->getInput('ids', 'post');
        if (! $ids) {
            $this->showError('WINDID:fail');
        }
        $this->_getMessageService()->deleteByMessageIds($ids);

        $this->showMessage('WINDID:success');
    }

    private function _getMessageService()
    {
        return Wekit::load('WSRV:message.srv.WindidMessageService');
    }

    private function _getMessageDs()
    {
        return Wekit::load('WSRV:message.WindidMessage');
    }

    /**
     * Enter description here ...
     *
     * @return PwUser
     */
    private function _getUserDs()
    {
        return Wekit::load('WSRV:user.WindidUser');
    }
}
