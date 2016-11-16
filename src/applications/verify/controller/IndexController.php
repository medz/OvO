<?php


/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: IndexController.php 28899 2013-05-29 07:23:48Z gao.wanggao $
 * @package
 */

class IndexController extends PwBaseController
{
    /* (non-PHPdoc)
     * @see PwBaseController::beforeAction()
     */
    public function beforeAction($handlerAdapter)
    {
    }

    /* (non-PHPdoc)
     * @see PwBaseController::afterAction()
     */
    public function afterAction($handlerAdapter)
    {
    }

    /**
     * 获取验证码
     */
    public function getAction()
    {
        $rand = $this->getInput('rand', 'get');
        $config = Wekit::C('verify');
        $config['type'] = $config['type'] ? $config['type'] : 'image' ;
        Wind::import('SRV:verify.srv.PwVerifyService');
        $srv = new PwVerifyService('PwVerifyService_getVerifyType');
        if ($rand) {
            $srv->getVerify($config['type']);
            exit;
        }
        $url = WindUrlHelper::createUrl('verify/index/get', array('rand' => Pw::getTime()), '', 'pw');
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
                $url = WindUrlHelper::createUrl('verify/index/getAudio', array(
                    'songVolume' => 100,
                    'autoStart' => 'false',
                    'repeatPlay' => 'false',
                    'showDownload' => 'false',
                    'rand' => Pw::getTime(),
                ), '', 'pw');
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
                $url = WindUrlHelper::createUrl('verify/index/getAudio', array(
                    'songVolume' => 100,
                    'autoStart' => 'false',
                    'repeatPlay' => 'false',
                    'showDownload' => 'false',
                    'rand' => Pw::getTime(),
                ), '', 'pw');
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

    /**
     * 获取语音验证码
     */
    public function getAudioAction()
    {
        
        $srv = new PwVerifyCode();
        $srv->getAudioVerify();
        exit;
    }

    /**
     * 验证验证码
     */
    public function checkAction()
    {
        $code = $this->getInput('code');
        $veryfy = $this->_getVerifyService();
        if ($veryfy->checkVerify($code) !== true) {
            $this->showError('USER:verifycode.error');
        }
        $this->showMessage();
    }

    /**
     * 验证码服务
     *
     * @return PwCheckVerifyService
     */
    private function _getVerifyService()
    {
        return Wekit::load('verify.srv.PwCheckVerifyService');
    }
}
