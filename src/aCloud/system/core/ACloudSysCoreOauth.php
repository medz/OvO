<?php

!defined('ACLOUD_PATH') && exit('Forbidden');
class ACloudSysCoreOauth
{
    public static function createHttpQuery($params)
    {
        if (!$params || !is_array($params)) {
            return '';
        }
        ksort($params);
        require_once Wind::getRealPath('ACLOUD:system.core.ACloudSysCoreHttp');

        return ACloudSysCoreHttp::httpBuildQuery($params);
    }

    public static function createHttpSign($plaintext)
    {
        return md5($plaintext);
    }
}
