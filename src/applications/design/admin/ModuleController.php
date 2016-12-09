<?php

Wind::import('ADMIN:library.AdminBaseController');
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>.
 *
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: ModuleController.php 28818 2013-05-24 10:10:46Z gao.wanggao $
 */
class ModuleController extends AdminBaseController
{
    public function run()
    {
        $isapi = $this->getInput('type');
        $ismodule = $this->getInput('ismodule');
        $model = $this->getInput('model');
        $moduleid = $this->getInput('moduleid');
        $modulename = $this->getInput('name');
        $pageid = $this->getInput('pageid');
        $page = (int) $this->getInput('page', 'get');
        $perpage = 10;
        $page = $page > 1 ? $page : 1;
        list($start, $perpage) = Pw::page2limit($page, $perpage);
        $ds = $this->_getDesignModuleDs();
        Wind::import('SRV:design.srv.vo.PwDesignModuleSo');
        $vo = new PwDesignModuleSo();
        $vo->setIsUse(1);
        if ($isapi == 'api') {
            $vo->setModuleType(PwDesignModule::TYPE_SCRIPT);
            $args['type'] = 'api';
        } else {
            $vo->setModuleType(PwDesignModule::TYPE_DRAG | PwDesignModule::TYPE_IMPORT);
        }
        if ($model) {
            $vo->setModelFlag($model);
            $args['model'] = $model;
        }
        if ($moduleid > 0) {
            $vo->setModuleId($moduleid);
            $args['moduleid'] = $moduleid;
        }
        if ($modulename) {
            $vo->setModuleName($modulename);
            $args['name'] = $modulename;
        }

        if ($pageid) {
            $vo->setPageId($pageid);
            $args['pageid'] = $pageid;
        }

        $vo->orderbyModuleId(false);
        $list = $ds->searchModule($vo, $start, $perpage);
        $count = $ds->countModule($vo);

        Wind::import('SRV:design.bo.PwDesignModelBo');
        $pageDs = $this->_getPageDs();
        foreach ($list as $k => $v) {
            $list[$k]['pageInfo'] = $pageDs->getPage($v['page_id']);
            $bo = new PwDesignModelBo($v['model_flag']);
            $model = $bo->getModel();
            $list[$k]['isdata'] = true;
            if ($model['tab'] && !in_array('data', $model['tab'])) {
                $list[$k]['isdata'] = false;
            }
        }
        $this->setOutput($this->_getDesignService()->getModelList(), 'models');
        $this->setOutput($args, 'args');
        $this->setOutput($list, 'list');
        $this->setOutput($count, 'count');
        $this->setOutput($page, 'page');
        $this->setOutput($perpage, 'perpage');
        $this->setOutput(ceil($count / $perpage), 'totalpage');
        $this->setOutput('design/module/run', 'pageurl');
        $this->setOutput($isapi, 'isapi');
        if ($isapi == 'api') {
            $this->setTemplate('module_api');
        }
    }

    public function deleteAction()
    {
        $moduleid = (int) $this->getInput('moduleid', 'post');
        if ($moduleid < 1) {
            $this->showError('operate.fail');
        }
        $this->_getDataDs()->deleteByModuleId($moduleid);
        $this->_getDesignModuleDs()->deleteModule($moduleid);
        $this->showMessage('operate.success');
    }

    public function scriptAction()
    {
        $moduleid = (int) $this->getInput('moduleid', 'get');
        if ($moduleid < 1) {
            $this->showError('operate.fail');
        }
        $module = $this->_getDesignModuleDs()->getModule($moduleid);
        if ($module['module_type'] != PwDesignModule::TYPE_SCRIPT) {
            $this->showError('operate.fail');
        }
        $script = $this->_getScriptDs()->getScript($moduleid);
        if (!$script) {
            $this->showError('operate.fail');
        }
        $apiUrl = WindUrlHelper::createUrl('design/api/run', array('token' => $script['token'], 'id' => $moduleid), '', 'pw');
        $this->setOutput('<design id="D_mod_'.$moduleid.'" role="module"></design>', 'value');
        $this->setOutput($apiUrl, 'apiUrl');
        $this->setOutput($module, 'module');
    }

    public function clearAction()
    {
        Wind::import('SRV:design.srv.vo.PwDesignModuleSo');
        $vo = new PwDesignModuleSo();
        $vo->setIsUse(0);
        $list = $this->_getDesignModuleDs()->searchModule($vo, 0, 0);
        $moduleDs = $this->_getDesignModuleDs();
        $permisDs = $this->_getPermissionsDs();
        $imageSrv = Wekit::load('design.srv.PwDesignImage');
        foreach ($list as $k => $v) {
            $permisDs->deleteByTypeAndDesignId(PwDesignPermissions::TYPE_MODULE, $k);
            $moduleDs->deleteModule($k);
            $imageSrv->clearFolder($k);
        }
        $this->showMessage('operate.success');
    }

    private function _getDesignService()
    {
        return Wekit::load('design.srv.PwDesignService');
    }

    private function _getDataDs()
    {
        return Wekit::load('design.PwDesignData');
    }

    private function _getPageDs()
    {
        return Wekit::load('design.PwDesignPage');
    }

    private function _getPushDs()
    {
        return Wekit::load('design.PwDesignPush');
    }

    private function _getPermissionsDs()
    {
        return Wekit::load('design.PwDesignPermissions');
    }

    private function _getDesignModuleDs()
    {
        return Wekit::load('design.PwDesignModule');
    }

    private function _getScriptDs()
    {
        return Wekit::load('design.PwDesignScript');
    }
}
