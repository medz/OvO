<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>.
 *
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwPushService.php 17555 2012-09-06 09:43:13Z gao.wanggao $
 */
class PwPushService
{
    /**
     * 获取某类型的一条推送数据.
     *
     * @return array 返回的数据结构与门户数据完全相同
     */
    public function getDataByFromid($model, $formid)
    {
        $cls = sprintf('PwDesign%sPushService', ucwords($model));
        if (!Wind::import('SRV:design.srv.model.'.$model.'.'.$cls)) {
            return array();
        }
        $srv = new $cls();
        if (!method_exists($srv, 'getFromData')) {
            return array();
        }

        return $srv->getFromData($formid);
    }

    /**
     * 添加推送数据到推送表.
     */
    public function addPushData(PwDesignPushDm $dm)
    {
        $data = $dm->getData();
         
        $srv = new PwModuleData($data['module_id']);
        $_data = $srv->buildDataByIds($data['push_from_id']);
        $_data = array_shift($_data);
        if (!$_data) {
            return new PwError('DESIGN:push.data.error');
        }
        $dm->setFormModel($_data['standard_fromapp'])
            ->setStandard($_data['standard'])
            ->setExtend($_data['extend']);

        return Wekit::load('design.PwDesignPush')->addPush($dm);
    }

    /**
     * 把一条推送数据写入展示数据.
     */
    public function pushToData($pushid)
    {
        /* 
         
        $pushDs = Wekit::load('design.PwDesignPush');
        $ds = Wekit::load('design.PwDesignData');
        $push = $pushDs->getPush($pushid);
        if (!$push) return false;
        $time = Pw::getTime();
        if ($push['end_time'] > 0 &&  $push['end_time'] < $time) return false;
        if (!$push['start_time']) $push['start_time'] = $time;
        $bo = new PwDesignModuleBo($push['module_id']);
        //list($start, $end, $refresh) = $bo->refreshTime($push['start_time']);
        $view = $bo->getView();
        $dm = new PwDesignDataDm();
        list($bold, $underline, $italic, $color) = explode('|', $push['push_style']);

        $extend = unserialize($push['push_extend']);
        $dm->setDatatype(PwDesignData::AUTO)
            ->setFromType(PwDesignData::FROM_PUSH)
            ->setFromApp($push['push_from_model'])
            ->setModuleid($push['module_id'])
            ->setFromid($pushid)
            ->setStandard(unserialize($push['push_standard']))
            ->setStyle($bold, $underline, $italic, $color)
            ->setExtend(unserialize($push['push_extend']))
            ->setStarttime($push['start_time'])
            ->setEndtime($push['end_time'])
            ->setVieworder($push['push_orderid']);
        if($push['push_orderid'] > 0) $dm->setDatatype(PwDesignData::ISFIXED);
        if($push['start_time'] > $time) $dm->setReservation(1);
        $resource = $ds->addData($dm);
        if ($resource instanceof PwError) return false;

        $this->afterPush($pushid);
        $countVo = Wekit::load('design.srv.vo.PwDesignDataSo');
        $countVo->setModuleid($push['module_id']);
        $countVo->setReservation(0);
        $count = $ds->countData($countVo);
        $limit = $view['limit'] ? $view['limit'] : 10;

        if ($push['start_time'] <= $time && $count >= $limit) {
            if ($push['push_orderid'] > 0) {
                //指定排序，删除原指定序号的
                $vo = Wekit::load('design.srv.vo.PwDesignDataSo');
                $vo->setModuleid($push['module_id']);
                $vo->setReservation(0);
                $vo->setVierOrder($push['push_orderid']);
                $vo->orderbyDataid(true);
                $data = array_shift($ds->searchData($vo, 1));
                $ds->deleteData($data['data_id']);
            } else {
                //先判断自动的，如果没有自动的数据，删除固定的
                $maxDataid = $ds->getMaxOrderDataId($push['module_id'], PwDesignData::AUTO);
                if (!$maxDataid) $maxDataid = $ds->getMaxOrderDataId($push['module_id'], PwDesignData::ISFIXED);
                $ds->deleteData($maxDataid);
            }
        }


         
        $srv = new PwModuleData($push['module_id']);
        $srv->reviseOrder();
        */
        $pushDs = Wekit::load('design.PwDesignPush');
        $push = $pushDs->getPush($pushid);
         
        $srv = new PwAutoData($push['module_id']);
        $srv->addAutoData();

        return true;
    }

    public function afterPush($pushid)
    {
        $pushDs = Wekit::load('design.PwDesignPush');
        $push = $pushDs->getPush($pushid);
        $cls = sprintf('PwDesign%sPushService', ucwords($push['push_from_model']));
        if (Wind::import('SRV:design.srv.model.'.$push['push_from_model'].'.'.$cls)) {
            if (!class_exists($cls, false)) {
                return false;
            }
            $srv = new $cls();
            if (method_exists($srv, 'afterPush')) {
                $srv->afterPush($pushid);

                return true;
            }
        }

        return false;
    }

    private function _getDesignService()
    {
        return Wekit::load('design.srv.PwDesignService');
    }
}
