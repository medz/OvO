<?php

Wind::import('ADMIN:library.AdminBaseController');
class ServerController extends AdminBaseController
{
    /* (non-PHPdoc)
     * @see WindController::run()
     */
    private $BenchService = null;

    /**
     * 应用中心.
     */
    public function appcenterAction()
    {
        require_once Wind::getRealPath('ACLOUD:aCloud');
        $_extrasService = ACloudSysCoreCommon::loadSystemClass('extras', 'config.service');
        ACloudSysCoreCommon::setGlobal('g_ips', explode('|', ACloudSysCoreDefine::ACLOUD_APPLY_IPS));
        ACloudSysCoreCommon::setGlobal('g_siteurl', ACloudSysCoreDefine::ACLOUD_APPLY_SITEURL ? ACloudSysCoreDefine::ACLOUD_APPLY_SITEURL : $_extrasService->getExtra('ac_apply_siteurl'));
        ACloudSysCoreCommon::setGlobal('g_charset', ACloudSysCoreDefine::ACLOUD_APPLY_CHARSET ? ACloudSysCoreDefine::ACLOUD_APPLY_CHARSET : $_extrasService->getExtra('ac_apply_charset'));
        $benchService = ACloudSysCoreCommon::loadSystemClass('administor', 'bench.service');
        $url = $benchService->getLink(['a' => 'forward', 'do' => 'appcenter']);
        $this->setOutput($url, 'url');
    }

    public function run()
    {
        require_once Wind::getRealPath('ACLOUD:aCloud');
        ACloudSysCoreCommon::setGlobal('g_siteurl', PUBLIC_URL);
        ACloudSysCoreCommon::setGlobal('g_sitename', Wekit::C('site', 'info.name'));
        ACloudSysCoreCommon::setGlobal('g_charset', Wind::getApp()->getResponse()->getCharset());
        list($this->BenchService, $operate) = [ACloudSysCoreCommon::loadSystemClass('administor', 'bench.service'), strtolower($this->getInput('operate'))];
        if ($this->BenchService->isOpen()) {
            $ac_url = $this->BenchService->getLink();
            $this->setOutput($ac_url, 'ac_url');

            return true;
        }

        return $this->apply();
    }

    public function checkAction()
    {
        require_once Wind::getRealPath('ACLOUD:aCloud');
        ACloudSysCoreCommon::setGlobal('g_siteurl', PUBLIC_URL);
        ACloudSysCoreCommon::setGlobal('g_sitename', Wekit::C('site', 'info.name'));
        ACloudSysCoreCommon::setGlobal('g_charset', Wind::getApp()->getResponse()->getCharset());
        list($this->BenchService, $operate) = [ACloudSysCoreCommon::loadSystemClass('administor', 'bench.service'), strtolower($this->getInput('operate'))];

        return ($operate == 'reset') ? $this->reset() : $this->checkEnvironment();
    }

    private function apply()
    {
        list($siteName, $siteUrl, $charset, $version) = $this->BenchService->getSiteInfo();
        list($bool, $result) = $this->BenchService->simpleApply($siteUrl);
        if (! $bool) {
            $this->setOutput('error', 'ac_type');
            $this->setOutput($result, 'ac_message');

            return false;
        }
        $this->setOutput($siteUrl, 'site_url');
        $this->setOutput($siteName, 'site_name');
        $this->setOutput($charset, 'site_charset');
        $this->setOutput($version, 'site_version');
        $this->setOutput(NEXT_RELEASE, 'site_minor_version');
        $this->setOutput('apply', 'ac_type');
        $this->setOutput($result, 'request_key');
        $this->setOutput($this->BenchService->getApplySubmitUrl(), 'ac_apply_url');
    }

    private function checkEnvironment()
    {
        list($ac_sitename, $ac_siteurl, $ac_charset, $ac_version) = $this->BenchService->getSiteInfo();
        $ac_evninfo = $this->BenchService->getEnvInfo();
        $ac_applyinfo = $this->BenchService->getLastApplyInfo();
        $this->setOutput('check', 'ac_type');
        $this->setOutput($ac_sitename, 'ac_sitename');
        $this->setOutput($ac_siteurl, 'ac_siteurl');
        $this->setOutput($ac_charset, 'ac_charset');
        $this->setOutput($ac_version, 'ac_version');
        $this->setOutput($ac_evninfo, 'ac_evninfo');
        $this->setOutput($ac_applyinfo, 'ac_applyinfo');
        $this->setTemplate('server_run');
    }

    private function reset()
    {
        $this->BenchService->resetServer();
        $this->setOutput('reset', 'ac_type');
        $this->setTemplate('server_run');
    }
}
