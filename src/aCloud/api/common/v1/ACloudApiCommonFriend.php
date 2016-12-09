<?php

!defined('ACLOUD_PATH') && exit('Forbidden');
require_once Wind::getRealPath('ACLOUD_VER:common.ACloudVerCommonFactory');
class ACloudApiCommonFriend
{
    public function getAllFriend($uid, $offset, $limit)
    {
        return $this->getVersionCommonFriend()->getAllFriend($uid, $offset, $limit);
    }

    public function searchAllFriend($uid, $keyword, $offset, $limit)
    {
        return $this->getVersionCommonFriend()->searchAllFriend($uid, $keyword, $offset, $limit);
    }

    public function getFollowByUid($uid, $offset, $limit)
    {
        return $this->getVersionCommonFriend()->getFollowByUid($uid, $offset, $limit);
    }

    public function addFollowByUid($uid, $touid)
    {
        return $this->getVersionCommonFriend()->addFollowByUid($uid, $touid);
    }

    public function deleteFollowByUid($uid, $touid)
    {
        return $this->getVersionCommonFriend()->deleteFollowByUid($uid, $touid);
    }

    public function getFanByUid($uid, $offset, $limit)
    {
        return $this->getVersionCommonFriend()->getFanByUid($uid, $offset, $limit);
    }

    private function getVersionCommonFriend()
    {
        return ACloudVerCommonFactory::getInstance()->getVersionCommonFriend();
    }
}
