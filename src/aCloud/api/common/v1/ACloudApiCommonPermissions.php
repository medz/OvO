<?php

!defined('ACLOUD_PATH') && exit('Forbidden');
require_once Wind::getRealPath('ACLOUD_VER:common.ACloudVerCommonFactory');
class ACloudApiCommonPermissions
{
    public function isUserBanned($uid)
    {
        return $this->getVersionCommonPermissions()->isUserBanned($uid);
    }

    public function readForum($uid, $fid)
    {
        return $this->getVersionCommonPermissions()->readForum($uid, $fid);
    }

    private function getVersionCommonPermissions()
    {
        return ACloudVerCommonFactory::getInstance()->getVersionCommonPermissions();
    }
}
