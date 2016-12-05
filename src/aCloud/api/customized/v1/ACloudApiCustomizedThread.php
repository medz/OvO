<?php

!defined('ACLOUD_PATH') && exit('Forbidden');
require_once Wind::getRealPath('ACLOUD_VER:customized.ACloudVerCustomizedFactory');
class ACloudApiCustomizedThread
{
    public function getByTid($tid)
    {
        return $this->getVersionCustomizedThread()->getByTid($tid);
    }

    public function getByUid($uid, $offset, $limit)
    {
        return $this->getVersionCustomizedThread()->getByUid($uid, $offset, $limit);
    }

    public function getLatestThread($fids, $offset, $limit)
    {
        return $this->getVersionCustomizedThread()->getLatestThread($fids, $offset, $limit);
    }

    public function getLatestThreadByFavoritesForum($uid, $offset, $limit)
    {
        return $this->getVersionCustomizedThread()->getLatestThreadByFavoritesForum($uid, $offset, $limit);
    }

    public function getLatestThreadByFollowUser($uid, $offset, $limit)
    {
        return $this->getVersionCustomizedThread()->getLatestThreadByFollowUser($uid, $offset, $limit);
    }

    public function getLatestImgThread($fids, $offset, $limit)
    {
        return $this->getVersionCustomizedThread()->getLatestImgThread($fids, $offset, $limit);
    }

    public function getThreadImgs($tid)
    {
        return $this->getVersionCustomizedThread()->getThreadImgs($tid);
    }

    public function getToppedThreadByFid($fid, $offset, $limit)
    {
        return $this->getVersionCustomizedThread()->getToppedThreadByFid($fid, $offset, $limit);
    }

    public function getThreadByFid($fid, $offset, $limit)
    {
        return $this->getVersionCustomizedThread()->getThreadByFid($fid, $offset, $limit);
    }

    public function getAtThreadByUid($uid, $offset, $limit)
    {
        return $this->getVersionCustomizedThread()->getAtThreadByUid($uid, $offset, $limit);
    }

    public function getThreadByTopic($topic, $offset, $limit)
    {
        return $this->getVersionCustomizedThread()->getThreadByTopic($topic, $offset, $limit);
    }

    public function getByTidAndUid($tid, $uid, $page, $offset = 0, $limit = 10)
    {
        return $this->getVersionCustomizedThread()->getByTidAndUid($tid, $uid, $page, $offset = 0, $limit = 10);
    }

    public function postThread($uid, $fid, $subject, $content)
    {
        return $this->getVersionCustomizedThread()->postThread($uid, $fid, $subject, $content);
    }

    private function getVersionCustomizedThread()
    {
        return ACloudVerCustomizedFactory::getInstance()->getVersionCustomizedThread();
    }
}
