<?php

!defined('ACLOUD_PATH') && exit('Forbidden');

define('MESSAGE_INVALID_PARAMS', 601);
define('MESSAGE_UID_ERROR', 602);
define('MESSAGE_SEND_FAIL', 603);

class ACloudVerCustomizedMessage extends ACloudVerCustomizedBase
{
    /**
     * 统计用户未读消息.
     *
     * @param int $uid
     *
     * @return int
     */
    public function countUnreadMessage($uid)
    {
        if ($uid < 1) {
            return $this->buildResponse(MESSAGE_UID_ERROR, '用户ID错误');
        }
        $user = new PwUserBo(intval($uid));
        if (empty($user)) {
            return $this->buildResponse(MESSAGE_UID_ERROR, '用户ID错误');
        }
        $result = $user->info['messages'];

        return $this->buildResponse(0, array('count' => intval($result)));
    }

    /**
     * 获取用户对应的对话框列表.
     *
     * @param int $uid
     * @param int $start
     * @param int $limit
     *
     * @return array
     */
    public function getMessageByUid($uid, $offset, $limit)
    {
        $user = PwUserBo::getInstance($uid);
        if (!$user->isExists()) {
            return $this->buildResponse(MESSAGE_UID_ERROR, '用户ID错误');
        }
        list($count, $result) = $this->getPwMessageService()->getDialogs($uid, $offset, $limit);
        if ($result instanceof PwError) {
            return $this->buildResponse(-1, $result->getError());
        }
        $message = array();
        foreach ($result as $k => $v) {
            $message[$k]['dialog_id'] = $v['dialog_id'];
            $message[$k]['uid'] = $v['from_uid'];
            $message[$k]['username'] = PwUserBo::getInstance($v['uid'])->username;
            $message[$k]['icon'] = Pw::getAvatar($v['uid']);
            $message[$k]['to_uid'] = $v['last_message']['to_uid'];
            $message[$k]['unread_count'] = $v['unread_count'];
            $message[$k]['message_count'] = $v['message_count'];
            $message[$k]['last_message'] = $v['last_message'];
        }

        return $this->buildResponse(0, array('count' => $count, 'messages' => $message));
    }

    public function getReplyThreadMessage($uid, $offset, $limit)
    {
    }

    /**
     * 按用户ID发送私信
     *
     * @param int    $uid
     * @param string $content
     *
     * @return PwError|bool
     */
    public function sendMessage($fromUid, $toUid, $content)
    {
        list($fromUid, $toUid, $content) = array(intval($fromUid), intval($toUid), trim($content));
        if ($fromUid < 1 || $toUid < 1 || !$content) {
            return $this->buildResponse(MESSAGE_INVALID_PARAMS, '发送消息接口错误');
        }
        $result = $this->getPwMessageService()->sendMessageByUid($toUid, $content, $fromUid);
        if ($result instanceof PwError) {
            return $this->buildResponse(-1, $result->getError());
        }
        $dialog = $this->_getMessageDs()->getDialogByUser($fromUid, $toUid);

        return $this->buildResponse(0, array('dialogid' => $dialog['dialog_id']));
    }

    public function replyMessage($messageId, $relationId, $uid, $content)
    {
    }

    /**
     * 根据消息id获取一条消息.
     *
     * @param int messageId
     *
     * @return array
     */
    public function getMessageById($messageId)
    {
        $messageId = intval($messageId);
        if ($messageId < 1) {
            return $this->buildResponse(MESSAGE_INVALID_PARAMS, '参数错误');
        }
        $message = $this->_getMessageDs()->getMessageById($messageId);

        return $this->buildResponse(0, array('message' => $message));
    }

    /**
     * 获取对话消息列表.
     *
     * @param int $dialogId
     * @param int $start
     * @param int $limit
     *
     * @return array
     */
    public function getMessageAndReply($dialogId, $offset, $limit)
    {
        list($dialogId, $offset, $limit) = array(intval($dialogId), intval($offset), intval($limit));
        if ($dialogId < 1) {
            return $this->buildResponse(MESSAGE_INVALID_PARAMS, '参数错误');
        }
        list($count, $dialogResult) = $this->getPwMessageService()->getDialogMessageList($dialogId, $limit, $offset);
        if ($dialogResult instanceof PwError) {
            return $this->buildResponse(-1, $dialogResult->getError());
        }
        $result = array();
        $dialogResult = array_values($dialogResult);
        foreach ($dialogResult as $k => $v) {
            $result[$k]['messageid'] = $v['message_id'];
            $result[$k]['uid'] = $v['from_uid'];
            $result[$k]['username'] = PwUserBo::getInstance($v['uid'])->username;
            $result[$k]['icon'] = Pw::getAvatar($v['from_uid']);
            $result[$k]['postdate'] = $v['created_time'];
            $result[$k]['content'] = $v['content'];
            $result[$k]['id'] = $v['id'];    //dialog和message的关系id
            $result[$k]['dialog_id'] = $dialogId;
            $result[$k]['is_read'] = $v['is_read'];
            $result[$k]['from_username'] = $v['from_username'];
        }

        return $this->buildResponse(0, array('count' => $count, 'dialog' => $result));
    }

    /**
     * 发送通知接口.
     *
     * @param int    $uid         [description]
     * @param sarray $usernames   [description]
     * @param array  $messageInfo [description]
     * @param int    $typeid      array(
     *                            'message' => 1,
     *                            'default' => 2,
     *                            'threadmanage'	=> 3,
     *                            'medal'	=> 4,
     *                            'task' => 5,
     *                            'massmessage' => 6,
     *                            'report_thread' => 7,
     *                            'report_post' => 8,
     *                            'report_message' => 9,
     *                            'threadreply' 	=> 10,
     *                            'attention' 	=> 11,
     *                            'remind' 	=> 12,
     *                            'ban' => 13,
     *                            'credit' => 14,
     *                            'postreply' => 15,
     *                            'report_photo' => 16,
     *                            )
     *
     * @return
     */
    public function sendNotice($uid, $usernames, $messageInfo, $typeid)
    {
        $extendParams = array('title' => $messageInfo['title'], 'content' => $messageInfo['content']);
        $result = $this->getPwNoticeService()->sendNotice($uid, 'app', 0, $extendParams);
        if ($result) {
            return $this->buildResponse(0);
        }

        return $this->buildResponse(-1);
    }

    public function sendFreshStat($uid, $content, $type)
    {
        $userBo = new PwUserBo($uid);

        $dm = new PwWeiboDm();
        $dm->setContent($content);

        $weiboService = new PwSendWeibo($userBo);
        $result = $weiboService->send($dm);
        if ($result) {
            return $this->buildResponse(0);
        }

        return $this->buildResponse(-1);
    }

    private function getPwNoticeService()
    {
        return Wekit::load('SRV:message.srv.PwNoticeService');
    }

    private function getUserDS()
    {
        return Wekit::load('SRV:user.PwUser');
    }

    private function getPwMessageService()
    {
        return Wekit::load('SRV:message.srv.PwMessageService');
    }

    /**
     * @return WindidMessage
     */
    private function _getMessageDs()
    {
        return WindidApi::api('message');
    }
}
