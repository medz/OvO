<?php

!defined('ACLOUD_PATH') && exit('Forbidden');

class ACloudSysConfigServiceGeneralData
{
    public function executeSql($sql)
    {
        $sql = trim($sql);
        if (!$sql) {
            return false;
        }
        $versionFilterService = $this->getVersionFilterService();

        return $versionFilterService->filterFields($this->getGeneralDao()->executeSql($sql));
    }

    public function getVersionFilterService()
    {
        static $service = null;
        if (!is_null($service)) {
            return $service;
        }
        require_once Wind::getRealPath('ACLOUD_VER:config.ACloudVerConfigFilter');
        $service = new ACloudVerConfigFilter();

        return $service;
    }

    private function getGeneralDao()
    {
        return ACloudSysCoreCommon::loadSystemClass('generaldata', 'config.dao');
    }
}
