<?php

! defined('ACLOUD_PATH') && exit('Forbidden');
require_once Wind::getRealPath('ACLOUD_VER:customized.ACloudVerCustomizedFactory');
class ACloudApiCustomizedPost
{
    public function getPost($tid, $sort, $offset, $limit)
    {
        return $this->getVersionCustomizedPost()->getPost($tid, $sort, $offset, $limit);
    }

    public function getPostByUid($uid, $offset, $limit)
    {
        return $this->getVersionCustomizedPost()->getPostByUid($uid, $offset, $limit);
    }

    public function getPostByTidAndUid($tid, $uid, $offset, $limit)
    {
        return $this->getVersionCustomizedPost()->getPostByTidAndUid($tid, $uid, $offset, $limit);
    }

    public function getLatestPost($tid, $page, $offset, $limit)
    {
        return $this->getVersionCustomizedPost()->getLatestPost($tid, $page, $offset, $limit);
    }

    public function sendPost($tid, $uid, $title, $content)
    {
        return $this->getVersionCustomizedPost()->sendPost($tid, $uid, $title, $content);
    }

    public function checkSensitiveWord($word)
    {
        return $this->getVersionCustomizedPost()->checkSensitiveWord($word);
    }

    private function getVersionCustomizedPost()
    {
        return ACloudVerCustomizedFactory::getInstance()->getVersionCustomizedPost();
    }
}
