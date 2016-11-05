<?php

! defined('ACLOUD_PATH') && define('ACLOUD_PATH', dirname(__FILE__).DIRECTORY_SEPARATOR);
Wind::register(ACLOUD_PATH, 'ACLOUD');
require_once Wind::getRealPath('ACLOUD:system.core.ACloudSysCoreCommon');
define('ACLOUD_VERSION_PATH', ACLOUD_PATH.'/version/'.ACloudSysCoreDefine::ACLOUD_VERSION);
Wind::register(ACLOUD_VERSION_PATH, 'ACLOUD_VER');
require_once Wind::getRealPath(sprintf('ACLOUD:version.%s.ACloud%sBootstrap', ACloudSysCoreDefine::ACLOUD_VERSION, ucfirst(ACloudSysCoreDefine::ACLOUD_VERSION)));

class ACloudRouter
{
    public function run()
    {
        list($a) = ACloudSysCoreS::gp(array('a'));
        $action = ($a) ? $a.'Action' : '';
        if ($action && method_exists($this, $action)) {
            ACloudInit::execute();
            $this->$action ();
        }
    }

    public function sysAction()
    {
        Wind::import('ACLOUD:system.ACloudSysRouter');
        $sysRouter = new ACloudSysRouter();

        return $sysRouter->run();
    }

    public function apiAction()
    {
        Wind::import('ACLOUD:api.ACloudApiRouter');
        $apiRouter = new ACloudApiRouter();

        return $apiRouter->run();
    }
}

class ACloudInit
{
    public static function execute()
    {
        $_extrasService = ACloudSysCoreCommon::loadSystemClass('extras', 'config.service');
        ACloudSysCoreCommon::setGlobal('g_ips', explode('|', ACloudSysCoreDefine::ACLOUD_APPLY_IPS));
        ACloudSysCoreCommon::setGlobal('g_siteurl', ACloudSysCoreDefine::ACLOUD_APPLY_SITEURL ? ACloudSysCoreDefine::ACLOUD_APPLY_SITEURL : $_extrasService->getExtra('ac_apply_siteurl'));
        ACloudSysCoreCommon::setGlobal('g_charset', ACloudSysCoreDefine::ACLOUD_APPLY_CHARSET ? ACloudSysCoreDefine::ACLOUD_APPLY_CHARSET : $_extrasService->getExtra('ac_apply_charset'));
    }
}

class ACloudAppGuiding
{
    public static function getApp($collect)
    {
        ACloudInit::execute();
        require_once Wind::getRealPath(sprintf('ACLOUD:version.%s.core.ACloudVerCoreApp', ACloudSysCoreDefine::ACLOUD_VERSION));

        return ACloudVerCoreApp::getAppOutPut($collect);
    }

    public static function runApps($page)
    {
        ACloudInit::execute();

        return ACloudSysCoreCommon::loadApps($page);
    }

    public static function collectSql($queryString, $params)
    {
        return ACloudSysCoreCommon::loadSystemClass('aggregate', 'dataflow.service')->collectSQL($queryString, $params);
    }
}
