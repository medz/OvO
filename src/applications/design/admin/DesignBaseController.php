<?php

Wind::import('ADMIN:library.AdminBaseController');
 
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>.
 *
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: DesignBaseController.php 16694 2012-08-27 10:42:54Z gao.wanggao $
 */
class DesignBaseController extends AdminBaseController
{
    protected $bo;

    /**
     * (non-PHPdoc).
     *
     * @see src/library/base/PwBaseController::beforeAction()
     */
    public function beforeAction($handlerAdapter)
    {
        parent::beforeAction($handlerAdapter);
        $isapi = '';
        $moduleid = $this->getInput('moduleid');
         
        $this->bo = new PwDesignModuleBo($moduleid);
        $module = $this->bo->getModule();
        if (!$module) {
            $this->showError('operate.fail');
        }
        if ($module['module_type'] == PwDesignModule::TYPE_SCRIPT) {
            $isapi = 'api';
        }
        $modelBo = new PwDesignModelBo($module['model_flag']);
        $model = $modelBo->getModel();
        $isdata = true;
        if ($model['tab'] && !in_array('data', $model['tab'])) {
            $isdata = false;
        }
        $this->setOutput($moduleid, 'moduleid');
        $this->setOutput($isapi, 'isapi');
        $this->setOutput($isdata, 'isdata');
    }

    protected function _getPermissionsService()
    {
        return Wekit::load('design.srv.PwDesignPermissionsService');
    }
}
