<?php

! defined('ACLOUD_PATH') && exit('Forbidden');
define('POST_INVALID_PARAMS', 301);
class ACloudApiCommonMessage
{
    public function countUnreadMessage($uid)
    {
        return $this->getVersionCommonMessage()->countUnreadMessage($uid);
    }

    public function getMessageByUid($uid, $offset, $limit)
    {
        return $this->getVersionCommonMessage()->getMessageByUid($uid, $offset, $limit);
    }

    public function getReplyThreadMessage($uid, $offset, $limit)
    {
        return $this->getVersionCommonMessage()->getReplyThreadMessage($uid, $offset, $limit);
    }

    public function sendMessage($fromUid, $toUid, $title, $content)
    {
        return $this->getVersionCommonMessage()->sendMessage($fromUid, $toUid, $title, $content);
    }

    public function replyMessage($messageId, $relationId, $uid, $content)
    {
        return $this->getVersionCommonMessage()->replyMessage($messageId, $relationId, $uid, $content);
    }

    public function getMessageAndReply($messageId, $relationId, $uid, $offset, $limit)
    {
        return $this->getVersionCommonMessage()->getMessageAndReply($messageId, $relationId, $uid, $offset, $limit);
    }

    private function getVersionCommonMessage()
    {
        return ACloudVerCommonFactory::getInstance()->getVersionCommonMessage();
    }
}
