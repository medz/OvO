<?php

!defined('ACLOUD_PATH') && exit('Forbidden');
class ACloudSysOpenServiceEnv
{
    public function checkFunctions()
    {
        $keys = array('fsockopen', 'parse_url', 'gethostbyname', 'md5_file', 'http_build_query', 'curl_init');
        $data = array();
        foreach ($keys as $key) {
            $data[$key] = function_exists($key);
        }

        return $data;
    }

    public function getNetWorkSpeed()
    {
        $time_start = ACloudSysCoreCommon::getMicrotime();
        require_once Wind::getRealPath('ACLOUD:system.core.ACloudSysCoreHttp');
        $result = ACloudSysCoreHttp::sendPost($this->buildPostParams('env.ping', array('speed' => $time_start)));
        $time_end = (is_object($result) && $result->code == 100) ? ACloudSysCoreCommon::getMicrotime() : $time_start - 1;

        return number_format($time_end - $time_start, 4);
    }

    public function getNetWorkInterflow()
    {
        require_once Wind::getRealPath('ACLOUD:system.core.ACloudSysCoreHttp');
        $result = ACloudSysCoreHttp::sendPost($this->buildPostParams('env.interflow', array()));

        return (is_object($result) && $result->code == 100) ? true : false;
    }

    public function hasIndexFile()
    {
        return is_file(ACLOUD_PATH.'/index.php') ? true : false;
    }

    public function getServerInfo()
    {
        $keys = array('SERVER_SOFTWARE', 'SERVER_PROTOCOL', 'HTTP_USER_AGENT', 'HTTP_ACCEPT_LANGUAGE', 'HTTP_ACCEPT_ENCODING', 'HTTP_ACCEPT_CHARSET', 'HTTP_CONNECTION');
        $params = array();
        foreach ($keys as $key) {
            $params[$key] = isset($_SERVER[$key]) ? $_SERVER[$key] : 'unknow';
        }
        $params['ACLOUD_V'] = ACloudSysCoreDefine::ACLOUD_V;
        $params['ACLOUD_VERSION'] = ACloudSysCoreDefine::ACLOUD_VERSION;
        $params['ACLOUD_HOST_API'] = ACloudSysCoreDefine::ACLOUD_HOST_API;
        $params['ACLOUD_HOST_APP'] = ACloudSysCoreDefine::ACLOUD_HOST_APP;
        $params['ACLOUD_API_VERSION'] = ACloudSysCoreDefine::ACLOUD_API_VERSION;

        require_once Wind::getRealPath(sprintf('ACLOUD:version.%s.core.ACloudVerCoreSite', ACloudSysCoreDefine::ACLOUD_VERSION));
        $hookService = new ACloudVerCoreSite();
        $sites = $hookService->execute();

        return array_merge($params, $sites);
    }

    public function getFilesInfo()
    {
        $sysFiles = ACloudSysCoreCommon::listDir(ACLOUD_PATH.'/system/');
        $tmp = array();
        foreach ($sysFiles as $file) {
            $filename = basename($file);
            $tmp[$filename] = md5_file($file);
        }

        return $tmp;
    }

    public function getIpLists($ips = array())
    {
        $ips = array_merge($ips, (array) ACloudSysCoreCommon::getGlobal('g_ips'));

        return array_merge($ips, array('110.75.164.x', '110.75.168.x', '110.75.171.x', '110.75.172.x', '110.75.173.x', '110.75.174.x', '110.75.175.x', '110.75.176.x', '110.75.167.x'));
    }

    private function buildPostParams($method, $data)
    {
        $params = array();
        $params['method'] = $method;
        $params['version'] = ACLOUD_V;
        $params['siteurl'] = ACloudSysCoreCommon::getGlobal('g_siteurl');
        $params['sitename'] = ACloudSysCoreCommon::getGlobal('g_sitename');
        $params['charset'] = ACloudSysCoreCommon::getGlobal('g_charset');
        $params['ip'] = ACloudSysCoreCommon::getIp();
        $params['ua'] = $_SERVER['HTTP_USER_AGENT'];
        $params['posttime'] = time();

        return $params;
    }
}
