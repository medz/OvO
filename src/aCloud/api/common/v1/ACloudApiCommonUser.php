<?php

!defined('ACLOUD_PATH') && exit('Forbidden');
require_once Wind::getRealPath('ACLOUD_VER:common.ACloudVerCommonFactory');
class ACloudApiCommonUser
{
    public function getByUid($uid)
    {
        return $this->getVersionCommonUser()->getByUid($uid);
    }

    public function getByName($username)
    {
        return $this->getVersionCommonUser()->getByName($username);
    }

    public function updateIcon($uid)
    {
        return $this->getVersionCommonUser()->updateIcon($uid);
    }

    public function banUser($uid)
    {
        return $this->getVersionCommonUser()->banUser($uid);
    }

    public function getFavoritesForumByUid($uid)
    {
        return $this->getVersionCommonUser()->getFavoritesForumByUid($uid);
    }

    public function addFavoritesForumByUid($uid, $fid)
    {
        return $this->getVersionCommonUser()->addFavoritesForumByUid($uid, $fid);
    }

    public function deleteFavoritesForumByUid($uid, $fid)
    {
        return $this->getVersionCommonUser()->deleteFavoritesForumByUid($uid, $fid);
    }

    public function userLogin($username, $password)
    {
        return $this->getVersionCommonUser()->userLogin($username, $password);
    }

    public function userRegister($username, $password, $email)
    {
        return $this->getVersionCommonUser()->userRegister($username, $password, $email);
    }

    public function updateEmail($uid, $email)
    {
        return $this->getVersionCommonUser()->updateEmail($uid, $email);
    }

    private function getVersionCommonUser()
    {
        return ACloudVerCommonFactory::getInstance()->getVersionCommonUser();
    }
}
