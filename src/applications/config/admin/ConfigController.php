<?php

Wind::import('ADMIN:library.AdminBaseController');

/**
 * 后台设置-站点设置-站点信息设置/全局参数设置
 *
 * @author Qiong Wu <papa0924@gmail.com> 2011-12-7
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: ConfigController.php 3935 2012-02-02 02:37:34Z gao.wanggao $
 * @package admin
 * @subpackage controller.config
 */
class ConfigController extends AdminBaseController
{
    /**
     * 站点设置-站点信息设置
     *
     */
    public function run()
    {

        /* @var $userGroup PwUserGroups */
        $userGroup = Wekit::load('usergroup.PwUserGroups');
        $groups = $userGroup->getAllGroups();
        $groupTypes = $userGroup->getTypeNames();
        $config = Wekit::C()->getValues('site');

        $this->setOutput($config, 'config');
        $this->setOutput($groups, 'groups');
        $this->setOutput($groupTypes, 'groupTypes');
    }

    /**
     * 配置增加表单处理器
     *
     */
    public function dorunAction()
    {
        $config = new PwConfigSet('site');
        $config->set('info.name', $this->getInput('infoName', 'post'))
            ->set('info.url', $this->getInput('infoUrl', 'post'))
            ->set('info.mail', $this->getInput('infoMail', 'post'))
            ->set('info.icp', $this->getInput('infoIcp', 'post'))
            ->set('info.logo', $this->getInput('infoLogo', 'post'))
            ->set('statisticscode', $this->getInput('statisticscode', 'post'))
            ->set('visit.state', $this->getInput('visitState', 'post'))
            ->set('visit.group', $this->getInput('visitGroup', 'post'))
            ->set('visit.gid', $this->getInput('visitGid', 'post'))
            ->set('visit.ip', $this->getInput('visitIp', 'post'))
            ->set('visit.member', $this->getInput('visitMember', 'post'))
            ->set('visit.message', $this->getInput('visitMessage', 'post'))
            ->flush();
        $this->showMessage('ADMIN:success');
    }



    /**
     * 站点设置
     *
     */
    public function siteAction()
    {
        $config = Wekit::C()->getValues('site');
        $this->setOutput($config, 'config');
    }



    /**
     * 全局配置增加表单处理器
     *
     */

    public function dositeAction()
    {
        $configSet = new PwConfigSet('site');
        $configSet->set('time.cv', (int) $this->getInput('timeCv', 'post'))
            ->set('time.timezone', $this->getInput('timeTimezone', 'post'))
            ->set('refreshtime', (int) $this->getInput('refreshtime', 'post'))
            ->set('onlinetime', (int) $this->getInput('onlinetime', 'post'))
            ->set('debug', $this->getInput('debug', 'post'))
            ->set('managereasons', $this->getInput('managereasons', 'post'))
//			->set('scorereasons', $this->getInput('scorereasons', 'post'))
            ->set('cookie.path', $this->getInput('cookiePath'), 'post')
            ->set('cookie.domain', $this->getInput('cookieDomain', 'post'))
            ->set('cookie.pre', $this->getInput('cookiePre', 'pre'))
            ->flush();
        Wekit::load('domain.srv.PwDomainService')->refreshTplCache();

        /*
        $service = $this->_loadConfigService();
        $config = $service->getValues('site');
        if ($config['windid'] != 'client') {
            $windid = $this->_getWindid();
            $windid->setConfig('site', 'timezone', $this->getInput('timeTimezone', 'post'));
            $windid->setConfig('site', 'timecv', (int)$this->getInput('timeCv', 'post'));
        }
        */
        $this->showMessage('ADMIN:success');
    }

    protected function _getWindid()
    {
        return WindidApi::api('config');
    }
}
