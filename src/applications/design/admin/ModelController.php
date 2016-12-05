<?php

Wind::import('ADMIN:library.AdminBaseController');
/**
 * the last known user to change this file in the repository  <$LastChangedBy: liusanbian $>.
 *
 * @author $Author: liusanbian $ Foxsee@aliyun.com
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: ModelController.php 12232 2012-06-19 17:37:18Z liusanbian $
 */
class ModelController extends AdminBaseController
{
    public function run()
    {
        $this->setOutput($this->_getDesignModelDs()->getModelList(), 'list');
    }

    public function addAction()
    {
        $this->setOutput($this->_getDesignService()->getDesignModelType(), 'types');
    }

    public function doaddAction()
    {
        $resource = $this->_getDesignModelDs()->addModel($this->getInput('flag', 'post'), $this->getInput('name', 'post'), $this->getInput('type', 'post'), $this->getInput('signkeys', 'post'));
        if (!$resource) {
            $this->showError('operate.fail');
        }
        $this->showMessage('operate.success');
    }

    public function editAction()
    {
        $flag = $this->getInput('flag', 'get');
        if (!$flag) {
            return $this->showError('operate.fail');
        }
        $this->setOutput($this->_getDesignModelDs()->getModel($flag), 'info');
        $this->setOutput($this->_getDesignService()->getDesignModelType(), 'types');
    }

    public function doeditAction()
    {
        $flag = $this->getInput('flag', 'post');
        if (!$flag) {
            $this->showError('operate.fail');
        }
        $resource = $this->_getDesignModelDs()->updateModel($flag, $this->getInput('name', 'post'), $this->getInput('type', 'post'), $this->getInput('signkeys', 'post'));
        if (!$resource) {
            $this->showError('operate.fail');
        }
        $this->showMessage('operate.success');
    }

    /**
     * getDesignService.
     *
     * @return PwDesignService
     */
    private function _getDesignService()
    {
        return Wekit::load('design.srv.PwDesignService');
    }

    /**
     * getDesignModelDs.
     *
     * @return PwDesignModel
     */
    private function _getDesignModelDs()
    {
        return Wekit::load('design.PwDesignModel');
    }
}
