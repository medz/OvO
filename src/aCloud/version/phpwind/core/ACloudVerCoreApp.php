<?php

! defined('ACLOUD_PATH') && exit('Forbidden');
class ACloudVerCoreApp
{
    public static function getAppOutPut($collect)
    {
        return '';
        $data = array();
        $sign = ACloudSysCoreCommon::getSiteSign();
        $data ['src'] = $collect->getSrc();
        $data ['url'] = ACloudSysCoreCommon::getGlobal('g_siteurl', $_SERVER ['SERVER_NAME']);
        $data ['sn'] = ACloudSysCoreCommon::getSiteUnique();
        $data ['fid'] = $collect->getFid();
        $data ['uid'] = $collect->getUid();
        $data ['tid'] = $collect->getTid();
        $data [$sign] = ACloudVerCoreApp::getSyncData($sign);
        $data ['charset'] = ACloudSysCoreCommon::getGlobal('g_charset', 'gbk');
        $data ['username'] = $collect->getUsername();
        $data ['title'] = $collect->getTitle();
        $data ['_ua'] = ACloudSysCoreCommon::getSiteUserAgent();
        $data ['_shr'] = base64_encode(isset($_SERVER ['HTTP_REFERER']) ? $_SERVER ['HTTP_REFERER'] : '');
        $data ['_sqs'] = base64_encode(isset($_SERVER ['QUERY_STRING']) ? $_SERVER ['QUERY_STRING'] : '');
        $data ['_ssn'] = base64_encode(isset($_SERVER ['SCRIPT_NAME']) ? $_SERVER ['SCRIPT_NAME'] : '');
        $data ['_t'] = ACloudSysCoreCommon::getGlobal('timestamp') + 86400;
        $data ['_v'] = rand(1000, 9999);

        require_once Wind::getRealPath('ACLOUD:system.core.ACloudSysCoreHttp');
        $url = sprintf('http://%s/?%s', ACloudSysCoreDefine::ACLOUD_HOST_APP, ACloudSysCoreHttp::httpBuildQuery($data));
        $output = "<script type=\"text/javascript\">(function(d,t){var url=\"$url\";var g=d.createElement(t);g.async=1;g.src=url;d.body.insertBefore(g,d.body.firstChild);}(document,\"script\"));</script>";

        return $output;
    }

    private static function getSyncData($sign)
    {
        $syncType = isset($_COOKIE ['_ac_'.$sign]) ? intval($_COOKIE ['_ac_'.$sign]) : 0;
        if (! $syncType) {
            return 0;
        }
        setcookie('_ac_'.$sign, '', time() - 3600);

        return $syncType;
    }
}
