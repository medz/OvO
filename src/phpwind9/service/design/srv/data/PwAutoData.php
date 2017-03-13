<?php


/**
 * the last known user to change this file in the repository <$LastChangedBy:
 * gao.wanggao $>.
 *
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwAutoData.php 23340 2013-01-08 11:08:26Z gao.wanggao $
 */
class PwAutoData extends PwModuleData
{
    private $_newPushIds = [];
    private $_newAutoIds = [];
    private $_reservData = [];

    /**
     * 自动更新所有数据.
     */
    public function addAutoData()
    {
        $this->_getData();
        $this->setDesignData();
        $this->_addData();
    }

    private function _getData()
    {
        $limit = $this->getLimit();
        $this->_getPushData($limit);
        $limit = $limit - count($this->_newPushIds);
        if ($limit > 0) {
            $param = $this->bo->getVoParam();
            $param['limit'] = $limit;
            $param['start'] = 0;
            $this->_getAutoData($param);
        }
    }

    private function _addData()
    {
        $delDataIds = $newOrderIds = [];
        $delImages = '';
        $delImgIds = [];
        $ds = Wekit::load('design.PwDesignData');
        $pushDs = Wekit::load('design.PwDesignPush');
        $imageDs = Wekit::load('design.PwDesignAsynImage');

        list($start, $end, $refresh) = $this->bo->refreshTime($this->time);
        foreach ($this->designData as $k => $v) {
            $k++;
            if (!$v) {
                $newOrderIds[] = $k;
                continue;
            }
            if ($v['from_type'] == PwDesignData::FROM_PUSH) {
                if (isset($this->_newPushIds[$v['from_id']])) {
                    if ($v['data_type'] == PwDesignData::ISFIXED) {
                        unset($this->_newPushIds[$v['from_id']]);
                        continue;
                    }
                    $data = $this->formatDesginData($v);
                    $data['vieworder'] = $this->_newPushIds[$v['from_id']]['vieworder'];
                    $data['from_type'] = $this->_newPushIds[$v['from_id']]['from_type'];
                    if (!$this->_newPushIds[$v['from_id']]['vieworder'] && !$data['is_edited']) {
                        $data['data_type'] = PwDesignData::AUTO;
                    }
                    $this->_newPushIds[$v['from_id']] = $data;
                    $delDataIds[] = $v['data_id'];
                    // $delImages .= $data['standard_image'];
                    unset($v);
                }
            }

            if ($v['from_type'] == PwDesignData::FROM_AUTO) {
                if (isset($this->_newAutoIds[$v['from_id']])) {
                    if ($v['data_type'] == PwDesignData::ISFIXED) {
                        $ds->updateEndTime($v['data_id'], $refresh);
                        unset($this->_newAutoIds[$v['from_id']]);
                        continue;
                    }
                    $data = $this->formatDesginData($v);
                    $data['vieworder'] = $this->_newAutoIds[$v['from_id']]['vieworder'];
                    $data['from_type'] = $this->_newAutoIds[$v['from_id']]['from_type'];
                    $this->_newAutoIds[$v['from_id']] = $data;
                    $delDataIds[] = $v['data_id'];
                    $delImages .= $data['standard_image'];
                    isset($data['__asyn']) && $delImgIds[] = $data['__asyn'];
                    unset($v);
                }
            }

            if ($v) {
                if ($v['data_type'] == PwDesignData::ISFIXED) {
                    continue;
                }
                $delDataIds[] = $v['data_id'];
                if ($v['from_type'] != PwDesignData::FROM_PUSH) {
                    $extend = unserialize($v['extend_info']);
                    $delImages .= $extend['standard_image'];
                    isset($data['__asyn']) && $delImgIds[] = $data['__asyn'];
                }
            }

            $newOrderIds[] = $k;
        }
        $ds->batchDelete($delDataIds);
        if ($delImages) {
            Wekit::load('design.srv.PwDesignImage')->clearFiles($this->bo->moduleid,
            explode('|||', $delImages));
        }
        if ($delImgIds) {
            Wekit::load('design.PwDesignAsynImage')->batchDelete($delImgIds);
        }
        // 添加新显示数据
        $limit = count($delDataIds);
        $i = 1;
        foreach ($this->_newPushIds as $key => $newData) {
            if (!$newData['vieworder']) {
                continue;
            }
            if ($i > $limit) {
                break;
            }
            $_k = array_search($newData['vieworder'], $newOrderIds);
            if ($_k !== false) {
                unset($newOrderIds[$_k]);
            }
            $dm = new PwDesignDataDm();
            $dm->setDatatype(PwDesignData::AUTO)->setFromType(PwDesignData::FROM_AUTO)->setFromApp(
                $newData['standard_fromapp'])->setFromid($newData['standard_fromid'])->setModuleid($this->bo->moduleid)->setStandard(
                $newData['standard'])->setVieworder($newData['vieworder'])->setStarttime($this->time)->setEdited(
                $newData['is_edited'])->setEndtime($refresh);
            if ($newData['standard_style']) {
                list($bold, $underline, $italic, $color) = $newData['standard_style'];
                $dm->setStyle($bold, $underline, $italic, $color);
            }
            if ($newData['from_type'] == 'push') {
                $dm->setFromType(PwDesignData::FROM_PUSH)->setStarttime($newData['start_time'])->setEndtime(
                    $newData['end_time']);
            }
            if ($newData['data_type']) {
                $dm->setDatatype($newData['data_type']);
            }
            if ($newData['vieworder']) {
                $dm->setDatatype(PwDesignData::ISFIXED);
            }
            $extend = $this->getExtend($newData);
            $dm->setExtend($extend);
            $resource = $ds->addData($dm);
            if (isset($extend['__asyn'])) {
                $imageDs->updateDataId($extend['__asyn'], $resource);
            }
            unset($this->_newPushIds[$key]);
            $i++;
        }

        foreach ($newOrderIds as $order) {
            $isupdate = false;
            $newData = array_shift($this->_newPushIds);
            if (!$newData) {
                $newData = array_shift($this->_newAutoIds);
            }
            if (!$newData) {
                break;
            }
            $newData['vieworder'] && $order = $newData['vieworder'];
            $dm = new PwDesignDataDm();
            $dm->setDatatype(PwDesignData::AUTO)->setFromType(PwDesignData::FROM_AUTO)->setFromApp(
                $newData['standard_fromapp'])->setFromid($newData['standard_fromid'])->setModuleid($this->bo->moduleid)->setStandard(
                $newData['standard'])->setVieworder($order)->setStarttime($this->time)->setEndtime($refresh)->setEdited(
                $newData['is_edited']);
            if ($newData['standard_style']) {
                list($bold, $underline, $italic, $color) = $newData['standard_style'];
                $dm->setStyle($bold, $underline, $italic, $color);
            }
            if ($newData['from_type'] == 'push') {
                $dm->setFromType(PwDesignData::FROM_PUSH)->setStarttime($newData['start_time'])->setEndtime(
                    $newData['end_time']);
            }
            if ($newData['data_type']) {
                $dm->setDatatype($newData['data_type']);
            }
            if ($newData['vieworder']) {
                $dm->setDatatype(PwDesignData::ISFIXED);
            }
            $extend = $this->getExtend($newData, $order);
            $dm->setExtend($extend);
            $resource = $ds->addData($dm);
            if (isset($extend['__asyn'])) {
                $imageDs->updateDataId($extend['__asyn'], $resource);
            }
        }

        // 添加预定数据
        foreach ($this->_reservData as $newData) {
            $dm = new PwDesignDataDm();
            $dm->setDatatype(PwDesignData::AUTO)->setFromType(PwDesignData::FROM_PUSH)->setFromApp(
                $newData['standard_fromapp'])->setFromid($newData['standard_fromid'])->setModuleid($this->bo->moduleid)->setStandard(
                $newData['standard'])->setVieworder($newData['vieworder'])->setStarttime($newData['start_time'])->setEndtime(
                $newData['end_time'])->setReservation(1)->setEdited(0);
            if ($newData['standard_style']) {
                list($bold, $underline, $italic, $color) = $newData['standard_style'];
                $dm->setStyle($bold, $underline, $italic, $color);
            }

            if ($newData['vieworder']) {
                $dm->setDatatype(PwDesignData::ISFIXED);
            }

            $extend = $this->getExtend($newData);
            $dm->setExtend($extend);
            $resource = $ds->addData($dm);
            if (isset($extend['__asyn'])) {
                $imageDs->updateDataId($extend['__asyn'], $resource);
            }
        }
    }

    /**
     * 获取推送数据
     * Enter description here .
     * ..
     *
     * @param int $limit
     * @param int $start
     * @param int $times
     *                   循环次数
     */
    private function _getPushData($limit, $start = 0, $times = 0)
    {
        $ds = Wekit::load('design.PwDesignPush');
        $vo = Wekit::load('design.srv.vo.PwDesignPushSo');
        do {
            $vo->setModuleid($this->bo->moduleid);
            $vo->setGtEndTime($this->time);
            $vo->setStatus(PwDesignPush::ISSHOW);
            $vo->orderbyPushid(false);
            $data = $ds->searchPush($vo, $limit, $start);
            $i = 0;
            $count = count($data);
            if ($count < 1) {
                break;
            }
            foreach ($data as $k => $v) {
                if ($v['start_time'] > $this->time) {
                    $i++;
                    $this->_reservData[] = $this->formatPushData($v);
                    continue;
                }
                $data = $this->formatPushData($v);
                $this->_newPushIds[$data['standard_fromid']] = $data;
            }
            if ($count < $limit) {
                break;
            }
            $start += $limit;
            $limit = $i;
            $times++;
        } while ($i && $times < 20);
    }

    private function _getAutoData($param)
    {
        $model = $this->bo->getModel();
        if (!$model) {
            return false;
        }
        $cls = sprintf('PwDesign%sDataService', ucwords($model));
        $service = Wekit::load('design.srv.model.'.$model.'.'.$cls);
        $service->setModuleBo($this->bo);
        $PwDesignShield = Wekit::load('design.PwDesignShield');

        $limit = $param['limit'];
        $start = $param['start'];
        $times = 20;
        do {
            $data = $service->buildAutoData($param, $param['order'], $limit, $start);
            $count = count($data);
            if ($count < 1) {
                break;
            }
            $shield_num = 0;

            $fromids = Pw::collectByKey($data, 'standard_fromid');
            $shields = $PwDesignShield->fetchByFromidsAndApp($fromids, $model);
            $shieldids = $shields ? Pw::collectByKey($shields, 'from_id') : [];
            foreach ($data as $k => $v) {
                if (in_array($v['standard_fromid'], $shieldids) || $v['standard_title'] === '') {
                    $shield_num++;
                    continue;
                } else {
                    $v['from_type'] = 'auto';
                    $v['data_type'] = 1;
                    $this->_newAutoIds[$v['standard_fromid']] = $v;
                }
            }
            $start += $limit;
            if ($count < $limit) {
                break;
            }
            $limit = $shield_num;
        } while (--$times > 0 && $limit > 0);

        return true;
    }
}
