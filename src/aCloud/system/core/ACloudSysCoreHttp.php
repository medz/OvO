<?php

!defined('ACLOUD_PATH') && exit('Forbidden');
class ACloudSysCoreHttp
{
    public static function getCloudApi()
    {
        return sprintf('http://%s/api.php?', ACloudSysCoreDefine::ACLOUD_HOST_API);
    }

    public static function sendPost($data)
    {
        require_once Wind::getRealPath('ACLOUD:system.core.ACloudSysCoreHttpclient');
        $data = self::httpBuildQuery($data);
        $result = ACloudSysCoreHttpclient::post(self::getCloudApi(), $data);

        return ACloudSysCoreCommon::jsonDecode($result);
    }

    public static function httpBuildQuery($params)
    {
        if (function_exists('http_build_query')) {
            return http_build_query($params);
        }

        if (!$params || !is_array($params)) {
            return '';
        }
        $query = '';
        foreach ($params as $key => $value) {
            $query .= "$key=".urlencode($value).'&';
        }

        return $query;
    }

    public static function splitHttpQuery($query)
    {
        if (!$query) {
            return array();
        }
        $query = explode('&', $query);
        $params = array();
        foreach ($query as $q) {
            list($key, $value) = explode('=', $q);
            $params[$key] = urldecode($value);
        }

        return $params;
    }
}
