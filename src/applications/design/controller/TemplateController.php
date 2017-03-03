<?php

Wind::import('APPS:design.controller.DesignBaseController');
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>.
 *
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: TemplateController.php 28907 2013-05-30 02:02:15Z gao.wanggao $
 */
class TemplateController extends DesignBaseController
{
    public function beforeAction($handlerAdapter)
    {
        parent::beforeAction($handlerAdapter);
        Wekit::load('design.PwDesignPermissions');
        $permissions = $this->_getPermissionsService()->getPermissionsForModule($this->loginUser->uid, $this->bo->moduleid, $this->pageid);
        if ($permissions < PwDesignPermissions::IS_ADMIN) {
            $this->showError('DESIGN:permissions.fail');
        }
    }

    public function editAction()
    {
        $module = $this->bo->getModule();
        $compid = '';
        $components = $this->_getComponentDs()->getComponentByFlag($this->bo->getModel());
        $tpl = $this->bo->getTemplate();
        $this->setOutput($components, 'components');
        if ($tpl == '') {
            $comp = array_shift($components);
            if ($comp) {
                $compid = $comp['comp_id'];
                $tpl = $comp['comp_tpl'];
            }
        } else {
            $compid = $module['module_compid'];
        }
        $this->setOutput($this->bo->getSignKey(), 'signkeys');
        $this->setOutput($compid, 'compid');
        $this->setOutput($tpl, 'tpl');
    }

    public function getcompAction()
    {
        $compid = (int) $this->getInput('compid', 'post');
        $component = $this->_getComponentDs()->getComponent($compid);
        if ($compid) {
            $tpl = $component['comp_tpl'];
        } else {
            $tpl = $this->bo->getTemplate();
        }
        $this->setOutput($tpl, 'html');
        $this->showMessage('operate.success');
    }

    public function doeditAction()
    {
        $tpl = $this->getInput('tpl', 'post');
        $compid = (int) $this->getInput('compid', 'post');
        $tpl = $this->_getDesignService()->filterTemplate($tpl);
        if (!$this->_getDesignService()->checkTemplate($tpl)) {
            $this->showError('DESIGN:template.error');
        }
        $property = $this->bo->getProperty();
        $limit = $this->compileFor($tpl);
        $property['limit'] = $limit ? $limit : $property['limit'];
         
        $dm = new PwDesignModuleDm($this->bo->moduleid);
        $dm->setModuleTpl($tpl)
            ->setCompid($compid)
            ->setProperty($property);
        $resource = $this->_getModuleDs()->updateModule($dm);
        if ($resource instanceof PwError) {
            $this->showError($resource->getError());
        }

        //更新模版
        $module = $this->bo->getModule();
        if ($module['module_type'] == PwDesignModule::TYPE_IMPORT) {
             
            $pageBo = new PwDesignPageBo($this->pageid);
            $pageInfo = $pageBo->getPage();
             
            $compile = new PwPortalCompile($pageBo);
            if ($pageInfo['page_type'] == PwDesignPage::PORTAL) {
                $compile->replaceList($this->bo->moduleid, $tpl);
            } elseif ($pageInfo['page_type'] == PwDesignPage::SYSTEM) {
                !$module['segment'] && $module['segment'] = '';
                $compile->replaceList($this->bo->moduleid, $tpl, $module['segment']);
            }
        }
        //更机数据
         
        $srv = new PwAutoData($this->bo->moduleid);
        $srv->addAutoData();
        $this->showMessage('operate.success');
    }

    public function dosaveAction()
    {
        $tplname = $this->getInput('tplname', 'post');
        $tpl = $this->getInput('tpl', 'post');
        $tpl = $this->_getDesignService()->filterTemplate($tpl);
        if (!$this->_getDesignService()->checkTemplate($tpl)) {
            $this->showError('DESIGN:template.error');
        }
        $return = $this->_getComponentDs()->addComponent($this->bo->getModel(), $tplname, $tpl);
        if ($return) {
            $this->showMessage('operate.success');
        }
        $this->showError('operate.success');
    }

    /**
     * 对<for:1>进行解析
     * Enter description here ...
     */
    protected function compileFor($section)
    {
        $limit = 0;
        if (preg_match('/\<for:(\d+)>/isU', $section, $matches)) {
            $limit = (int) $matches[1];
        }

        return $limit;
    }

    private function _getDesignService()
    {
        return Wekit::load('design.srv.PwDesignService');
    }

    private function _getCompileService()
    {
        return Wekit::load('design.srv.PwDesignCompile');
    }

    private function _getDataDs()
    {
        return Wekit::load('design.PwDesignData');
    }

    private function _getPushDs()
    {
        return Wekit::load('design.PwDesignPush');
    }

    private function _getModuleDs()
    {
        return Wekit::load('design.PwDesignModule');
    }

    private function _getComponentDs()
    {
        return Wekit::load('design.PwDesignComponent');
    }
}
