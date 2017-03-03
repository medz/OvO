<?php


/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>.
 *
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PushController.php 28899 2013-05-29 07:23:48Z gao.wanggao $
 */
class PushController extends PwBaseController
{
    public function beforeAction($handlerAdapter)
    {
        parent::beforeAction($handlerAdapter);
        Wekit::load('design.PwDesignPermissions');
        $permissions = $this->_getPermissionsService()->getPermissionsForUserGroup($this->loginUser->uid);
        if ($permissions < PwDesignPermissions::NEED_CHECK) {
            $this->showError('DESIGN:permissions.fail');
        }
    }

    public function addAction()
    {
        $fromid = (int) $this->getInput('fromid', 'get');
        $fromtype = $this->getInput('fromtype', 'get');
        if (!$fromtype) {
            $this->showError('operate.fail');
        }
        $data = $this->_getPushService()->getDataByFromid($fromtype, $fromid);
        if (!$data) {
            $this->showError('operate.fail');
        }
        $pageList = $this->_getPermissionsService()->getPermissionsAllPage($this->loginUser->uid);
        if (!$pageList) {
            $this->showError('push.page.empty');
        }

        $this->setOutput($pageList, 'pageList');

        $first = array_shift($pageList);
        $moduleList = $this->_getModuleDs()->fetchModule(explode(',', $first['module_ids']));
        foreach ($moduleList as $k => $module) {
            if ($module['model_flag'] != $fromtype) {
                unset($moduleList[$k]);
            }
        }
        $this->setOutput($moduleList, 'moduleList');
        $this->setOutput($fromtype, 'fromtype');
        $this->setOutput($fromid, 'fromid');
    }

    public function getmoduleAction()
    {
        $option = '';
        $pageid = (int) $this->getInput('pageid', 'post');
        $fromtype = $this->getInput('fromtype', 'post');
        $permissions = $this->_getPermissionsService()->getPermissionsForPage($this->loginUser->uid, $pageid);
        if ($permissions < PwDesignPermissions::NEED_CHECK) {
            $option = '<option value="">无可用模块</option>';
            $this->setOutput($option, 'data');
            $this->showMessage('operate.success');
        }
        $moduleList = $this->_getModuleDs()->getByPageid($pageid);
        foreach ($moduleList as $v) {
            if ($v['model_flag'] != $fromtype) {
                continue;
            }
            $option .= '<option value="'.$v['module_id'].'">'.$v['module_name'].'</option>';
        }
        if (!$option) {
            $option = '<option value="">无可用模块</option>';
        }
        $this->setOutput($option, 'html');
        $this->showMessage('operate.success');
    }

    public function doaddAction()
    {
        $pageid = (int) $this->getInput('pageid', 'post');
        $moduleid = (int) $this->getInput('moduleid', 'post');
        $isnotice = (int) $this->getInput('isnotice', 'post');
        $fromid = (int) $this->getInput('fromid', 'post');
        $fromtype = $this->getInput('fromtype', 'post');
        $start = $this->getInput('start_time', 'post');
        $end = $this->getInput('end_time', 'post');
        if ($moduleid < 1) {
            $this->showError('operate.fail');
        }
        $permiss = $this->_getPermissionsService()->getPermissionsForModule($this->loginUser->uid, $moduleid, $pageid);
        $pushService = $this->_getPushService();
        $data = $pushService->getDataByFromid($fromtype, $fromid);

         
        $bo = new PwDesignModuleBo($moduleid);
        $time = Pw::getTime();
        $startTime = $start ? Pw::str2time($start) : $time;
        $endTime = $end ? Pw::str2time($end) : $end;
        if ($end && $endTime < $time) {
            $this->showError('DESIGN:endtimd.error');
        }
        $pushDs = $this->_getPushDs();
         
        $dm = new PwDesignPushDm();
        $dm->setFromid($fromid)
            ->setModuleId($moduleid)
            ->setCreatedUserid($this->loginUser->uid)
            ->setCreatedTime($time)
            ->setStartTime($startTime)
            ->setEndTime($endTime)
            ->setAuthorUid($data['uid']);
        if ($isnotice) {
            $dm->setNeedNotice(1);
        }
        if ($permiss <= PwDesignPermissions::NEED_CHECK) {
            $dm->setStatus(PwDesignPush::NEEDCHECK);
            $isdata = false;
        } else {
            $isdata = true;
        }
        $resource = $pushService->addPushData($dm);
        if ($resource instanceof PwError) {
            $this->showError($resource->getError());
        }

        if ($isdata) {
            $pushService->pushToData((int) $resource);
            $pushService->afterPush((int) $resource);
        }
        $this->showMessage('operate.success');
    }

    private function _getDesignService()
    {
        return Wekit::load('design.srv.PwDesignService');
    }

    private function _getPushService()
    {
        return Wekit::load('design.srv.PwPushService');
    }

    protected function _getPermissionsService()
    {
        return Wekit::load('design.srv.PwDesignPermissionsService');
    }

    private function _getModuleDs()
    {
        return Wekit::load('design.PwDesignModule');
    }

    private function _getPageDs()
    {
        return Wekit::load('design.PwDesignPage');
    }

    private function _getPushDs()
    {
        return Wekit::load('design.PwDesignPush');
    }
}
