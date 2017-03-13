<?php

Wind::import('ADMIN:library.AdminBaseController');

/**
 * the last known user to change this file in the repository  <$LastChangedBy: xiaoxia.xuxx $>.
 *
 * @author $Author: xiaoxia.xuxx $ Foxsee@aliyun.com
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PropertyController.php 24134 2013-01-22 06:19:24Z xiaoxia.xuxx $
 */
class PropertyController extends AdminBaseController
{
    protected $bo;

    public function beforeAction($handlerAdapter)
    {
        parent::beforeAction($handlerAdapter);
        $isapi = '';
        $isdata = true;
        $moduleid = $this->getInput('moduleid');
        if ($moduleid) {
            $this->bo = new PwDesignModuleBo($moduleid);
            $module = $this->bo->getModule();
            if ($module && $module['module_type'] == PwDesignModule::TYPE_SCRIPT) {
                $isapi = 'api';
            }
            $modelBo = new PwDesignModelBo($module['model_flag']);
            $model = $modelBo->getModel();
            if ($model['tab'] && !in_array('data', $model['tab'])) {
                $isdata = false;
            }
        }
        $this->setOutput($moduleid, 'moduleid');
        $this->setOutput($isapi, 'isapi');
        $this->setOutput($isdata, 'isdata');
    }

    public function add1Action()
    {
        $this->setOutput($this->_getDesignService()->getModelList(), 'models');
    }

    public function add2Action()
    {
        $model = $this->getInput('model', 'post');
        if (!$model) {
            $this->showError('operate.fail');
        }

        $bo = new PwDesignModelBo($model);
        if (!$bo->isModel()) {
            $this->showError('operate.fail');
        }
        $cls = sprintf('PwDesign%sDataService', ucwords($model));

        $service = new $cls();
        $decorator = $service->decorateAddProperty($model);

        $modelBo = new PwDesignModelBo($model);
        $cache['expired'] = 15;
        $this->setOutput($cache, 'cache');
        $this->setOutput($bo->getProperty(), 'property');
        $this->setOutput($decorator, 'decorator');
        $this->setOutput($model, 'model');
        $this->setOutput($modelBo->getModel(), 'modelInfo');
    }

    public function doaddAction()
    {
        $model = $this->getInput('model', 'post');
        if (!$model) {
            $this->showError('operate.fail');
        }

        $bo = new PwDesignModelBo($model);
        if (!$bo->isModel()) {
            $this->showError('operate.fail');
        }
        $name = trim($this->getInput('module_name', 'post'));
        if (empty($name)) {
            $this->showError('DESIGN:module.name.empty');
        }
        $cache = $this->getInput('cache', 'post');
        $property = $this->getInput('property', 'post');
        if ($property['limit'] > 200) {
            $this->showError('DESIGN:maxlimit.error');
        }
        $cls = sprintf('PwDesign%sDataService', ucwords($model));

        $service = new $cls();
        if (method_exists($service, 'decorateSaveProperty')) {
            $property = $service->decorateSaveProperty($property);
            if ($property  instanceof PwError) {
                $this->showError($property->getError());
            }
        }

        $ds = $this->_getModuleDs();

        $dm = new PwDesignModuleDm();
        $dm->setFlag($model)
            ->setName($name)
            ->setProperty($property)
            ->setCache($cache)
            ->setModuleType(PwDesignModule::TYPE_SCRIPT)
            ->setIsused(1);
        if ($property['html_tpl']) {
            $dm->setModuleTpl($property['html_tpl']);
        }
        $resource = $ds->addModule($dm);
        if ($resource instanceof PwError) {
            $this->showError($resource->getError());
        }
        $moduleid = (int) $resource;

        $srv = new PwAutoData($moduleid);
        $srv->addAutoData();
        //调用模块token
        $token = WindUtility::generateRandStr(10);
        $this->_getScriptDs()->addScript((int) $moduleid, $token, 0);

        $tab = ['property', 'template'];

        $bo = new PwDesignModelBo($model);
        $modelInfo = $bo->getModel();
        if (is_array($modelInfo['tab'])) {
            foreach ($tab as $k => $v) {
                if (in_array($v, $modelInfo['tab'])) {
                    $_tab[] = $tab[$k];
                }
            }
            $tab = $_tab;
        }

        if (in_array('template', $tab)) {
            $this->showMessage('operate.success', 'design/template/edit?isscript=1&moduleid='.$moduleid, true);
        } else {
            $this->showMessage('operate.success', 'design/module/run?type=api', true);
        }
    }

    public function editAction()
    {
        $isedit = false;
        $model = $this->getInput('model', 'get'); //前台为post
        if ($model) {
            $isedit = true;
            $this->bo->setModel($model);
        } else {
            $model = $this->bo->getModel();
        }
        $module = $this->bo->getModule();
        if (!$model) {
            $this->showError('operate.fail');
        }
        $cls = sprintf('PwDesign%sDataService', ucwords($model));

        $service = new $cls();
        $decorator = $service->decorateEditProperty($this->bo);

        $modelBo = new PwDesignModelBo($model);
        $property = $modelBo->getProperty();
        $vProperty = $isedit ? [] : $this->bo->getProperty();
        $isedit && $vProperty['compid'] = null;
        $service = $this->_getDesignService();
        $types = $service->getDesignModelType();
        $models = $service->getModelList();
        foreach ($models as $k => $v) {
            $_models[$v['type']][] = ['name' => $v['name'], 'model' => $k];
        }
        $this->setOutput($types, 'types');
        $this->setOutput($_models, 'models');
        $this->setOutput($model, 'model');
        $this->setOutput($modelBo->getProperty(), 'property');
        $this->setOutput($decorator, 'decorator');
        $this->setOutput($module, 'module');
        $this->setOutput($vProperty, 'vProperty');
        $this->setOutput($this->bo->getCache(), 'cache');
        $this->setOutput($modelBo->getModel(), 'modelInfo');
        $this->setOutput($isedit, 'isedit');
    }

    public function doeditAction()
    {
        $model = $this->getInput('model', 'post');
        $moduleid = $this->getInput('moduleid', 'post');
        if (!$moduleid) {
            $this->showError('operate.fail');
        }

        $moduleBo = new PwDesignModuleBo($moduleid);
        $_model = $moduleBo->getModel();
        if ($model != $_model) {
            $this->_getDataDs()->deleteByModuleId($moduleid);
            $this->_getPushDs()->deleteByModuleId($moduleid);
        }
        !$model && $model = $_model;
        $module = $moduleBo->getModule();
        if (!$module) {
            $this->showError('operate.fail');
        }
        $name = trim($this->getInput('module_name', 'post'));
        if (empty($name)) {
            $this->showError('DESIGN:module.name.empty');
        }
        $cache = $this->getInput('cache', 'post');
        $property = $this->getInput('property', 'post');
        if ($property['limit'] > 200) {
            $this->showError('DESIGN:maxlimit.error');
        }
        $cls = sprintf('PwDesign%sDataService', ucwords($model));

        $service = new $cls();
        if (method_exists($service, 'decorateSaveProperty')) {
            $property = $service->decorateSaveProperty($property);
            if ($property  instanceof PwError) {
                $this->showError($property->getError());
            }
        }

        $dm = new PwDesignModuleDm($moduleid);
        $dm->setFlag($model)
            ->setName($name)
            ->setProperty($property)
            ->setCache($cache);
        if ($property['html_tpl']) {
            $dm->setModuleTpl($property['html_tpl']);
        }
        $resource = $this->_getModuleDs()->updateModule($dm);
        if ($resource instanceof PwError) {
            $this->showError($resource->getError());
        }

        Wekit::load('design.srv.PwSegmentService')->updateSegmentByPageId($module['page_id']);
        $this->_getDesignService()->clearCompile();

        $srv = new PwAutoData($moduleid);
        $srv->addAutoData();
        $this->showMessage('operate.success');
    }

    private function _getCompileService()
    {
        return Wekit::load('design.srv.PwDesignCompile');
    }

    private function _getDesignService()
    {
        return Wekit::load('design.srv.PwDesignService');
    }

    private function _getComponentDs()
    {
        return Wekit::load('design.PwDesignComponent');
    }

    private function _getShieldDs()
    {
        return Wekit::load('design.PwDesignShield');
    }

    private function _getPageDs()
    {
        return Wekit::load('design.PwDesignPage');
    }

    private function _getDataDs()
    {
        return Wekit::load('design.PwDesignData');
    }

    private function _getPushDs()
    {
        return Wekit::load('design.PwDesignPush');
    }

    private function _getModelDs()
    {
        return Wekit::load('design.PwDesignModel');
    }

    private function _getModuleDs()
    {
        return Wekit::load('design.PwDesignModule');
    }

    private function _getSegmentDs()
    {
        return Wekit::load('design.PwDesignSegment');
    }

    private function _getScriptDs()
    {
        return Wekit::load('design.PwDesignScript');
    }
}
