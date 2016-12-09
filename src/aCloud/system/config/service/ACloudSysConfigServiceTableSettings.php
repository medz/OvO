<?php

!defined('ACLOUD_PATH') && exit('Forbidden');

class ACloudSysConfigServiceTableSettings
{
    public function addTableSetting($fields)
    {
        $fields = $this->checkFields($fields);
        if (!ACloudSysCoreS::isArray($fields) || !$fields['name']) {
            return false;
        }
        (!isset($fields['created_time']) || !$fields['created_time']) && $fields['created_time'] = time();
        (!isset($fields['modified_time']) || !$fields['modified_time']) && $fields['modified_time'] = time();

        return $this->getTableSettingsDao()->insert($fields);
    }

    public function getSettingByTableName($tableName)
    {
        $tableName = trim($tableName);
        if (!$tableName) {
            return array();
        }

        return $this->getTableSettingsDao()->get($tableName);
    }

    public function getSettingByTableNameWithReplace($tableName)
    {
        $tableName = trim($tableName);
        if (!$tableName) {
            return array();
        }
        $result = $this->getTableSettingsDao()->get($tableName);
        if (!$result) {
            return array();
        }
        $result['name'] = '{{'.str_replace('prefix_', '', $result['name']).'}}';

        return $result;
    }

    public function updateTableSettingByTableName($tableName, $fields)
    {
        list($tableName, $fields) = array(trim($tableName), $this->checkFields($fields));
        if (!$tableName || !ACloudSysCoreS::isArray($fields)) {
            return false;
        }

        return $this->getTableSettingsDao()->update($fields, $tableName);
    }

    public function deleteTableSettingByTableName($tableName)
    {
        $tableName = trim($tableName);
        if (!$tableName) {
            return false;
        }

        return $this->getTableSettingsDao()->delete($tableName);
    }

    public function getTableSettings()
    {
        return $this->getTableSettingsDao()->gets();
    }

    private function checkFields($fields)
    {
        $result = array();
        isset($fields['name']) && $result['name'] = trim($fields['name']);
        isset($fields['status']) && $result['status'] = intval($fields['status']);
        isset($fields['category']) && $result['category'] = intval($fields['category']);
        isset($fields['primary_key']) && $result['primary_key'] = trim($fields['primary_key']);
        isset($fields['created_time']) && $result['created_time'] = intval($fields['created_time']);
        isset($fields['modified_time']) && $result['modified_time'] = intval($fields['modified_time']);

        return $result;
    }

    private function getTableSettingsDao()
    {
        return ACloudSysCoreCommon::loadSystemClass('table.settings', 'config.dao');
    }
}
