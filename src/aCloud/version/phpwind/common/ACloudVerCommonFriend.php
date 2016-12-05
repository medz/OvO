<?php

!defined('ACLOUD_PATH') && exit('Forbidden');

define('Friend_INVALID_PARAMS', 101);
define('Friend_NOT_EXISTS', 102);
define('Friend_ALREADY_FOLLOWED', 103);
define('Friend_FOLLOWED_BLACKLIST', 104);
define('Friend_NOT_FOLLOWED', 105);

class ACloudVerCommonFriend extends ACloudVerCommonBase
{
    public function getAllFriend($uid, $offset, $limit)
    {
    }

    public function searchAllFriend($uid, $keyword, $offset, $limit)
    {
    }

    public function getFollowByUid($uid, $offset, $limit)
    {
        $result = $this->getAttention()->getFollows($uid, $offset, $limit);
        if ($result instanceof PwError) {
            return $this->buildResponse(-1, $result->getError());
        }

        return $this->buildResponse(0, $result);
    }

    public function addFollowByUid($uid, $touid)
    {
        $result = $this->getAttentionService()->addFollow($uid, $touid);
        if ($result instanceof PwError) {
            return $this->buildResponse(-1, $result->getError());
        }

        return $this->buildResponse(0, $result);
    }

    public function deleteFollowByUid($uid, $touid)
    {
        $result = $this->getAttentionService()->deleteFollow($uid, $touid);
        if ($result instanceof PwError) {
            return $this->buildResponse(-1, $result->getError());
        }

        return $this->buildResponse(0, $result);
    }

    public function getFanByUid($uid, $offset, $limit)
    {
        $result = $this->getAttention()->getFans($uid, $offset, $limit);
        if ($result instanceof PwError) {
            return $this->buildResponse(-1, $result->getError());
        }

        return $this->buildResponse(0, $result);
    }

    private function getAttention()
    {
        return Wekit::load('SRV:attention.PwAttention');
    }

    private function getAttentionService()
    {
        return Wekit::load('SRV:attention.srv.PwAttentionService');
    }
}
