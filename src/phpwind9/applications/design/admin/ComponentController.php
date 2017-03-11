<?php

Wind::import('ADMIN:library.AdminBaseController');
/**
 * the last known user to change this file in the repository  <$LastChangedBy: jieyin $>.
 *
 * @author $Author: jieyin $ Foxsee@aliyun.com
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: ComponentController.php 28810 2013-05-24 08:50:05Z jieyin $
 */
class ComponentController extends AdminBaseController
{
    public function run()
    {
        $page = (int) $this->getInput('page', 'get');
        $flag = $this->getInput('flag');
        $compid = (int) $this->getInput('compid');
        $compname = $this->getInput('compname');
        $perpage = 10;
        $args = array();
        $page = $page > 1 ? $page : 1;
        list($start, $perpage) = Pw::page2limit($page, $perpage);

        $vo = new PwDesignComponentSo();
        if ($flag) {
            $vo->setModelFlag($flag);
            $args['flag'] = $flag;
        }
        if ($compid > 0) {
            $vo->setCompid($compid);
            $args['compid'] = $compid;
        }
        if ($compname) {
            $vo->setCompname($compname);
            $args['compname'] = $compname;
        }

        $list = $this->_getDesignComponentDs()->searchComponent($vo, $start, $perpage);
        $count = $this->_getDesignComponentDs()->countComponent($vo);
        $models = $this->_getDesignService()->getModelList();
        $this->setOutput($args, 'args');
        $this->setOutput($flag, 'flag');
        $this->setOutput($list, 'list');
        $this->setOutput($models, 'models');
        $this->setOutput($count, 'count');
        $this->setOutput($page, 'page');
        $this->setOutput($perpage, 'perpage');
    }

    public function add1Action()
    {
        $this->setOutput($this->_getDesignService()->getModelList(), 'models');
    }

    public function add2Action()
    {
        $flag = $this->getInput('flag', 'post');
        if (!$flag) {
            $this->forwardRedirect(WindUrlHelper::createUrl('design/component/add1'));
        }

        $bo = new PwDesignModelBo($flag);
        $this->setOutput($flag, 'flag');
        $this->setOutput($bo->getSignKeys(), 'signKeys');
    }

    public function doadd2Action()
    {
        $flag = $this->getInput('flag', 'post');
        $name = $this->getInput('name', 'post');
        $tpl = $this->getInput('tpl', 'post');
        $tpl = $this->_getDesignService()->filterTemplate($tpl);
        if (!$this->_getDesignService()->checkTemplate($tpl)) {
            $this->showError('DESIGN:template.error');
        }
        $resource = $this->_getDesignComponentDs()->addComponent($flag, $name, $tpl);
        if (!$resource) {
            $this->showMessage('operate.fail');
        }
        $this->showMessage('operate.success', 'design/component/run', true);
    }

    public function editAction()
    {
        $id = (int) $this->getInput('id', 'get');
        $page = (int) $this->getInput('page', 'get');
        $comp = $this->_getDesignComponentDs()->getComponent($id);
        if (!$comp) {
            $this->showMessage('operate.fail');
        }

        $bo = new PwDesignModelBo($comp['model_flag']);
        $this->setOutput($bo->getSignKeys(), 'signKeys');
        $this->setOutput($comp, 'comp');
        $this->setOutput($page, 'page');
    }

    public function doeditAction()
    {
        $page = (int) $this->getInput('page', 'post');
        $id = (int) $this->getInput('compid', 'post');
        $flag = $this->getInput('flag', 'post');
        $name = $this->getInput('name', 'post');
        $tpl = $this->getInput('tpl', 'post');
        $tpl = $this->_getDesignService()->filterTemplate($tpl);
        if (!$this->_getDesignService()->checkTemplate($tpl)) {
            $this->showError('DESIGN:template.error');
        }
        if ($id < 1) {
            $this->showError('operate.fail');
        }
        $resource = $this->_getDesignComponentDs()->updateComponent($id, $flag, $name, $tpl);
        if (!$resource) {
            $this->showMessage('operate.fail');
        }
        $this->showMessage('operate.success', 'design/component/run?page='.$page, true);
    }

    public function delAction()
    {
        $id = (int) $this->getInput('id', 'post');
        if (!$id) {
            $this->showMessage('operate.fail');
        }
        $resource = $this->_getDesignComponentDs()->deleteComponent($id);
        if (!$resource) {
            $this->showMessage('operate.fail');
        }
        $this->showMessage('operate.success');
    }

    private function _getDesignService()
    {
        return Wekit::load('design.srv.PwDesignService');
    }

    private function _getDesignComponentDs()
    {
        return Wekit::load('design.PwDesignComponent');
    }
}
