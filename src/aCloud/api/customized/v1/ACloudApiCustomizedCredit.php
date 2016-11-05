<?php

! defined('ACLOUD_PATH') && exit('Forbidden');
require_once Wind::getRealPath('ACLOUD_VER:customized.ACloudVerCustomizedFactory');
class ACloudApiCustomizedCredit
{
    public function fetchCreditType()
    {
        return $this->getVersionCustomizedCredit()->fetchCreditType();
    }

    public function setCredit($uid, $ctype, $point, $appName)
    {
        return $this->getVersionCustomizedCredit()->setCredit($uid, $ctype, $point, $appName);
    }

    private function getVersionCustomizedCredit()
    {
        return ACloudVerCustomizedFactory::getInstance()->getVersionCustomizedCredit();
    }
}
