<?php

!defined('ACLOUD_PATH') && exit('Forbidden');
require_once Wind::getRealPath('ACLOUD_VER:customized.ACloudVerCustomizedFactory');
class ACloudApiCustomizedUser
{
    public function getByUid($uid)
    {
        return $this->getVersionCustomizedUser()->getByUid($uid);
    }

    public function getByName($username)
    {
        return $this->getVersionCustomizedUser()->getByName($username);
    }

    public function updateIcon($uid)
    {
        return $this->getVersionCustomizedUser()->updateIcon($uid);
    }

    public function getFavoritesForumByUid($uid)
    {
        return $this->getVersionCustomizedUser()->getFavoritesForumByUid($uid);
    }

    public function addFavoritesForumByUid($uid, $fid)
    {
        return $this->getVersionCustomizedUser()->addFavoritesForumByUid($uid, $fid);
    }

    public function deleteFavoritesForumByUid($uid, $fid)
    {
        return $this->getVersionCustomizedUser()->deleteFavoritesForumByUid($uid, $fid);
    }

    public function userLogin($username, $password)
    {
        return $this->getVersionCustomizedUser()->userLogin($username, $password);
    }

    public function userRegister($username, $password, $email)
    {
        return $this->getVersionCustomizedUser()->userRegister($username, $password, $email);
    }

    public function updateEmail($uid, $email)
    {
        return $this->getVersionCustomizedUser()->updateEmail($uid, $email);
    }

    public function checkCookie($cookie)
    {
        return $this->getVersionCustomizedUser()->checkCookie($cookie);
    }

    public function getUserBindInfo($uid, $type)
    {
        return $this->getVersionCustomizedUser()->getUserBindInfo($uid, $type);
    }

    private function getVersionCustomizedUser()
    {
        return ACloudVerCustomizedFactory::getInstance()->getVersionCustomizedUser();
    }
}
