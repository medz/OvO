<?php

! defined('ACLOUD_PATH') && exit('Forbidden');

class ACloudSysConfigServiceReset
{
    public function resetConfig()
    {
        ACloudSysCoreCommon::loadSystemClass('extras', 'config.dao')->deleteAll();
        ACloudSysCoreCommon::loadSystemClass('apps', 'config.dao')->deleteAll();
        ACloudSysCoreCommon::loadSystemClass('app.configs', 'config.dao')->deleteAll();

        return true;
    }
}
