<?php

! defined('ACLOUD_PATH') && exit('Forbidden');
require_once Wind::getRealPath('ACLOUD_VER:common.ACloudVerCommonFactory');
class ACloudApiCommonAd
{
    public function getAdType()
    {
        return $this->getVersionCommonAd()->getAdType();
    }

    public function addAdPosition($id, $identifier, $type, $width, $height, $status, $schedule)
    {
        return $this->getVersionCommonAd()->addAdPosition($id, $identifier, $type, $width, $height, $status, $schedule);
    }

    public function editAdPosition($id, $identifier, $type, $width, $height, $status, $schedule, $showType, $condition)
    {
        return $this->getVersionCommonAd()->editAdPosition($id, $identifier, $type, $width, $height, $status, $schedule, $showType, $condition);
    }

    public function changeAdPositionStatus($id, $status)
    {
        return $this->getVersionCommonAd()->changeAdPositionStatus($id, $status);
    }

    public function getModes()
    {
        return $this->getVersionCommonAd()->getModes();
    }

    public function getDefaultPosition()
    {
        return $this->getVersionCommonAd()->getDefaultPosition();
    }

    public function getPages()
    {
        return $this->getVersionCommonAd()->getPages();
    }

    public function getPortals()
    {
        return $this->getVersionCommonAd()->getPortals();
    }

    public function getInstalledPosition()
    {
        return $this->getVersionCommonAd()->getInstalledPosition();
    }

    private function getVersionCommonAd()
    {
        return ACloudVerCommonFactory::getInstance()->getVersionCommonAd();
    }
}
