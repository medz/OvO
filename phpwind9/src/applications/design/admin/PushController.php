<?php

Wind::import('ADMIN:library.AdminBaseController');
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>.
 *
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PushController.php 28818 2013-05-24 10:10:46Z gao.wanggao $
 */
class PushController extends AdminBaseController
{
    public function run()
    {
        $page = (int) $this->getInput('page', 'get');
        $moduleid = (int) $this->getInput('moduleid');
        $pageid = (int) $this->getInput('pageid');
        $perpage = 10;
        $pushids = $pageids = $moduleids = $uids = $args = [];
        $page = $page > 1 ? $page : 1;
        list($start, $perpage) = Pw::page2limit($page, $perpage);
        if ($pageid) {
            $args = ['pageid' => $pageid];
        }
        if ($moduleid) {
            $args['moduleid'] = $moduleid;
        }
        if ($pageid && !$moduleid) {
            $pageinfo = $this->_getPageDs()->getPage($pageid);
            $moduleid = explode(',', $pageinfo['module_ids']);
        }
        $ds = $this->_getDataDs();

        $vo = new PwDesignDataSo();
        if ($moduleid) {
            $vo->setModuleid($moduleid);
        }
        $vo->setFromType(PwDesignData::FROM_PUSH);
        $list = $ds->searchData($vo, $perpage, $start);
        $count = $ds->countData($vo);
        $pagelist = $this->_getPageDs()->getPageList();
        foreach ($list as $k => $v) {
            $moduleids[] = $v['module_id'];
            $pushids[] = $v['from_id'];
            $_tmp = unserialize($v['extend_info']);
            $standard = unserialize($v['standard']);
            $list[$k]['title'] = $_tmp[$standard['sTitle']];
            $list[$k]['url'] = $_tmp[$standard['sUrl']];
            $list[$k]['intro'] = $_tmp[$standard['sIntro']];
        }
        array_unique($moduleids);
        $modules = $this->_getModuleDs()->fetchModule($moduleids);
        $pushs = $this->_getPushDs()->fetchPush($pushids);
        foreach ($pushs as $v) {
            $uids[] = $v['created_userid'];
        }
        $users = Wekit::load('user.PwUser')->fetchUserByUid($uids);
        foreach ($pushs as &$push) {
            $push['created_user'] = $users[$push['created_userid']]['username'];
        }
        $this->setOutput($moduleid, 'moduleid');
        $this->setOutput($pageid, 'pageid');
        $this->setOutput($modules, 'modules');
        $this->setOutput($pagelist, 'pagelist');
        $this->setOutput($pushs, 'pushs');
        $this->setOutput($list, 'list');
        $this->setOutput($args, 'args');
        $this->setOutput($count, 'count');
        $this->setOutput($page, 'page');
        $this->setOutput($perpage, 'perpage');
        $this->setOutput(ceil($count / $perpage), 'totalpage');
    }

    public function statusAction()
    {
        $status = (int) $this->getInput('status', 'get');
        $page = (int) $this->getInput('page', 'get');
        $moduleid = (int) $this->getInput('moduleid');
        $pageid = (int) $this->getInput('pageid');
        $perpage = 10;
        $pageids = $moduleids = $uids = [];
        $page = $page > 1 ? $page : 1;
        $args['status'] = $status;
        if ($moduleid) {
            $args['moduleid'] = $moduleid;
        }
        if ($pageid && !$moduleid) {
            $pageinfo = $this->_getPageDs()->getPage($pageid);
            $moduleid = explode(',', $pageinfo['module_ids']);
            $args['pageid'] = $pageid;
        }
        list($start, $perpage) = Pw::page2limit($page, $perpage);
        $time = Pw::getTime();
        $ds = $this->_getPushDs();
        $vo = Wekit::load('design.srv.vo.PwDesignPushSo');
        $moduleid && $vo->setModuleid($moduleid);
        if ($status == 1) {
            $vo->setStatus(1);
        }
        if ($status == 2) {
            $vo->setStatus(0);
        }
        $vo->orderbyPushid(false);
        $list = $ds->searchPush($vo, $perpage, $start);
        $count = $ds->countPush($vo);

        foreach ($list as $k => $v) {
            $uids[] = $v['created_userid'];
            $moduleids[] = $v['module_id'];
            $_tmp = unserialize($v['push_extend']);
            $standard = unserialize($v['push_standard']);
            $list[$k]['title'] = $_tmp[$standard['sTitle']];
            $list[$k]['url'] = $_tmp[$standard['sUrl']];
            $list[$k]['intro'] = $_tmp[$standard['sIntro']];
        }
        array_unique($uids);
        array_unique($moduleids);
        $modules = $this->_getModuleDs()->fetchModule($moduleids);

        $pagelist = $this->_getPageDs()->getPageList();
        $users = Wekit::load('user.PwUser')->fetchUserByUid($uids);
        $this->setOutput($moduleid, 'moduleid');
        $this->setOutput($pageid, 'pageid');
        $this->setOutput($pagelist, 'pagelist');
        $this->setOutput($list, 'list');
        $this->setOutput($users, 'users');
        $this->setOutput($modules, 'modules');
        $this->setOutput($args, 'args');
        $this->setOutput($count, 'count');
        $this->setOutput($page, 'page');
        $this->setOutput($perpage, 'perpage');
        $this->setOutput(ceil($count / $perpage), 'totalpage');
        $this->setOutput($status, 'status');
    }

    public function shieldAction()
    {
        $page = (int) $this->getInput('page', 'get');
        $perpage = 10;
        $page = $page > 1 ? $page : 1;
        list($start, $perpage) = Pw::page2limit($page, $perpage);
        $count = $this->_getShieldDs()->countShield(0);
        $list = $this->_getShieldDs()->getShieldList(0, $start, $perpage);
        $this->setOutput($list, 'list');
        $this->setOutput('', 'args');
        $this->setOutput($count, 'count');
        $this->setOutput($page, 'page');
        $this->setOutput($perpage, 'perpage');
        $this->setOutput(ceil($count / $perpage), 'totalpage');
    }

    public function doshieldAction()
    {
        $dataid = (int) $this->getInput('dataid', 'post');
        $ds = $this->_getDataDs();
        $data = $ds->getData($dataid);
        if (!$data) {
            $this->showError('operate.fail');
        }

        switch ($data['from_type']) {
            case PwDesignData::FROM_PUSH:
                $resource = $ds->deleteData($dataid);
                $this->_getPushDs()->deletePush($data['from_id']);
                //$this->_getPushDs()->updateStatus($data['from_id'], PwDesignPush::ISSHIELD);
                break;
            case PwDesignData::FROM_AUTO:
                $resource = $ds->deleteData($dataid);
                $this->_getShieldDs()->addShield($data['from_app'], $data['from_id'], $data['module_id']);
                break;
            default:
                $this->showError('operate.fail');
                break;
        }
        $extend = unserialize($data['extend_info']);
        $delImages = $extend['standard_image'];
        Wekit::load('design.srv.PwDesignImage')->clearFiles($this->bo->moduleid, explode('|||', $delImages));
        if (!$data['is_reservation']) {
            $srv = new PwShieldData($data['module_id']);
            $srv->addShieldData();
        }
        $this->showMessage('operate.success');
    }

    public function doshielddeleteAction()
    {
        $shieldid = (int) $this->getInput('shieldid', 'post');
        if ($this->_getShieldDs()->deleteShield($shieldid)) {
            $this->showMessage('operate.success');
        } else {
            $this->showError('operate.fail');
        }
    }

    public function doshielddeletesAction()
    {
        $shieldids = $this->getInput('shieldids', 'post');
        foreach ((array) $shieldids as $shieldid) {
            $this->_getShieldDs()->deleteShield($shieldid);
        }
        $this->showMessage('operate.success');
    }

    public function dopushAction()
    {
        $pushid = (int) $this->getInput('pushid', 'get');
        $pushDs = $this->_getPushDs();
        $push = $pushDs->getPush($pushid);
        $pushDs->updateStatus($pushid, PwDesignPush::ISSHOW);

        $srv = new PwAutoData($push['module_id']);
        $srv->addAutoData();
        $this->showMessage('operate.success');
    }

    public function delpushAction()
    {
        $pushid = (int) $this->getInput('pushid', 'get');
        if (!$pushid) {
            $this->showError('operate.fail');
        }
        $ds = $this->_getDataDs();
        $pushDs = $this->_getPushDs();
        $push = $pushDs->getPush($pushid);
        //TODO 权限
        if ($this->_getPushDs()->deletePush($pushid)) {
            $vo = new PwDesignDataSo();
            $vo->setModuleid($push['module_id']);
            $vo->setFromType(PwDesignData::FROM_PUSH);
            $vo->setFromid($pushid);
            $list = $ds->searchData($vo, 1, 0);
            if ($list) {
                $data = array_shift($list);
                $extend = unserialize($data['extend_info']);
                $delImages = $extend['standard_image'];
                Wekit::load('design.srv.PwDesignImage')->clearFiles($push['module_id'], explode('|||', $delImages));
            }
            $this->showMessage('operate.success');
        }
        $this->showError('operate.fail');
    }

    public function batchshieldAction()
    {
        $dataids = $this->getInput('dataids', 'post');
        $ds = $this->_getDataDs();

        foreach ($dataids as $dataid) {
            $data = $ds->getData($dataid);
            if (!$data) {
                continue;
            }

            switch ($data['from_type']) {
                case PwDesignData::FROM_PUSH:
                    $resource = $ds->deleteData($dataid);
                    //$this->_getPushDs()->updateStatus($data['from_id'], PwDesignPush::ISSHIELD);
                    $this->_getPushDs()->deletePush($data['from_id']);
                    break;
                case PwDesignData::FROM_AUTO:
                    $resource = $ds->deleteData($dataid);
                    $this->_getShieldDs()->addShield($data['from_app'], $data['from_id'], $data['module_id']);
                    break;
                default:
                    $this->showError('operate.fail');
                    break;
            }
            $srv = new PwShieldData($data['module_id']);
            $srv->addShieldData();
        }
        $this->showMessage('operate.success');
    }

    public function batchcheckAction()
    {
        $moduleids = [];
        $pushids = $this->getInput('pushids', 'post');
        $pushDs = $this->_getPushDs();
        $srv = $this->_getPushService();
        foreach ($pushids as $pushid) {
            $pushInfo = $pushDs->getPush($pushid);
            $pushDs->updateStatus($pushid, PwDesignPush::ISSHOW);
            $moduleids[] = $pushInfo['module_id'];
        }
        $moduleids = array_unique($moduleids);

        foreach ($moduleids as $moduleid) {
            $srv = new PwAutoData($moduleid);
            $srv->addAutoData();
        }
        //多模块不允许更新
        $this->showMessage('operate.success');
    }

    public function batchdeleteAction()
    {
        $pushids = $this->getInput('pushids', 'post');
        if ($this->_getPushDs()->batchDelete($pushids)) {
            $this->showMessage('operate.success');
        }
        $this->showMessage('operate.fail');
    }

    private function _getPushService()
    {
        return Wekit::load('design.srv.PwPushService');
    }

    private function _getPushDs()
    {
        return Wekit::load('design.PwDesignPush');
    }

    private function _getDataDs()
    {
        return Wekit::load('design.PwDesignData');
    }

    private function _getPageDs()
    {
        return Wekit::load('design.PwDesignPage');
    }

    private function _getModuleDs()
    {
        return Wekit::load('design.PwDesignModule');
    }

    private function _getShieldDs()
    {
        return Wekit::load('design.PwDesignShield');
    }
}
