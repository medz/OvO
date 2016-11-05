<?php

! defined('ACLOUD_PATH') && exit('Forbidden');
class ACloudSysBenchServiceAdministor
{
    public function isOpen()
    {
        $this->checkTables();
        $service = ACloudSysCoreCommon::loadSystemClass('extras', 'config.service');
        if (! $service->getExtra('ac_isopen')) {
            return false;
        }

        ACloudSysCoreCommon::setGlobal('g_siteurl', $service->getExtra('ac_apply_siteurl'));
        ACloudSysCoreCommon::setGlobal('g_charset', $service->getExtra('ac_apply_charset'));

        $isAdvanced = $service->getExtra('ac_install_advanced');
        list($lastaccess) = $this->getLastAccessInfo();
        list($bool, $message) = ($isAdvanced != 1 && (time() - $lastaccess >= 7200)) ? $this->checkLogin() : array(true, '');
        if (! $bool) {
            return false;
        }
        $extrasService = ACloudSysCoreCommon::loadSystemClass('extras', 'config.service');
        $extrasService->setExtra('ac_access_lasttime', time());

        return true;
    }

    public function getSiteInfo()
    {
        return array(ACloudSysCoreCommon::getGlobal('g_sitename', Wekit::C('site', 'info.name')), ACloudSysCoreCommon::getGlobal('g_siteurl', 'http://'.$_SERVER ['HTTP_HOST']), ACloudSysCoreCommon::getGlobal('g_charset'), ACloudSysCoreDefine::ACLOUD_V);
    }

    public function getEnvInfo()
    {
        $envService = ACloudSysCoreCommon::loadSystemClass('env', 'open.service');
        $tableInfo = $this->getTableInfo();
        $data = array();
        $data [] = array('k' => 'parse_url函数', 't' => 1, 'v' => function_exists('parse_url') ? 1 : 0);
        $data [] = array('k' => 'fsockopen函数', 't' => 1, 'v' => function_exists('fsockopen') ? 1 : 0);
        $data [] = array('k' => 'curl_init函数', 't' => 1, 'v' => function_exists('curl_init') ? 1 : 0);
        $data [] = array('k' => 'DNS解析函数', 't' => 1, 'v' => function_exists('gethostbyname') ? 1 : 0);
        $data [] = array('k' => '云服务域名解析', 't' => 0, 'v' => function_exists('gethostbyname') ? gethostbyname(ACloudSysCoreDefine::ACLOUD_HOST_API) : '0.0.0.0');
        $data [] = array('k' => '云服务端口测试', 't' => 0, 'v' => $envService->getNetWorkSpeed().'s');
        $data [] = array('k' => '云服务网络互通', 't' => 1, 'v' => ($envService->getNetWorkInterflow()) ? 1 : 0);
        $data [] = array('k' => '云服务入口文件', 't' => 1, 'v' => ($envService->hasIndexFile()) ? 1 : 0);
        $data [] = array('k' => '云服务数据表缺失', 't' => 0, 'v' => ($tableInfo ? implode(',', $tableInfo) : '没有缺失数据表'));

        return $data;
    }

    public function getLastApplyInfo()
    {
        $extrasService = ACloudSysCoreCommon::loadSystemClass('extras', 'config.service');
        $data = array();
        $data ['siteurl'] = ($siteurl = $extrasService->getExtra('ac_apply_siteurl')) ? $siteurl : '暂无';
        $data ['lasttime'] = ($lasttime = $extrasService->getExtra('ac_apply_lasttime')) ? date('Y-m-d H:i:s', $lasttime) : '暂无';

        return $data;
    }

    public function getLastAccessInfo()
    {
        $extrasService = ACloudSysCoreCommon::loadSystemClass('extras', 'config.service');
        $lastaccess = $extrasService->getExtra('ac_access_lasttime');

        return array($lastaccess, date('Y-m-d H:i:s', $lastaccess));
    }

    public function checkTables()
    {
        return true;
    }

    public function getTableInfo()
    {
        $dao = ACloudSysCoreCommon::loadSystemClass('createtable', 'config.dao');
        $tables = $dao->checkTables();
        $tmp = array();
        foreach ($tables as $table => $v) {
            if (! $v) {
                $tmp [] = $table;
            }
        }

        return ($tmp) ? $tmp : array();
    }

    public function getApplyTimeOut()
    {
        $extrasService = ACloudSysCoreCommon::loadSystemClass('extras', 'config.service');
        $lasttime = $extrasService->getExtra('ac_apply_lasttime');

        return array(date('Y-m-d H:i:s', $lasttime), (600 - (time() - $lasttime)));
    }

    public function checkLogin()
    {
        $params = array();
        $params ['method'] = 'login.check';
        $params ['ip'] = ACloudSysCoreCommon::getIp();
        $params ['ua'] = $_SERVER ['HTTP_USER_AGENT'];
        $params ['posttime'] = time();
        require_once Wind::getRealPath('ACLOUD:system.core.ACloudSysCoreVerify');
        $params = $this->buildPostParams($params);
        $params ['sign'] = ACloudSysCoreVerify::createSignWithOAuth($params);
        require_once Wind::getRealPath('ACLOUD:system.core.ACloudSysCoreHttp');
        $result = ACloudSysCoreHttp::sendPost($params);
        if (! is_object($result) || $result->code != 100) {
            return array(false, $result->msg);
        }

        return array(true, $result->msg);
    }

    public function getLink($data = array())
    {
        $params = $this->buildPostParams();
        require_once Wind::getRealPath('ACLOUD:system.core.ACloudSysCoreVerify');
        $params ['accesssign'] = ACloudSysCoreVerify::createSignWithOAuth($params);
        require_once Wind::getRealPath('ACLOUD:system.core.ACloudSysCoreHttp');

        return sprintf('http://%s/index.php?%s', ACloudSysCoreDefine::ACLOUD_HOST_API, ACloudSysCoreHttp::httpBuildQuery(array_merge($params, $data)));
    }

    public function localInstall($siteurl, $charset, $keys)
    {
        if (! $siteurl || ! $charset || ! is_array($keys)) {
            return false;
        }
        $this->checkTables();
        $result = $this->localSetKeys(1, $keys);
        if ($result) {
            $this->localSetExtras(ACloudSysCoreCommon::parseDomainName($siteurl), $charset);
        }

        return $result;
    }

    public function localSetKeys($id, $keys)
    {
        $id = intval($id);
        if (! $keys || ! isset($keys [$id])) {
            return false;
        }
        $key1 = isset($keys [$id] ['key1']) ? $keys [$id] ['key1'] : '';
        $key2 = isset($keys [$id] ['key2']) ? $keys [$id] ['key2'] : '';
        $key3 = isset($keys [$id] ['key3']) ? $keys [$id] ['key3'] : '';
        $keyService = ACloudSysCoreCommon::loadSystemClass('keys', 'config.service');
        if (strlen($key1) != 128 || strlen($key2) != 128 || strlen($key3) != 128) {
            return false;
        }
        $result = $keyService->updateKey123($id, $key1, $key2, $key3);

        return ($result) ? true : false;
    }

    public function localSetExtras($siteurl, $charset)
    {
        $extrasService = ACloudSysCoreCommon::loadSystemClass('extras', 'config.service');
        $extrasService->setExtra('ac_apply_siteurl', $siteurl);
        $extrasService->setExtra('ac_apply_charset', $charset);
        $extrasService->setExtra('ac_apply_lasttime', time());
        $extrasService->setExtra('ac_isopen', 1);
        $extrasService->setExtra('ac_install_advanced', 1);
        $extrasService->setExtra('ac_access_lasttime', time());

        return true;
    }

    public function resetServer()
    {
        $resetService = ACloudSysCoreCommon::loadSystemClass('reset', 'config.service');
        $resetService->resetConfig();

        require_once Wind::getRealPath(sprintf('ACLOUD:version.%s.core.ACloudVerCoreReset', ACloudSysCoreDefine::ACLOUD_VERSION));
        $hookService = new ACloudVerCoreReset();
        $hookService->execute();

        return true;
    }

    public function buildPostParams($data = array())
    {
        $params = array();
        $params ['siteurl'] = ACloudSysCoreCommon::getGlobal('g_siteurl', 'http://'.$_SERVER ['HTTP_HOST']);
        $params ['charset'] = ACloudSysCoreCommon::getGlobal('g_charset');
        $params ['footprint'] = ACloudSysCoreCommon::randCode(60);
        $params ['version'] = ACloudSysCoreDefine::ACLOUD_V;
        $params ['accesstime'] = time();

        return array_merge($params, is_array($data) ? $data : array());
    }

    public function simpleApply($siteUrl)
    {
        $siteUrl = trim($siteUrl);
        ACloudSysCoreCommon::setGlobal('g_siteurl', $siteUrl);
        $extrasService = ACloudSysCoreCommon::loadSystemClass('extras', 'config.service');
        $extrasService->setExtra('ac_apply_siteurl', ACloudSysCoreCommon::parseDomainName($siteUrl));
        $extrasService->setExtra('ac_apply_charset', ACloudSysCoreCommon::getGlobal('g_charset'));
        $extrasService->setExtra('ac_apply_lasttime', time());
        list($bool, $message) = $this->checkApplyCondition($siteUrl);
        if (! $bool) {
            return array($bool, $message);
        }
        $keyService = ACloudSysCoreCommon::loadSystemClass('keys', 'config.service');
        $key6 = $keyService->updateKey6(2);

        return array(true, $key6);
    }

    public function checkApplyCondition($siteUrl)
    {
        if (! $siteUrl) {
            return array(false, '站点地址不能为空');
        }
        $envService = ACloudSysCoreCommon::loadSystemClass('env', 'open.service');
        if (! $envService->hasIndexFile()) {
            return array(false, 'aCloud安装包的代码不完整，请重新安装覆盖');
        }
        $tableInfo = $this->getTableInfo();
        if (ACloudSysCoreS::isArray($tableInfo)) {
            return array(false, '缺少以下数据表'.implode(',', $tableInfo));
        }

        return array(true, '');
    }

    public function getApplySubmitUrl()
    {
        $params = array();
        $params ['timestamp'] = time();
        $params ['rand'] = ACloudSysCoreCommon::randCode(32);
        $params ['ua'] = isset($_SERVER ['HTTP_USER_AGENT']) ? $_SERVER ['HTTP_USER_AGENT'] : 'default';
        $params ['ip'] = ACloudSysCoreCommon::getIp();

        return sprintf('http://%s/?c=apply&%s', ACloudSysCoreDefine::ACLOUD_HOST_API, http_build_query($params));
    }
}
