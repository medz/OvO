<?php

!defined('ACLOUD_PATH') && exit('Forbidden');
class ACloudSysOpenServiceApply
{
    public function verifying($data)
    {
        if (!is_array($data) || count($data) < 7) {
            return false;
        }
        $keysService = $this->getKeysService();
        $key6 = $keysService->getKey6(2);
        require_once Wind::getRealPath('ACLOUD:system.core.ACloudSysCoreVerify');
        if ($key6 && strlen($key6) == 128 && ACloudSysCoreVerify::verifyWithOAuth($data, $key6)) {
            return true;
        }

        return false;
    }

    public function applySuccess()
    {
        $extrasService = ACloudSysCoreCommon::loadSystemClass('extras', 'config.service');

        return $extrasService->setExtra('ac_isopen', 1);
    }

    private function getKeysService()
    {
        return ACloudSysCoreCommon::loadSystemClass('keys', 'config.service');
    }
}
