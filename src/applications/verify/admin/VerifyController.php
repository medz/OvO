<?php

Wind::import('ADMIN:library.AdminBaseController');

/**
 * 后台设置-验证机制配置.
 *
 * @author Qiong Wu <papa0924@gmail.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: VerifyController.php 28863 2013-05-28 03:22:39Z jieyin $
 */
class VerifyController extends AdminBaseController
{
    /* (non-PHPdoc)
     * @see WindController::run()
     */
    public function run()
    {
        Wind::import('SRV:verify.srv.PwVerifyService');
        $srv = new PwVerifyService('PwVerifyService_getVerifyType');
        $verifyType = $srv->getVerifyType();

        $config = Wekit::C()->getValues('verify');
        $this->setOutput($config, 'config');
        $this->setOutput($verifyType, 'verifyType');
    }

    /**
     * 配置增加表单处理器.
     */
    public function dorunAction()
    {
        $this->getRequest()->isPost() || $this->showError('operate.fail');
        $questions = $this->getInput('contentQuestions', 'post');
        $_questions = array();
        !$questions && $questions = array();
        foreach ($questions as $key => $value) {
            if (empty($value['ask']) && empty($value['answer'])) {
                continue;
            }
            if ($value['ask'] && empty($value['answer'])) {
                $this->showError('ADMIN:verify.answer.empty');
            }
            $_questions[] = $value;
        }
        $type = $this->getInput('type', 'post');
        if ($type == 'flash') {
            if (!class_exists('SWFBitmap')) {
                $this->showError('ADMIN:verify.flash.not.allow');
            }
        }
        $config = new PwConfigSet('verify');
        $config->set('type', $this->getInput('type', 'post'))
            ->set('randtype', $this->getInput('randtype', 'post'))
            ->set('content.type', $this->getInput('contentType', 'post'))
            ->set('content.length', $this->getInput('contentLength', 'post'))
            ->set('content.questions', $_questions)
            ->set('width', 240)
            ->set('height', 60)
            ->set('content.showanswer', $this->getInput('contentShowanswer', 'post'))
            ->set('voice', $this->getInput('voice', 'post'))
            ->flush();
        $this->showMessage('ADMIN:success');
    }

    /**
     * 站点设置.
     */
    public function setAction()
    {
        $config = Wekit::C()->getValues('verify');
        $this->setOutput($config, 'config');

        //扩展：key => title
        $verifyExt = array();
        $verifyExt = PwSimpleHook::getInstance('verify_showverify')->runWithFilters($verifyExt);
        $this->setOutput($verifyExt, 'verifyExt');
    }

    /**
     * 全局配置增加表单处理器.
     */
    public function dosetAction()
    {
        $this->getRequest()->isPost() || $this->showError('operate.fail');
        $ext = $this->getInput('ext', 'post');
        $extConfig = array();
        foreach ($ext as $key => $value) {
            if ($value == 1) {
                $extConfig[] = $key;
            }
        }
        $config = new PwConfigSet('verify');
        $config->set('showverify', $extConfig)->flush();
        $this->showMessage('ADMIN:success');
    }
}
