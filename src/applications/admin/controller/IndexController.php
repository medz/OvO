<?php

Wind::import('ADMIN:library.AdminBaseController');
/**
 * 后台应用主题框架操作处理类
 *
 * 后台应用默认操作处理类,操作方法：<ul>
 * <li>run,后台主体框架页面处理</li>
 * <li>login,后台登录操作</li>
 * </ul>
 *
 * @author Qiong Wu <papa0924@gmail.com> 2011-10-13
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: IndexController.php 32087 2014-11-02 13:46:50Z gao.wanggao $
 * @package admin
 * @subpackage controller
 */
class IndexController extends AdminBaseController
{
    /**
     * 后台主体框架页面
     *
     */
    public function run()
    {
        /* @var $menuService AdminMenuService */
        $menuService = Wekit::load('ADMIN:service.srv.AdminMenuService');
        $menus = $menuService->getMyMenus($this->loginUser);
        //常用菜单
        if (isset($menus['custom']['items']) && is_array($menus['custom']['items'])) {
            $menus['custom']['items'] += $menuService->getCustomMenus($this->loginUser);
        }
        $this->setOutput($menus, 'menus');
    }

    public function noticeAction()
    {
        $notice = Wekit::load('APPCENTER:service.srv.PwSystemInstallation')->getNotice($this->loginUser);
        $this->setOutput($notice, 'data');
        $this->showMessage('success');
    }

    /**
     * 后台退出
     *
     */
    public function logoutAction()
    {
        $adminUserService = Wekit::load('ADMIN:service.srv.AdminUserService');
        $result = $adminUserService->logout();
        if ($result instanceof PwError) {
            $this->showError($result->getError());
        }
        if ($result === false) {
            $this->showError('logout.fail');
        }
        $this->forwardRedirect(WindUrlHelper::createUrl('index/run'));
    }

    /**
     * 后台登录操作
     *
     */
    public function loginAction()
    {
        /* @var $adminLogService AdminLogService */
        $adminLogService = Wekit::load('ADMIN:service.srv.AdminLogService');
        list($countLoginFailed, $lastLoginTime) = $adminLogService->checkLoginFailed($this->getRequest());
        $remainTime = 900 - (time() - $lastLoginTime);
        if ($countLoginFailed > 8 && $remainTime > 0) {
            $this->showError(
                array(
                    'ADMIN:login.fail.forbidden',
                    array('{mtime}' => intval($remainTime / 60), '{stime}' => intval($remainTime % 60)),
                )
            );
        }
        list($username, $password) = $this->getInput(array('username', 'password'), 'post');
        if ($username && $password) {
            $this->checkVerify();
            /* @var $adminUserService AdminUserService */
            $adminUserService = Wekit::load('ADMIN:service.srv.AdminUserService');
            $result = $adminUserService->login($username, $password);
            if ($result instanceof PwError) {
                $adminLogService->loginLogFailed($this->getRequest(), $username);
                $this->showError($result->getError());
            } elseif ($result === false) {
                $this->showError('login.fail');
            } else {
                $adminLogService->loginLog($this->getRequest(), $username);
            }
            $this->forwardRedirect(WindUrlHelper::createUrl('index/run'));
        }
        if (in_array('windidlogin', Wekit::C()->verify->get('showverify', array()))) {
            $this->setOutput(in_array('windidlogin', Wekit::C()->verify->get('showverify', array())), 'showVerify');
        } else {
            $this->setOutput(in_array('adminlogin', Wekit::C()->verify->get('showverify', array())), 'showVerify');
        }
    }

    /**
     * 检测用户的验证码是否正确
     */
    private function checkVerify()
    {
        //windid登陆
        if (in_array('windidlogin', Wekit::C()->verify->get('showverify', array()))) {
            $verifySrv = Wekit::load('WINDID:service.verify.srv.PwCheckVerifyService');
            if ($verifySrv->checkVerify($this->getInput('code'))) {
                return true;
            }
            $this->showError('USER:verifycode.error');

            return false;
        }

        if (!in_array('adminlogin', Wekit::C()->verify->get('showverify', array()))) {
            return true;
        }
        /* @var $verifySrv PwCheckVerifyService */
        $verifySrv = Wekit::load('verify.srv.PwCheckVerifyService');
        if (!$verifySrv->checkVerify($this->getInput('code'))) {
            $this->showError('USER:verifycode.error');
        }

        return true;
    }

    /**
     * 显示验证码
     */
    public function showVerifyAction()
    {
        $audio = $this->getInput('getAudio', 'get');
        if ($audio) {
            
            $srv = new PwVerifyCode();
            $srv->getAudioVerify();
            exit;
        }
        $rand = $this->getInput('rand', 'get');
        $config = Wekit::C('verify');
        $config['type'] = $config['type'] ? $config['type'] : 'image' ;

        //windid登陆验证码显示
        if (in_array('windidlogin', Wekit::C()->verify->get('showverify', array()))) {
            Wind::import('WINDID:service.verify.srv.PwVerifyService');
        } else {
            Wind::import('SRV:verify.srv.PwVerifyService');
        }
        $srv = new PwVerifyService('PwVerifyService_getVerifyType');
        if ($rand) {
            $srv->getVerify($config['type']);
            exit;
        }
        $url = WindUrlHelper::createUrl('index/showVerify', array('rand' => Pw::getTime()));
        $display = $srv->getOutType($config['type']);
        if ($display == 'flash') {
            $html = '<embed align="middle"
				width="'.$config['width'].'"
				height="'.$config['height'].'"
				type="application/x-shockwave-flash"
				allowscriptaccess="sameDomain"
				menu="false"
				bgcolor="#ffffff"
				wmode="transparent"
				quality="high"
				src="'.$url.'">';
            if ($config['voice']) {
                $url = WindUrlHelper::createUrl('index/showVerify', array(
                    'getAudio' => 1,
                    'songVolume' => 100,
                    'autoStart' => 'false',
                    'repeatPlay' => 'false',
                    'showDownload' => 'false',
                    'rand' => Pw::getTime(),
                ));
                $html .= '<embed height="20" width="25"
				type="application/x-shockwave-flash"
				pluginspage="http://www.macromedia.com/go/getflashplayer"
				quality="high"
				src="'.Wind::getApp()->getResponse()->getData('G', 'url', 'images').'/audio.swf?file='.urlencode($url).'">';
            }
            $html .= '<a id="J_verify_update_a" href="#" role="button">换一个</a>';
        } elseif ($display == 'image') {
            $html = '<img id="J_verify_update_img" src="'.$url.'"
				width="'.$config['width'].'"
				height="'.$config['height'].'" >';
            if ($config['voice']) {
                $url = WindUrlHelper::createUrl('index/showVerify', array(
                    'getAudio' => 1,
                    'songVolume' => 100,
                    'autoStart' => 'false',
                    'repeatPlay' => 'false',
                    'showDownload' => 'false',
                    'rand' => Pw::getTime(),
                ));
                $html .= '<span title="点击后键入您听到的内容"><embed wmode="transparent" height="20" width="25"
				type="application/x-shockwave-flash"
				pluginspage="http://www.macromedia.com/go/getflashplayer"
				quality="high"
				src="'.Wind::getApp()->getResponse()->getData('G', 'url', 'images').'/audio.swf?file='.urlencode($url).'"></span>';
            }
            $html .= '<a id="J_verify_update_a" href="#" role="button">换一个</a>';
        } else {
            $html = $srv->getVerify($config['type']);
        }
        $this->setOutput($html, 'html');
        $this->showMessage('operate.success');
    }
}
