<?php

Wind::import('LIB:base.PwBaseController');

/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: DesignBaseController.php 19273 2012-10-12 07:17:57Z gao.wanggao $
 * @package
 */
class DesignBaseController extends PwBaseController
{
    protected $bo;
    protected $pageid;
    /**
     * (non-PHPdoc)
     * @see src/library/base/PwBaseController::beforeAction()
     */
    public function beforeAction($handlerAdapter)
    {
        parent::beforeAction($handlerAdapter);
        $moduleid = $this->getInput('moduleid');
        Wind::import('SRV:design.bo.PwDesignModuleBo');
        $this->bo = new PwDesignModuleBo($moduleid);
        $module = $this->bo->getModule();
        if (!$module || $module['page_id'] < 1) {
            $this->showError('operate.fail');
        }
        $this->pageid = $module['page_id'];
        Wind::import('SRV:design.bo.PwDesignPageBo');
        $pageBo = new PwDesignPageBo($module['page_id']);
        if ($pageBo->getLock()) {
            $this->showError('DESIGN:page.edit.other.user');
        }
        $this->setOutput($moduleid, 'moduleid');
    }

    protected function _getPermissionsService()
    {
        return Wekit::load('design.srv.PwDesignPermissionsService');
    }
}
