<?php


/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>.
 *
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PropertyController.php 24726 2013-02-18 06:15:04Z gao.wanggao $
 */
class PropertyController extends PwBaseController
{
    public function addAction()
    {
        $struct = $this->getInput('struct', 'post');
        $model = $this->getInput('model', 'post');
        $pageid = $this->getInput('pageid', 'post');
        if (!$model) {
            $this->showError('operate.fail');
        }
        Wekit::load('design.PwDesignPermissions');
        $permissions = $this->_getPermissionsService()->getPermissionsForPage($this->loginUser->uid, $pageid);
        if ($permissions < PwDesignPermissions::IS_DESIGN) {
            $this->showError('DESIGN:permissions.fail');
        }
         
        $bo = new PwDesignModelBo($model);
        if (!$bo->isModel()) {
            $this->showError('operate.fail');
        }
        $cls = sprintf('PwDesign%sDataService', ucwords($model));
         
        $service = new $cls();
        $decorator = $service->decorateAddProperty($model);
        $_models = array();
        $service = $this->_getDesignService();
        $types = $service->getDesignModelType();
        $models = $service->getModelList();
        foreach ($models as $k => $v) {
            $_models[$v['type']][] = array('name' => $v['name'], 'model' => $k);
        }
        $ds = $this->_getModuleDs();
        $pageInfo = $this->_getPageDs()->getPage($pageid);
        $module['module_name'] = $pageInfo['page_name'].'_'.WindUtility::generateRandStr(4);
        $cache['expired'] = 15;
        $this->setOutput($cache, 'cache');
        $this->setOutput($module, 'module');
        $this->setOutput($types, 'types');
        $this->setOutput($_models, 'models');
        $this->setOutput($bo->getProperty(), 'property');
        $this->setOutput($bo->getModel(), 'modelInfo');
        $this->setOutput($decorator, 'decorator');
        $this->setOutput($model, 'model');
        $this->setOutput($pageid, 'pageid');
        $this->setOutput($struct, 'struct');
    }

    public function doaddAction()
    {
        $struct = $this->getInput('struct', 'post');
        $pageid = $this->getInput('pageid', 'post');
        $model = $this->getInput('model', 'post');
        if (!$model || $pageid < 1) {
            $this->showError('operate.fail');
        }
        Wekit::load('design.PwDesignPermissions');
        $permissions = $this->_getPermissionsService()->getPermissionsForPage($this->loginUser->uid, $pageid);
        if ($permissions < PwDesignPermissions::IS_DESIGN) {
            $this->showError('DESIGN:permissions.fail');
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

        isset($property['html']) && $property['html'] = $this->_getDesignService()->filterTemplate($property['html']);

        if ($property['limit'] > 200) {
            $this->showError('DESIGN:maxlimit.error');
        }
        $cls = sprintf('PwDesign%sDataService', ucwords($model));
         
        $service = new $cls();

        $ds = $this->_getModuleDs();
         
        $dm = new PwDesignModuleDm();
        $dm->setPageId($pageid)
            ->setStruct($struct)
            ->setFlag($model)
            ->setName($name)
            ->setCache($cache)
            ->setModuleType(PwDesignModule::TYPE_DRAG)
            ->setIsused(1);
        $resource = $ds->addModule($dm);
        if ($resource instanceof PwError) {
            $this->showError($resource->getError());
        }

        $dm = new PwDesignModuleDm($resource);
        if (method_exists($service, 'decorateSaveProperty')) {
            $property = $service->decorateSaveProperty($property, $resource);
            if ($property  instanceof PwError) {
                $this->showError($property->getError());
            }
        }
        $dm->setProperty($property);
        if ($property['html_tpl']) {
            $dm->setModuleTpl($property['html_tpl']);
        }
        $r = $ds->updateModule($dm);
        if ($r instanceof PwError) {
            $this->showError($r->getError());
        }

         
        $srv = new PwAutoData($resource);
        $srv->addAutoData();

        $this->setOutput($resource, 'data');
        $this->showMessage('operate.success');
    }

    public function editAction()
    {
        $other = array('html', 'searchbar', 'image');
        $isedit = false;
        $model = $this->getInput('model', 'post');
        $moduleid = (int) $this->getInput('moduleid', 'post');
         
        $moduleBo = new PwDesignModuleBo($moduleid);
        if ($model) {
            $isedit = true;
            $moduleBo->setModel($model);
        } else {
            $model = $moduleBo->getModel();
        }
        $module = $moduleBo->getModule();
        if (!$model) {
            $this->showError('operate.fail');
        }
        Wekit::load('design.PwDesignPermissions');
        $permissions = $this->_getPermissionsService()->getPermissionsForModule($this->loginUser->uid, $moduleid, $module['page_id']);
        if ($permissions < PwDesignPermissions::IS_ADMIN && !in_array($module['model_flag'], $other)) {
            $this->showError('DESIGN:permissions.fail');
        }
        if ($permissions < PwDesignPermissions::IS_PUSH) {
            $this->showError('DESIGN:permissions.fail');
        }
        $cls = sprintf('PwDesign%sDataService', ucwords($model));
         
        $service = new $cls();
        $decorator = $service->decorateEditProperty($moduleBo);
         
        $modelBo = new PwDesignModelBo($model);
        $property = $modelBo->getProperty();
        $vProperty = $isedit ? array() : $moduleBo->getProperty();
        //$isedit && $vProperty['compid'] = null;
        $service = $this->_getDesignService();
        $types = $service->getDesignModelType();
        $models = $service->getModelList();
        foreach ($models as $k => $v) {
            $_models[$v['type']][] = array('name' => $v['name'], 'model' => $k);
        }
        $this->setOutput($types, 'types');
        $this->setOutput($_models, 'models');
        $this->setOutput($model, 'model');
        $this->setOutput($modelBo->getProperty(), 'property');
        $this->setOutput($decorator, 'decorator');
        $this->setOutput($module, 'module');
        $this->setOutput($vProperty, 'vProperty');
        $this->setOutput($moduleBo->getCache(), 'cache');
        $this->setOutput($modelBo->getModel(), 'modelInfo');
        $this->setOutput($isedit, 'isedit');
    }

    public function doeditAction()
    {
        $other = array('html', 'searchbar', 'image');
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
        if (!$module || $module['page_id'] < 1) {
            $this->showError('operate.fail');
        }

         
        $pageBo = new PwDesignPageBo($module['page_id']);
        if ($pageBo->getLock()) {
            $this->showError('DESIGN:page.edit.other.user');
        }

        Wekit::load('design.PwDesignPermissions');
        $permissions = $this->_getPermissionsService()->getPermissionsForModule($this->loginUser->uid, $moduleid, $module['page_id']);
        if ($permissions < PwDesignPermissions::IS_ADMIN && !in_array($module['model_flag'], $other)) {
            $this->showError('DESIGN:permissions.fail');
        }
        if ($permissions < PwDesignPermissions::IS_PUSH) {
            $this->showError('DESIGN:permissions.fail');
        }
        $name = trim($this->getInput('module_name', 'post'));
        if (empty($name)) {
            $this->showError('DESIGN:module.name.empty');
        }
        $cache = $this->getInput('cache', 'post');
        $property = $this->getInput('property', 'post');
        $property['html'] = $this->_getDesignService()->filterTemplate($property['html']);
        if (!$this->_getDesignService()->checkTemplate($property['html'])) {
            $this->showError('DESIGN:template.error');
        }
        //
        if ($property['limit'] > 200) {
            $this->showError('DESIGN:maxlimit.error');
        }
        $cls = sprintf('PwDesign%sDataService', ucwords($model));
         
        $service = new $cls();
        if (method_exists($service, 'decorateSaveProperty')) {
            $property = $service->decorateSaveProperty($property, $moduleid);
            if ($property  instanceof PwError) {
                $this->showError($property->getError());
            }
        }
         
        $dm = new PwDesignModuleDm($moduleid);
        $dm->setFlag($model)
            ->setName($name)
            ->setProperty($property)
            ->setCache($cache);
        if (isset($property['html_tpl'])) {
            $dm->setModuleTpl($property['html_tpl']);
        }
        $resource = $this->_getModuleDs()->updateModule($dm);
        if ($resource instanceof PwError) {
            $this->showError($resource->getError());
        }

         
        $srv = new PwAutoData($moduleid);
        $srv->addAutoData();
        $this->showMessage('operate.success');
    }

    /**
     * 对模块进行删除
     * PS:不是真正的删除，记录为isused = 0状态
     */
    public function deleteAction()
    {
        $moduleid = (int) $this->getInput('moduleid', 'post');
        $module = $this->_getModuleDs()->getModule($moduleid);
        if (!$module || $module['page_id'] < 1) {
            $this->showError('operate.fail');
        }

        Wekit::load('design.PwDesignPermissions');
        $permissions = $this->_getPermissionsService()->getPermissionsForModule($this->loginUser->uid, $moduleid);
        if ($permissions < PwDesignPermissions::IS_DESIGN) {
            $this->showError('DESIGN:permissions.fail');
        }
         
        $pageBo = new PwDesignPageBo($module['page_id']);
        if ($pageBo->getLock()) {
            $this->showError('DESIGN:page.edit.other.user');
        }
         
        $dm = new PwDesignModuleDm($moduleid);
        $dm->setIsused(0);
        $resource = $this->_getModuleDs()->updateModule($dm);
        if ($resource instanceof PwError) {
            $this->showError($resource->getError());
        }
        //if (!$this->_getModuleDs()->deleteModule($moduleid)) $this->showMessage("operate.fail");
        $this->_getDataDs()->deleteByModuleId($moduleid);
        Wekit::load('design.PwDesignPush')->deleteByModuleId($moduleid);

        //删除导入数据的模版内容
        $dir = Wind::getRealDir('THEMES:portal.local.');
        $path = $dir.$pageBo->getTplPath().'/template/';
        $files = WindFolder::read($path, WindFolder::READ_FILE);
        foreach ($files as $file) {
            $filePath = $path.$file;
            $content = WindFile::read($filePath);
            if (!$content) {
                continue;
            }
            $tmp = preg_replace('/\<pw-list\s*id=\"'.$moduleid.'\"\s*>(.+)<\/pw-list>/isU', '', $content);
            if ($tmp != $content) {
                WindFile::write($filePath, $tmp);
            }
        }
        $imageSrv = Wekit::load('design.srv.PwDesignImage');
        $imageSrv->clearFolder($moduleid);
        $this->showMessage('operate.success');
    }

    public function gettabAction()
    {
        $model = $this->getInput('model', 'post');
        $pageid = $this->getInput('pageid', 'post');
        if (!$model) {
            $this->showError('operate.fail');
        }
        Wekit::load('design.PwDesignPermissions');
        $permissions = $this->_getPermissionsService()->getPermissionsForPage($this->loginUser->uid, $pageid);
        if ($permissions < PwDesignPermissions::IS_DESIGN) {
            $this->showError('DESIGN:permissions.fail');
        }

        //对config里的tab进行过滤
        $tab = array('property', 'template');
         
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
        $this->setOutput($tab, 'data');
        $this->showMessage('operate.success');
    }

    protected function _getPermissionsService()
    {
        return Wekit::load('design.srv.PwDesignPermissionsService');
    }

    private function _getDesignService()
    {
        return Wekit::load('design.srv.PwDesignService');
    }

    private function _getComponentDs()
    {
        return Wekit::load('design.PwDesignComponent');
    }

    private function _getPageDs()
    {
        return Wekit::load('design.PwDesignPage');
    }

    private function _getShieldDs()
    {
        return Wekit::load('design.PwDesignShield');
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

    private function _getBakDs()
    {
        return Wekit::load('design.PwDesignBak');
    }
}
