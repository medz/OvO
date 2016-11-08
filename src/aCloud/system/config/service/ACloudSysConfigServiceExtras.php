<?php

! defined('ACLOUD_PATH') && exit('Forbidden');

class ACloudSysConfigServiceExtras
{
    public function setExtra($key, $value, $etype = 1)
    {
        $etype = ($etype) ? $etype : (is_array($value) ? 2 : 1);
        $evalue = is_array($value) ? serialize($value) : $value;
        $fields = array('ekey' => $key, 'evalue' => $evalue, 'etype' => $etype, 'created_time' => time(), 'modified_time' => time());

        return $this->_setExtra($key, $fields);
    }

    public function getExtrasByKeys($keys)
    {
        return $this->getExtrasDao()->getsByKeys($keys);
    }

    public function deleteAllExtras()
    {
        return $this->getExtrasDao()->deleteAll();
    }

    public function getExtra($key)
    {
        static $acloudExtrasConfig = array();
        if ($acloudExtrasConfig[$key]) {
            $extra = $acloudExtrasConfig[$key];
        } else {
            $extra = $this->_getExtra($key);
            $acloudExtrasConfig[$key] = $extra;
        }

        return ($extra && $extra ['evalue']) ? (($extra ['etype'] == 2) ? unserialize($extra ['evalue']) : $extra ['evalue']) : null;
    }

    public function _setExtra($key, $data)
    {
        $fields = array('ekey' => $key, 'evalue' => $data ['evalue'], 'etype' => $data ['etype'], 'created_time' => time(), 'modified_time' => time());

        return $this->getExtrasDao()->insert($fields);
    }

    public function _getExtra($key)
    {
        return $this->getExtrasDao()->get($key);
    }

    public function getExtras()
    {
        return $this->getExtrasDao()->gets();
    }

    private function getExtrasDao()
    {
        return ACloudSysCoreCommon::loadSystemClass('extras', 'config.dao');
    }
}
