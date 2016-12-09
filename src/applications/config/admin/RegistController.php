<?php

Wind::import('ADMIN:library.AdminBaseController');

/**
 * 后台设置-注册登录设置.
 *
 * @author Qiong Wu <papa0924@gmail.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: RegistController.php 4132 2012-02-11 05:35:07Z xiaoxia.xuxx $
 */
class RegistController extends AdminBaseController
{
    /* (non-PHPdoc)
     * @see WindController::run()
     */
    public function run()
    {
        /* @var $userGroup PwUserGroups */
        $userGroup = Wekit::load('usergroup.PwUserGroups');
        $groups = $userGroup->getAllGroups();
        $groupTypes = $userGroup->getTypeNames();

        Wind::import('SRV:credit.bo.PwCreditBo');
        /* @var $pwCreditBo PwCreditBo */
        $pwCreditBo = PwCreditBo::getInstance();

        $config = Wekit::C()->getValues('register');
        if (!$config['active.field']) {
            $config['active.field'] = array();
        }

        $wconfig = WindidApi::C('reg');
        $config['security.username.min'] = $wconfig['security.username.min'];
        $config['security.username.max'] = $wconfig['security.username.max'];
        $config['security.password.min'] = $wconfig['security.password.min'];
        $config['security.password.max'] = $wconfig['security.password.max'];
        $config['security.password'] = $wconfig['security.password'];
        $config['security.ban.username'] = $wconfig['security.ban.username'];

        $this->setOutput($config, 'config');
        $this->setOutput($pwCreditBo->cType, 'credits');
        $this->setOutput($groups, 'groups');
        $this->setOutput($groupTypes, 'groupTypes');
    }

    /**
     * 配置增加表单处理器.
     */
    public function dorunAction()
    {
        $this->getRequest()->isPost() || $this->showError('operate.fail');

        $username_max = abs($this->getInput('securityUsernameMax', 'post'));
        $username_min = abs($this->getInput('securityUsernameMin', 'post'));
        $username_max = max(array($username_max, $username_min));
        $username_max > 15 && $username_max = 15;
        $username_min = min(array($username_max, $username_min));
        $username_min < 1 && $username_min = 1;
        $password_max = abs($this->getInput('securityPasswordMax', 'post'));
        $password_min = abs($this->getInput('securityPasswordMin', 'post'));
        $password_max = max(array($password_max, $password_min));
        $password_min = min(array($password_max, $password_min));
        $password_min < 1 && $password_min = 1;
        $password_security = $this->getInput('securityPassword', 'post');

        $ipTime = ceil($this->getInput('securityIp', 'post'));
        if ($ipTime < 0) {
            $ipTime = 1;
        }
        $config = new PwConfigSet('register');
        $config->set('type', $this->getInput('type', 'post'))
            ->set('protocol', $this->getInput('protocol', 'post'))
            ->set('active.field', $this->getInput('activeField', 'post'))
            ->set('active.mail', $this->getInput('activeMail', 'post'))
            ->set('active.mail.title', $this->getInput('activeTitle', 'post'))
            ->set('active.mail.content', $this->getInput('activeContent', 'post'))
            ->set('active.phone', $this->getInput('activePhone', 'post'))
            ->set('active.check', $this->getInput('activeCheck', 'post'))
            ->set('security.ban.username', $this->getInput('securityBanUsername', 'post'))
            ->set('security.username.max', $username_max)
            ->set('security.username.min', $username_min)
            ->set('security.password', $password_security)
            ->set('security.password.max', $password_max)
            ->set('security.password.min', $password_min)
            ->set('security.ip', $ipTime)
            ->set('welcome.type', $this->getInput('welcomeType', 'post'))
            ->set('welcome.title', $this->getInput('welcomeTitle', 'post'))
            ->set('welcome.content', $this->getInput('welcomeContent', 'post'))
            ->set('close.msg', $this->getInput('closeMsg', 'post'))
            ->set('invite.expired', ceil($this->getInput('inviteExpired', 'post')))
            ->set('invite.credit.type', $this->getInput('inviteCreditType', 'post'))
            ->set('invite.reward.credit.num', $this->getInput('inviteRewardCreditNum', 'post'))
            ->set('invite.reward.credit.type', $this->getInput('inviteRewardCredit', 'post'))
            ->set('invite.pay.open', $this->getInput('invitePayState', 'post'))
            ->set('invite.pay.money', $this->getInput('invitePayMoney', 'post'))
            ->flush();

        //同步设置到Windid中
        $windid = $this->_getWindid();
        $windid->setConfig('reg', 'security.username.min', $username_min);
        $windid->setConfig('reg', 'security.username.max', $username_max);
        $windid->setConfig('reg', 'security.password.min', $password_min);
        $windid->setConfig('reg', 'security.password.max', $password_max);
        $windid->setConfig('reg', 'security.password', $password_security);
        $windid->setConfig('reg', 'security.ban.username', $this->getInput('securityBanUsername', 'post'));
        $this->showMessage('ADMIN:success');
    }

    /**
     * 站点设置.
     */
    public function loginAction()
    {
        /* @var $userGroup PwUserGroups */
        $userGroup = Wekit::load('usergroup.PwUserGroups');
        $groups = $userGroup->getAllGroups();
        $groupTypes = $userGroup->getTypeNames();

        $config = Wekit::C()->getValues('login');
        if (!$config['question.groups']) {
            $config['question.groups'] = array();
        }
        $this->setOutput($config, 'config');
        $this->setOutput($groups, 'groups');
        $this->setOutput($groupTypes, 'groupTypes');
    }

    /**
     * 全局配置增加表单处理器.
     */
    public function dologinAction()
    {
        $this->getRequest()->isPost() || $this->showError('operate.fail');

        $way = $this->getInput('ways', 'post');
        if (!$way) {
            $this->showError('config.login.type.require');
        }
        $config = new PwConfigSet('login');
        $config->set('ways', $this->getInput('ways', 'post'))
            ->set('trypwd', $this->getInput('trypwd', 'post'))
            ->set('question.groups', $this->getInput('questionGroups', 'post'))
            ->set('resetpwd.mail.title', $this->getInput('resetPwdMailTitle', 'post'))
            ->set('resetpwd.mail.content', $this->getInput('resetPwdMailContent', 'post'))
            ->flush();
        $this->showMessage('operate.success');
    }

    /**
     * 用户引导页面.
     */
    public function guideAction()
    {
        /* @var $guideService PwUserRegisterGuideService */
        $guideService = Wekit::load('APPS:u.service.PwUserRegisterGuideService');
        $this->setOutput($guideService->getGuideList(), 'list');
    }

    /**
     * 用户引导页面设置.
     */
    public function doguideAction()
    {
        $config = $this->getInput('config', 'post');
        if (!$config) {
            $this->showError('ADMIN:fail');
        }
        /* @var $guideService PwUserRegisterGuideService */
        $guideService = Wekit::load('APPS:u.service.PwUserRegisterGuideService');
        $guideService->setConfig($config);
        $this->showMessage('ADMIN:success', 'config/regist/guide');
    }

    protected function _getWindid()
    {
        return WindidApi::api('config');
    }
}
