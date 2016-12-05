<?php

!defined('ACLOUD_PATH') && exit('Forbidden');
class ACloudSysVerifyServiceControl
{
    public function doubleControl($data)
    {
        return ($this->ipControl() && $this->oauthControl($data) && $this->aseControl($data)) ? true : false;
    }

    public function aseControl($data)
    {
        if (!is_array($data) || !isset($data['ciphertext']) || !$data['ciphertext']) {
            return false;
        }
        $keysService = ACloudSysCoreCommon::loadSystemClass('keys', 'config.service');
        $keys = $keysService->getKey123(1);
        if (!$keys || strlen($keys['key1']) != 128 || strlen($keys['key2']) != 128 || strlen($keys['key3']) != 128) {
            return false;
        }
        require_once Wind::getRealPath('ACLOUD:system.core.ACloudSysCoreAes');
        $aesService = new ACloudSysCoreAes();
        $key = $aesService->encrypt($keys['key3'], $keys['key2'], 256);
        if (!$key) {
            return false;
        }
        $plaintext = $aesService->strcode($data['ciphertext'], $key, 'DECODE');
        if (!$plaintext) {
            return false;
        }
        $params = ACloudSysCoreHttp::splitHttpQuery($plaintext);
        if (!is_array($params)) {
            return false;
        }
        $tmp = ACloudSysCoreCommon::arrayIntersectAssoc($params, $data);
        if (is_array($tmp) && count($tmp) > 0 && (count($tmp) == count($params)) && ($tmp['securecode'] === $data['securecode'])) {
            return true;
        }

        return false;
    }

    public function oauthControl($data)
    {
        $keysService = ACloudSysCoreCommon::loadSystemClass('keys', 'config.service');
        $key1 = $keysService->getKey1(1);
        if (!$key1 || strlen($key1) != 128 || !is_array($data) || count($data) < 4) {
            return false;
        }
        require_once Wind::getRealPath('ACLOUD:system.core.ACloudSysCoreVerify');
        if (ACloudSysCoreVerify::verifyWithOAuth($data, $key1)) {
            return true;
        }

        return false;
    }

    public function apiControl($data)
    {
        $appsService = ACloudSysCoreCommon::loadSystemClass('apps', 'config.service');
        if (!$data || !is_array($data) || !isset($data['app_id']) || !$data['app_id']) {
            return false;
        }
        $app = $appsService->getApp($data['app_id']);
        if (!$app || strlen($app['app_token']) != 128) {
            return false;
        }
        require_once Wind::getRealPath('ACLOUD:system.core.ACloudSysCoreVerify');
        if (ACloudSysCoreVerify::verifyWithOAuth($data, $app['app_token'])) {
            return true;
        }

        return false;
    }

    public function identifyControl($data)
    {
        if (!is_array($data) || !$data) {
            return false;
        }
        $initService = ACloudSysCoreCommon::loadSystemClass('init', 'open.service');
        list($bool) = $initService->identifyKey($data);

        return $bool;
    }

    public function ipControl($ips = array())
    {
        return true;
        $ip = ACloudSysCoreCommon::getIp();
        if ($this->spiderControl() || !$ip) {
            return false;
        }
        list($ip1, $ip2, $ip3) = explode('.', $ip);
        $envService = ACloudSysCoreCommon::loadSystemClass('env', 'open.service');
        if (!in_array($ip1.'.'.$ip2.'.'.$ip3.'.x', $envService->getIpLists())) {
            return false;
        }

        return true;
    }

    public function spiderControl()
    {
        $user_agent = strtolower($_SERVER['HTTP_USER_AGENT']);
        $allow_spiders = array('Baiduspider', 'Googlebot');
        foreach ($allow_spiders as $spider) {
            $spider = strtolower($spider);
            if (strpos($user_agent, $spider) !== false) {
                return true;
            }
        }

        return false;
    }
}
