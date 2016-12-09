<?php

Wind::import('APPS:api.controller.OpenBaseController');

/**
 * the last known user to change this file in the repository  <$LastChangedBy: jieyin $>.
 *
 * @author $Author: jieyin $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: MessageController.php 24706 2013-02-16 06:02:32Z jieyin $
 */
class MessageController extends OpenBaseController
{
    public function getMessageByIdAction()
    {
        $result = $this->_getMessageDs()->getMessageById($this->getInput('messageId', 'get'));
        $this->output($result);
    }

    public function getNumAction()
    {
        $result = $this->_getMessageService()->getUnRead($this->getInput('uid', 'get'));
        $this->output($result);
    }

    public function countMessageAction()
    {
        $result = $this->_getMessageDs()->countRelation($this->getInput('dialogId', 'get'));
        $this->output($result);
    }

    public function getMessageListAction()
    {
        $dialogId = $this->getInput('dialogId', 'get');
        $start = (int) $this->getInput('start', 'get');
        $limit = $this->getInput('limit', 'get');
        !$limit && $limit = 10;
        $result = $this->_getMessageDs()->getDialogMessages($dialogId, $start, $limit);
        $this->output($result);
    }

    public function getDialogAction()
    {
        $result = $this->_getMessageDs()->getDialog($this->getInput('dialogId', 'get'));
        $this->output($result);
    }

    public function fetchDialogAction()
    {
        $result = $this->_getMessageDs()->fetchDialog($this->getInput('dialogIds', 'get'));
        $this->output($result);
    }

    public function getDialogByUserAction()
    {
        $uid = $this->getInput('uid', 'get');
        $dialogUid = $this->getInput('dialogUid', 'get');
        $result = $this->_getMessageDs()->getDialogByUid($uid, $dialogUid);
        $this->output($result);
    }

    public function getDialogByUsersAction()
    {
        $uid = $this->getInput('uid', 'get');
        $dialogUids = $this->getInput('dialogUids', 'get');
        $result = $this->_getMessageDs()->getDialogByUids($uid, $dialogUids);
        $this->output($result);
    }

    public function getDialogListAction()
    {
        $uid = $this->getInput('uid', 'get');
        $start = (int) $this->getInput('start', 'get');
        $limit = (int) $this->getInput('limit', 'get');
        !$limit && $limit = 10;
        $result = $this->_getMessageDs()->getDialogs($uid, $start, $limit);
        $this->output($result);
    }

    public function countDialogAction()
    {
        $uid = (int) $this->getInput('uid', 'get');
        $result = $this->_getMessageDs()->countDialogs($uid);
        $this->output($result);
    }

    /**
     * 搜索消息.
     *
     * @return array(count, list)
     */
    public function searchMessageAction()
    {
        $start = (int) $this->getInput('start', 'get');
        $limit = (int) $this->getInput('limit', 'get');
        list($fromuid, $keyword, $username, $starttime, $endtime) = $this->getInput(array('fromuid', 'keyword', 'username', 'starttime', 'endtime'), 'get');

        $search = array();
        isset($fromuid) && $search['fromuid'] = $fromuid;
        isset($keyword) && $search['keyword'] = $keyword;
        isset($username) && $search['username'] = $username;
        isset($starttime) && $search['starttime'] = $starttime;
        isset($endtime) && $search['endtime'] = $endtime;
        !$limit && $limit = 10;
        $result = $this->_getMessageService()->searchMessage($search, $start, $limit);
        $this->output($result);
    }

    public function editNumAction()
    {
        $uid = (int) $this->getInput('uid', 'post');
        $num = (int) $this->getInput('num', 'post');
        $result = $this->_getMessageService()->editMessageNum($uid, $num);
        $this->_getNotifyService()->send('editMessageNum', array('uid' => $uid), $this->appid);
        $this->output(WindidUtility::result($result));
    }

    public function sendAction()
    {
        $uids = $this->getInput('uids', 'post');
        $content = $this->getInput('content', 'post');
        $fromUid = $this->getInput('fromUid', 'post');

        is_array($uids) || $uids = array($uids);
        $result = $this->_getMessageService()->sendMessageByUids($uids, $content, $fromUid);
        if ($result instanceof WindidError) {
            $this->output($result->getCode());
        }
        $srv = $this->_getNotifyService();
        foreach ($uids as $uid) {
            $srv->send('editMessageNum', array('uid' => $uid), $this->appid);
        }
        $this->output(WindidUtility::result($result));
    }

    public function readAction()
    {
        $messageIds = $this->getInput('messageIds', 'post');
        $dialogId = $this->getInput('dialogId', 'post');
        $uid = $this->getInput('uid', 'post');
        $result = $this->_getMessageService()->read($uid, $dialogId, $messageIds);
        if ($result) {
            $this->_getNotifyService()->send('editMessageNum', array('uid' => $uid), $this->appid);
        }
        $this->output($result);
    }

    public function readDialogAction()
    {
        $result = $this->_getMessageService()->readDialog($this->getInput('dialogIds', 'post'));
        $ds = $this->_getMessageDs();
        foreach ($dialogIds as $id) {
            $dialog = $ds->getDialog($id);
            $this->_getNotifyService()->send('editMessageNum', array('uid' => $dialog['to_uid']), $this->appid);
        }
        $this->output(WindidUtility::result($result));
    }

    public function deleteAction()
    {
        $messageIds = $this->getInput('messageIds', 'post');
        $dialogId = $this->getInput('dialogId', 'post');
        $uid = $this->getInput('uid', 'post');
        $result = $this->_getMessageService()->delete($uid, $dialogId, $messageIds);
        if ($result) {
            $this->_getNotifyService()->send('editMessageNum', array('uid' => $uid), $this->appid);
        }
        $this->output(WindidUtility::result($result));
    }

    public function batchDeleteDialogAction()
    {
        $dialogIds = $this->getInput('dialogIds', 'post');
        $uid = $this->getInput('uid', 'post');
        $result = $this->_getMessageService()->batchDeleteDialog($uid, $dialogIds);
        $this->_getNotifyService()->send('editMessageNum', array('uid' => $uid), $this->appid);
        $this->output(WindidUtility::result($result));
    }

    public function deleteByMessageIdsAction()
    {
        $result = $this->_getMessageService()->deleteByMessageIds($this->getInput('messageIds', 'post'));
        $this->output(WindidUtility::result($result));
    }

    public function deleteUserMessagesAction()
    {
        $uid = (int) $this->getInput('uid', 'post');
        $result = $this->_getMessageService()->deleteUserMessages($uid);
        $this->_getNotifyService()->send('editMessageNum', array('uid' => $uid), $this->appid);
        $this->output(WindidUtility::result($result));
    }

    /********************** 传统收件箱，发件箱接口start *********************/

    public function fromBox()
    {
        $uid = (int) $this->getInput('uid', 'get');
        $start = (int) $this->getInput('start', 'get');
        $limit = (int) $this->getInput('limit', 'get');
        !$limit && $limit = 10;
        !$start && $start = 0;
        $result = $this->_getBoxMessage()->fromBox($uid, $start, $limit);
        $this->output($result);
    }

    public function toBox()
    {
        $uid = (int) $this->getInput('uid', 'get');
        $start = (int) $this->getInput('start', 'get');
        $limit = (int) $this->getInput('limit', 'get');
        !$limit && $limit = 10;
        !$start && $start = 0;
        $result = $this->_getBoxMessage()->toBox($uid, $start, $limit);
        $this->output($result);
    }

    public function readMessages()
    {
        $uid = (int) $this->getInput('uid', 'post');
        $messageIds = $this->getInput('messageIds', 'post');
        if (!is_array($messageIds)) {
            $messageIds = array($messageIds);
        }
        $result = $this->_getBoxMessage()->readMessages($uid, $messageIds);
        $this->output(WindidUtility::result($result));
    }

    public function deleteMessages()
    {
        $uid = (int) $this->getInput('uid', 'post');
        $messageIds = $this->getInput('messageIds', 'post');
        if (!is_array($messageIds)) {
            $messageIds = array($messageIds);
        }
        $result = $this->_getBoxMessage()->deleteMessages($uid, $messageIds);
        $this->output(WindidUtility::result($result));
    }

    /********************** 传统收件箱，发件箱接口end *********************/

    private function _getMessageDs()
    {
        return Wekit::load('WSRV:message.WindidMessage');
    }

    private function _getMessageService()
    {
        return Wekit::load('WSRV:message.srv.WindidMessageService');
    }

    private function _getBoxMessage()
    {
        return Wekit::load('WSRV:message.srv.WindidBoxMessage');
    }

    private function _getNotifyService()
    {
        return Wekit::load('WSRV:notify.srv.WindidNotifyService');
    }
}
