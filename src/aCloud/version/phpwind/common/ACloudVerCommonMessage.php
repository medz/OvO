<?php

!defined('ACLOUD_PATH') && exit('Forbidden');

define('MESSAGE_INVALID_PARAMS', 601);
define('MESSAGE_UID_ERROR', 602);
define('MESSAGE_SEND_FAIL', 603);

class ACloudVerCommonMessage extends ACloudVerCommonBase
{
    /**
     * 统计用户未读通知.
     *
     * @param int $uid
     *
     * @return int
     */
    public function countUnreadMessage($uid)
    {
        $user = new PwUserBo(intval($uid));
        $result = $user->info['messages'];
        if (!$result) {
            return $this->buildResponse(MESSAGE_UID_ERROR);
        }

        return $this->buildResponse(0, $result);
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
        $user = new PwUserBo($uid);
        if (!$user->isExists()) {
            return $this->buildResponse(MESSAGE_UID_ERROR);
        }
        $result = $this->getPwMessageService()->getDialogs($uid, $offset, $limit);
        if ($result instanceof PwError) {
            return $this->buildResponse(-1, $result->getError());
        }

        return $this->buildResponse(0, $result);
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
    public function sendMessage($fromUid, $toUid, $title, $content)
    {
        $result = $this->getPwMessageService()->sendMessageByUid($toUid, $content, $fromUid);
        if ($result instanceof PwError) {
            return $this->buildResponse(-1, $result->getError());
        }

        return $this->buildResponse(0, $result);
    }

    public function replyMessage($messageId, $relationId, $uid, $content)
    {
    }

    /**
     * 获取对话消息列表.
     *
     * @param int $messageId
     * @param int $start
     * @param int $limit
     *
     * @return array
     */
    public function getMessageAndReply($messageId, $relationId, $uid, $offset, $limit)
    {
        $result = $this->getPwMessageService()->getDialogMessageList($messageId, $offset, $limit);
        if ($result instanceof PwError) {
            return $this->buildResponse(-1, $result->getError());
        }

        return $this->buildResponse(0, $result);
    }

    private function getPwMessageService()
    {
        return wekit::load('SRV:message.srv.PwMessageService');
    }
}
