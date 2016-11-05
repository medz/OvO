<?php

! defined('ACLOUD_PATH') && exit('Forbidden');

class ACloudApiCommonGeneralApi
{
    public function get($apiSetting, $request)
    {
        $sql = ACloudSysCoreCommon::loadSystemClass('sqlbuilder')->buildSelectSql($apiSetting, $request);
        if (! $sql) {
            return array(4006, array());
        }
        $data = ACloudSysCoreCommon::loadSystemClass('generaldata', 'config.service')->executeSql($sql);

        return ($data === false) ? array(4007, array()) : array(0, $data);
    }
}
