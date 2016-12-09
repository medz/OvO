<?php

!defined('ACLOUD_PATH') && exit('Forbidden');

class ACloudSysConfigServiceApps
{
    public function addApp($fields)
    {
        if (!$fields['app_id'] || !$fields['app_token'] || strlen($fields['app_token']) != 128) {
            return false;
        }
        $data = array();
        $data['app_id'] = $fields['app_id'];
        $data['app_name'] = $fields['app_name'];
        $data['app_token'] = $fields['app_token'];
        $data['created_time'] = $data['modified_time'] = time();

        return $this->getAppsDao()->insert($data);
    }

    public function getApp($appId)
    {
        return $this->getAppsDao()->get($appId);
    }

    public function deleteApp($appId)
    {
        return $this->getAppsDao()->delete($appId);
    }

    public function deleteAllApp()
    {
        return $this->getAppsDao()->deleteAll();
    }

    public function updateApp($fields, $appId)
    {
        $fields['modified_time'] = time();

        return $this->getAppsDao()->update($fields, $appId);
    }

    public function getApps()
    {
        return $this->getAppsDao()->gets();
    }

    private function getAppsDao()
    {
        return ACloudSysCoreCommon::loadSystemClass('apps', 'config.dao');
    }
}
