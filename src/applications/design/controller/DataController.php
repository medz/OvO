<?php

Wind::import('APPS:design.controller.DesignBaseController');
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: DataController.php 24487 2013-01-31 02:50:53Z gao.wanggao $
 * @package
 */
class DataController extends DesignBaseController
{
    public function beforeAction($handlerAdapter)
    {
        parent::beforeAction($handlerAdapter);
        Wekit::load('design.PwDesignPermissions');
        $permissions = $this->_getPermissionsService()->getPermissionsForModule($this->loginUser->uid, $this->bo->moduleid, $this->pageid);
        if ($permissions < PwDesignPermissions::IS_PUSH) {
            $this->showError('DESIGN:permissions.fail');
        }
    }


    public function run()
    {
        $this->setOutput($this->bo->getData(), 'list');
    }

    public function addAction()
    {
        $intro = array();
        $standard = $this->_getDesignService()->getStandardSignkey($this->bo->getModel());
        $allSign = $this->_buildAllSign();
        list($threeSign, $twoSign, $oneSign) = $this->_buildModuleSign();
        foreach ($oneSign as $k => $sign) {
            if ($sign == $standard['sTitle']) {
                unset($oneSign[$k]);
            }
            if ($sign == $standard['sIntro'] && $standard['sIntro']) {
                $intro = array('name' => $allSign[$sign], 'key' => $standard['sIntro'], 'data' => '');
                unset($oneSign[$k]);
            }
        }
        $this->setOutput($intro, 'intro');
        $this->setOutput($standard['sTitle'], 'sTitle');
        $this->setOutput($threeSign, 'threeSign');
        $this->setOutput($twoSign, 'twoSign');
        $this->setOutput($oneSign, 'oneSign');
        $this->setOutput($allSign, 'allSign');
        $this->setOutput($this->bo->getLimit(), 'limit');
    }

    public function doaddAction()
    {
        $orderid = (int) $this->getInput('vieworder', 'post');
        $start = $this->getInput('start_time', 'post');
        $end = $this->getInput('end_time', 'post');
        $data = $this->getInput('data', 'post');
        $images = $this->getInput('images', 'post');
        $bold = $this->getInput('bold', 'post');
        $italic = $this->getInput('italic', 'post');
        $underline = $this->getInput('underline', 'post');
        $color = $this->getInput('color', 'post');
        $standard = $this->_getDesignService()->getStandardSignkey($this->bo->getModel());
        if (!$data[$standard['sTitle']]) {
            $this->showError('operate.fail');
        }
        foreach ((array) $images as $k => $v) {
            if ($_FILES[$k]['name'] && $image = $this->_uploadFile($k, $this->bo->moduleid)) {
                $data[$k] = $image;
            } else {
                $data[$k] = '';
            }
        }

        $time = Pw::getTime();
        $startTime = $start ? Pw::str2time($start) : $time;
        $endTime = $end ? Pw::str2time($end) : $end;
        if ($end && $endTime < $time) {
            $this->showError('DESIGN:endtimd.error');
        }
        $ds = $this->_getPushDs();
        if ($orderid) {
            $ds->updateAutoByModuleAndOrder($this->bo->moduleid, $orderid);
            $this->_getDataDs()->updateFixedToAuto($this->bo->moduleid, $orderid);
        }
        Wind::import('SRV:design.dm.PwDesignPushDm');
        $pushDm = new PwDesignPushDm();
        $pushDm->setAuthorUid($this->loginUser->uid)
            ->setCreatedUserid($this->loginUser->uid)
            ->setExtend($data)
            ->setFormModel('design')
            ->setModuleId($this->bo->moduleid)
            ->setOrderid($orderid)
            ->setStandard($standard)
            ->setStyle($bold, $underline, $italic, $color)
            ->setCreatedTime($time)
            ->setStartTime($startTime)
            ->setEndTime($endTime);
        $pushid = $ds->addPush($pushDm);
        if ($pushid instanceof PwError) {
            $this->showError($resource->getError());
        }
        $pushDm = new PwDesignPushDm($pushid);
        $pushDm->setFromid($pushid)
                ->setModuleId($this->bo->moduleid);
        $ds->updatePush($pushDm);
        $pushService = $this->_getPushService();
        $pushService->pushToData($pushid);
        $this->showMessage('operate.success');
    }

    public function editAction()
    {
        $dataid = (int) $this->getInput('dataid', 'get');
        $data = $this->_getDataDs()->getData($dataid);
        if (!$data) {
            $this->showError('fail');
        }
        list($data['bold'], $data['underline'], $data['italic'], $data['color']) = explode('|', $data['style']);
        $data['extend_info'] = unserialize($data['extend_info']);
        $standard = $this->_getDesignService()->getStandardSignkey($this->bo->getModel());
        $allSign = $this->_buildAllSign();
        list($threeSign, $twoSign, $oneSign) = $this->_buildModuleSign();
        foreach ($oneSign as $k => $sign) {
            if ($sign == $standard['sTitle']) {
                unset($oneSign[$k]);
            }
            if ($sign == $standard['sIntro'] && $standard['sIntro']) {
                $intro = array('name' => $allSign[$sign], 'key' => $standard['sIntro'], 'data' => $data[$sign]);
                unset($oneSign[$k]);
            }
        }
        $this->setOutput($intro, 'intro');
        $this->setOutput($this->bo->getLimit(), 'limit');
        $this->setOutput($standard['sTitle'], 'sTitle');
        $this->setOutput($data, 'data');
        $this->setOutput($threeSign, 'threeSign');
        $this->setOutput($twoSign, 'twoSign');
        $this->setOutput($oneSign, 'oneSign');
        $this->setOutput($allSign, 'allSign');
    }

    public function doeditAction()
    {
        $dataid = (int) $this->getInput('dataid', 'post');
        $info = $this->_getDataDs()->getData($dataid);
        if (!$info) {
            $this->showError('operate.fail');
        }
        $orderid = (int) $info['vieworder'];
        $start = $this->getInput('start_time', 'post');
        $end = $this->getInput('end_time', 'post');
        $data = $this->getInput('data', 'post');
        $images = $this->getInput('images', 'post');
        $bold = $this->getInput('bold', 'post');
        $italic = $this->getInput('italic', 'post');
        $underline = $this->getInput('underline', 'post');
        $color = $this->getInput('color', 'post');
        $standard = $this->_getDesignService()->getStandardSignkey($this->bo->getModel());
        if (!$data[$standard['sTitle']]) {
            $this->showError('operate.fail');
        }
        $imageSrv = Wekit::load('design.srv.PwDesignImage');
        foreach ((array) $images as $k => $v) {
            if ($_FILES[$k]['name'] && $image = $this->_uploadFile($k, $this->bo->moduleid)) {
                $data[$k] = $image;
                $extend = unserialize($info['extend_info']);
                $delImages = $extend['standard_image'];
                $imageSrv->clearFiles($this->bo->moduleid, explode('|||', $delImages));
            } else {
                $data[$k] = $v;
            }
        }
        $time = Pw::getTime();
        $startTime = $start ? Pw::str2time($start) : $time;
        $endTime = $end ? Pw::str2time($end) : $end;
        if ($end && $endTime < $time) {
            $this->showError('DESIGN:endtimd.error');
        }
        Wind::import('SRV:design.dm.PwDesignDataDm');
        $dm = new PwDesignDataDm($dataid);
        $dm->setStyle($bold, $underline, $italic, $color)
            ->setExtend($data)
            ->setStarttime($startTime)
            ->setEndtime($endTime);
        //推送的数据，不打修改标识
        if ($info['from_type'] == PwDesignData::FROM_AUTO) {
            $dm->setEdited(1);
        }
        if ($startTime > $time) {
            $dm->setReservation(1);
        }
        //if ($info['from_type'] == PwDesignData::FROM_AUTO) $dm->setDatatype(PwDesignData::ISEDIT);
        $this->_getDataDs()->updateData($dm);
        if ($info['from_type'] == PwDesignData::FROM_PUSH) {
            Wind::import('SRV:design.dm.PwDesignPushDm');
            $pushDm = new PwDesignPushDm($info['from_id']);
            $pushDm->setStyle($bold, $underline, $italic, $color)
                ->setExtend($data)
                ->setStartTime($startTime)
                ->setEndTime($endTime)
                ->setModuleId($info['module_id']);
            $this->_getPushDs()->updatePush($pushDm);
        }
        $this->showMessage('operate.success');
    }

    public function doshieldAction()
    {
        $dataid = (int) $this->getInput('dataid', 'get');
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
                $this->_getShieldDs()->addShield($data['from_app'], $data['from_id'], $data['module_id'], $data['title'], $data['url']);
                break;
            default:
                $this->showError('operate.fail');
                break;
        }
        $extend = unserialize($data['extend_info']);
        $delImages = $extend['standard_image'];
        Wekit::load('design.srv.PwDesignImage')->clearFiles($this->bo->moduleid, explode('|||', $delImages));
        if (!$data['is_reservation']) {
            Wind::import('SRV:design.srv.data.PwShieldData');
            $srv = new PwShieldData($data['module_id']);
            $srv->addShieldData();
        }
        $this->showMessage('operate.success');
    }

    public function docacheAction()
    {
        Wind::import('SRV:design.srv.data.PwAutoData');
        $srv = new PwAutoData($this->bo->moduleid);
        $srv->addAutoData();
        $this->showMessage('operate.success');
    }


    public function pushAction()
    {
        $page = (int) $this->getInput('page', 'get');
        $perpage = 10;
        $uids = array();
        $page = $page > 1 ? $page : 1;
        $pushDs = $this->_getPushDs();
        list($start, $perpage) = Pw::page2limit($page, $perpage);

        $vo = Wekit::load('design.srv.vo.PwDesignPushSo');
        $vo->setModuleid($this->bo->moduleid);
        $vo->setStatus(PwDesignPush::NEEDCHECK);
        $vo->orderbyPushid(false);
        $list = $pushDs->searchPush($vo, $perpage, $start);
        $count = $pushDs->countPush($vo);
        foreach ($list as $k => $v) {
            $uids[] = $v['created_userid'];
            $_tmp = unserialize($v['push_extend']);
            $standard = unserialize($v['push_standard']);
            $list[$k]['title'] = $_tmp[$standard['sTitle']];
            $list[$k]['url'] = $_tmp[$standard['sUrl']];
            $list[$k]['intro'] = $_tmp[$standard['sIntro']];
        }
        array_unique($uids);
        $users = Wekit::load('user.PwUser')->fetchUserByUid($uids);
        $this->setOutput($list, 'list');
        $this->setOutput($users, 'users');
        $this->setOutput(array('moduleid' => $this->bo->moduleid), 'args');
        $this->setOutput($count, 'count');
        $this->setOutput($page, 'page');
        $this->setOutput($perpage, 'perpage');
        $this->setOutput(ceil($count / $perpage), 'totalpage');
    }


    public function dopushAction()
    {
        $pushid = (int) $this->getInput('pushid', 'get');
        $pushDs = $this->_getPushDs();
        $pushDs->updateStatus($pushid, PwDesignPush::ISSHOW);
        Wind::import('SRV:design.srv.data.PwAutoData');
        $srv = new PwAutoData($this->bo->moduleid);
        $srv->addAutoData();
        $this->showMessage('operate.success');
    }

    public function delpushAction()
    {
        $pushid = (int) $this->getInput('pushid', 'get');
        $push = $this->_getPushDs()->getPush($pushid);
        if (!$push) {
            $this->showError('operate.fail');
        }
        if ($this->_getPushDs()->deletePush($pushid)) {
            $extend = unserialize($push['extend_info']);
            $delImages = $extend['standard_image'];
            Wekit::load('design.srv.PwDesignImage')->clearFiles($this->bo->moduleid, explode('|||', $delImages));
            $this->showMessage('operate.success');
        }
        $this->showError('operate.fail');
    }

    public function batchEditDataAction()
    {
        $dataid = $this->getInput('dataid', 'post');
        $order_tmp = $vieworder = $this->getInput('vieworder', 'post');
        $vieworder_tmp = $this->getInput('vieworder_tmp', 'post');
        $vieworder_reserv = $this->getInput('vieworder_reserv', 'post');
        $isfixed = $this->getInput('isfixed', 'post');
        Wind::import('SRV:design.dm.PwDesignDataDm');
        Wind::import('SRV:design.dm.PwDesignPushDm');
        $ds = $this->_getDataDs();

        //转换排序数字
        asort($vieworder);
        $i = 1;
        foreach ($vieworder as &$order) {
            $order = $i;
            $i++;
        }

        foreach ($dataid as $id) {
            $data = $ds->getData($id);
            if ($data['is_reservation']) {
                continue;
            }
            $dm = new PwDesignDataDm($id);
            $orderid = (int) $vieworder[$id];
            if ($isfixed[$id]) {
                $dm->setDatatype(PwDesignData::ISFIXED);
                if ($data['from_type'] == PwDesignData::FROM_PUSH) {
                    $this->_getPushDs()->updateAutoByModuleAndOrder($data['module_id'], $orderid);
                    $pushDm = new PwDesignPushDm($data['from_id']);
                    $pushDm->setOrderid($orderid);
                    $this->_getPushDs()->updatePush($pushDm);
                }
            } else {
                $dm->setDatatype(PwDesignData::AUTO);
                if ($data['from_type'] == PwDesignData::FROM_PUSH) {
                    $pushDm = new PwDesignPushDm($data['from_id']);
                    $pushDm->setOrderid(0);
                    $this->_getPushDs()->updatePush($pushDm);
                }
            }

            $dm->setVieworder($orderid);

            //产品要求，没显性改过排序的不作编辑处理......
            if ($order_tmp[$id] != $vieworder_tmp[$id]) {
                $dm->setEdited(1);
            }
            $ds->updateData($dm);
        }

        //预订
        foreach ($dataid as $id) {
            $data = $ds->getData($id);
            if (!$data['is_reservation']) {
                continue;
            }
            $dm = new PwDesignDataDm($id);
            $orderid = (int) $vieworder_reserv[$id];
            if ($isfixed[$id]) {
                $dm->setDatatype(PwDesignData::ISFIXED);
                $dm->setVieworder($orderid);
                if ($data['from_type'] == PwDesignData::FROM_PUSH) {
                    $this->_getPushDs()->updateAutoByModuleAndOrder($data['module_id'], $orderid);
                    $ds->updateFixedToAuto($data['module_id'], $orderid);
                    $pushDm = new PwDesignPushDm($data['from_id']);
                    $pushDm->setOrderid($orderid);
                    $this->_getPushDs()->updatePush($pushDm);
                }
            } else {
                $dm->setDatatype(PwDesignData::AUTO);
                if ($data['from_type'] == PwDesignData::FROM_PUSH) {
                    $pushDm = new PwDesignPushDm($data['from_id']);
                    $pushDm->setOrderid(0);
                    $this->_getPushDs()->updatePush($pushDm);
                }
            }
            $ds->updateData($dm);
        }
        $this->showMessage('operate.success');
    }

    public function batchCheckPushAction()
    {
        $pushid = $this->getInput('pushid', 'post');
        if (!$pushid) {
            $this->showError('operate.fail');
        }
        $ds = $this->_getPushDs();
        foreach ($pushid as $id) {
            $ds->updateStatus($id, PwDesignPush::ISSHOW);
        }
        Wind::import('SRV:design.srv.data.PwAutoData');
        $srv = new PwAutoData($this->bo->moduleid);
        $srv->addAutoData();
        $this->showMessage('operate.success');
    }

    public function batchDelPushAction()
    {
        $pushid = $this->getInput('pushid', 'post');
        if ($this->_getPushDs()->batchDelete($pushid)) {
            $this->showMessage('operate.success');
        }
        $this->showError('operate.fail');
    }

    private function _uploadFile($key, $moduleid = 0)
    {
        Wind::import('SRV:upload.action.PwDesignDataUpload');
        Wind::import('LIB:upload.PwUpload');
        $bhv = new PwDesignDataUpload($key, $moduleid);
        $upload = new PwUpload($bhv);
        if (($result = $upload->check()) === true) {
            $result = $upload->execute();
        }
        if ($result !== true) {
            $this->showError($result->getError());
        }
        $image = $bhv->getAttachInfo();

        return $image['filename'] ? Pw::getPath($image['path'].$image['filename']) : '';
    }

    /**
     * 从全部可用模块标签中转换key=>value标签
     * Enter description here ...
     */
    private function _buildAllSign()
    {
        $signKey = $this->bo->getSignKey();
        $_key = array();
        foreach ($signKey as $v) {
            list($_sign, $_name, $_k) = $v;
            $_name = str_replace('｜', '|', $_name);
            $_name = explode('|', $_name);
            $_name = array_shift($_name);
            if (preg_match('/\{(\w+)\|(.+)}/U', $_sign, $matches)) {
                $_key[$matches[1]] = $_name;
                continue;
            }
            if (preg_match('/\{(\w+)}/isU', $_sign, $matches)) {
                $_key[$matches[1]] = $_name;
                continue;
            }
        }

        return $_key;
    }

    /**
     * 从模块模板中转换key标签
     * Enter description here ...
     */
    private function _buildModuleSign()
    {
        $tpl = $this->bo->getTemplate();
        $three = array();
        $two = array();
        $one = array();
        if (preg_match_all('/\{(\w+)\|(\d+)\|(\d+)}/U', $tpl, $matche)) {
            foreach ($matche[1] as $k => $v) {
                $three[] = array('sign' => $v, 'width' => $matche[2][$k], 'height' => $matche[3][$k]);
            }
        }
        if (preg_match_all('/\{(\w+)\|img}/U', $tpl, $matche)) {
            foreach ($matche[1] as $v) {
                $two[] = $v;
            }
        }

        if (preg_match_all('/\{(\w+)\|(\d+)}/U', $tpl, $matche)) {
            foreach ($matche[1] as $v) {
                $one[] = $v;
            }
        }

        if (preg_match_all('/\{(\w+)}/isU', $tpl, $matche)) {
            foreach ($matche[1] as $v) {
                $one[] = $v;
            }
        }

        return array(array_unique($three), array_unique($two), array_unique($one));
    }

    private function _getPushService()
    {
        return Wekit::load('design.srv.PwPushService');
    }

    private function _getDesignService()
    {
        return Wekit::load('design.srv.PwDesignService');
    }

    private function _getPushDs()
    {
        return Wekit::load('design.PwDesignPush');
    }

    private function _getDataDs()
    {
        return Wekit::load('design.PwDesignData');
    }

    private function _getShieldDs()
    {
        return Wekit::load('design.PwDesignShield');
    }
}
