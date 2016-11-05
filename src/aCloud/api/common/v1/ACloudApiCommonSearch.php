<?php

! defined('ACLOUD_PATH') && exit('Forbidden');
require_once Wind::getRealPath('ACLOUD_VER:common.ACloudVerCommonFactory');
class ACloudApiCommonSearch
{
    public function getHotwords()
    {
        return $this->getVersionCommonSearch()->getHotwords();
    }

    private function getVersionCommonSearch()
    {
        return ACloudVerCommonFactory::getInstance()->getVersionCommonSearch();
    }
}
