<?php

!defined('ACLOUD_PATH') && exit('Forbidden');
require_once Wind::getRealPath('ACLOUD_VER:common.ACloudVerCommonFactory');
class ACloudApiCommonAttach
{
    public function getImgAttaches($aids)
    {
        return $this->getVersionCommonAttach()->getImgAttaches($aids);
    }

    public function getImgAttachesByTids($tids)
    {
        return $this->getVersionCommonAttach()->getImgAttachesByTids($tids);
    }

    private function getVersionCommonAttach()
    {
        return ACloudVerCommonFactory::getInstance()->getVersionCommonAttach();
    }
}
