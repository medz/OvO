<?php

!defined('ACLOUD_PATH') && exit('Forbidden');
require_once Wind::getRealPath('ACLOUD:system.core.ACloudSysCoreDao');
class ACloudSysConfigDaoAppConfigs extends ACloudSysCoreDao
{
    private $tablename = '{{acloud_app_configs}}';

    public function insert($fields)
    {
        $sql = sprintf('REPLACE INTO %s %s', $this->tablename, $this->buildClause($fields));
        $this->query($sql);

        return $this->get($fields['app_id'], $fields['app_key']);
    }

    public function update($fields, $appId, $appKey)
    {
        $sql = sprintf('UPDATE %s %s WHERE app_id = %s AND app_key = %s', $this->tablename, $this->buildClause($fields), ACloudSysCoreS::sqlEscape($appId), ACloudSysCoreS::sqlEscape($appKey));
        $this->query($sql);

        return $this->get($appId, $appKey);
    }

    public function get($appId, $appKey)
    {
        return $this->fetchOne(sprintf('SELECT * FROM %s WHERE app_id = %s AND app_key = %s', $this->tablename, ACloudSysCoreS::sqlEscape($appId), ACloudSysCoreS::sqlEscape($appKey)));
    }

    public function delete($appId, $appKey)
    {
        return $this->query(sprintf('DELETE FROM %s WHERE app_id = %s AND app_key = %s', $this->tablename, ACloudSysCoreS::sqlEscape($appId), ACloudSysCoreS::sqlEscape($appKey)));
    }

    public function deleteAppConfigByAppId($appId)
    {
        return $this->query(sprintf('DELETE FROM %s WHERE app_id = %s', $this->tablename, ACloudSysCoreS::sqlEscape($appId)));
    }

    public function deleteAll()
    {
        return $this->query(sprintf('DELETE FROM %s ', $this->tablename));
    }

    public function gets()
    {
        return $this->fetchAll(sprintf('SELECT * FROM %s ', $this->tablename));
    }

    public function getsByAppId($appId)
    {
        return $this->fetchAll(sprintf('SELECT * FROM %s WHERE app_id = %s ', $this->tablename, ACloudSysCoreS::sqlEscape($appId)));
    }
}
