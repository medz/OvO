<?php

Wind::import('APPS:design.admin.DesignBaseController');
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>.
 *
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: TemplateController.php 28936 2013-05-31 02:50:17Z gao.wanggao $
 */
class TemplateController extends DesignBaseController
{
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
        $this->setOutput($this->getInput('isscript', 'get'), 'isscript');
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
        Wind::import('SRV:design.dm.PwDesignModuleDm');
        $dm = new PwDesignModuleDm($this->bo->moduleid);
        $dm->setModuleTpl($tpl)
            ->setCompid($compid)
            ->setProperty($property);
        $resource = $this->_getModuleDs()->updateModule($dm);
        if ($resource instanceof PwError) {
            $this->showError($resource->getError());
        }

        $module = $this->bo->getModule();
        Wekit::load('design.srv.PwSegmentService')->updateSegmentByPageId($module['page_id']);
        Wind::import('SRV:design.srv.data.PwAutoData');
        $srv = new PwAutoData($this->bo->moduleid);
        $srv->addAutoData();
        $this->_getDesignService()->clearCompile();
        if ($module['module_type'] == PwDesignModule::TYPE_SCRIPT) {
            $this->showMessage('operate.success', 'design/module/run?type=api', true);
        } else {
            $this->showMessage('operate.success');
        }
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
        $this->showError('operate.fail');
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

    private function _getPageDs()
    {
        return Wekit::load('design.PwDesignPage');
    }

    private function _getSegmentDs()
    {
        return Wekit::load('design.PwDesignSegment');
    }

    private function _getPushDs()
    {
        return Wekit::load('design.PwDesignPush');
    }

    private function _getDataDs()
    {
        return Wekit::load('design.PwDesignData');
    }

    private function _getModuleDs()
    {
        return Wekit::load('design.PwDesignModule');
    }

    public function _getComponentDs()
    {
        return Wekit::load('design.PwDesignComponent');
    }
}
