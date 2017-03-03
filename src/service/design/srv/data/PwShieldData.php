<?php

 
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>.
 *
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwShieldData.php 23167 2013-01-06 13:29:44Z gao.wanggao $
 */
class PwShieldData extends PwModuleData
{
    public function addShieldData()
    {
        $this->_designData();
        $this->_getData();
        $this->_addData();
        $this->reviseOrder();
    }

    private function _designData()
    {
        $delDataIds = $_data = array();
        $ds = Wekit::load('design.PwDesignData');
        $data = $ds->getDataByModuleid($this->bo->moduleid);
        $limit = $this->getLimit();
        foreach ($data as $v) {
            if ($v['from_type'] == PwDesignData::FROM_PUSH) {
                $this->pushids[] = $v['from_id'];
            } else {
                $this->autoids[] = $v['from_id'];
            }
            $_data[] = $v;
        }
        $ds->batchDelete($delDataIds);
        //格式化门户数据系列，无数据的补空；
        for ($i = 0; $i < $limit; $i++) {
            $this->designData[] = isset($_data[$i]) ? $_data[$i] : array();
        }
    }

    private function _getData()
    {
        $limit = 1;
        $this->_getPushData($limit);
        $limit = $limit - count($this->sourData);
        if ($limit < 1) {
            return;
        }
        $param = $this->bo->getVoParam();
        $param['limit'] = $limit;
        $param['start'] = 0;
        $this->_getAutoData($param);
    }

    private function _addData()
    {
        $usedDataids = array();
        $ds = Wekit::load('design.PwDesignData');
         
        $time = Pw::getTime();
        list($start, $end, $refresh) = $this->bo->refreshTime($time);
        $orderid = $ds->getMaxOrder($this->bo->moduleid);

        foreach ($this->sourData as $k=>$v) {
            if (!isset($v['standard_title']) && $v['from_type'] == 'auto') {
                continue;
            }
            $orderid++;
            $dm = new PwDesignDataDm();
            $dm->setDatatype(PwDesignData::AUTO)
                ->setFromType(PwDesignData::FROM_AUTO)
                ->setFromApp($v['standard_fromapp'])
                ->setFromid($v['standard_fromid'])
                ->setModuleid($this->bo->moduleid)
                ->setStandard($v['standard'])
                ->setVieworder($orderid)
                ->setStarttime($time)
                ->setEndtime($refresh);
            if ($v['from_type'] == 'push') {
                $dm->setFromType(PwDesignData::FROM_PUSH)
                    ->setVieworder($v['vieworder']);
            }
            if (isset($v['bold'])) {
                $dm->setStyle($v['bold'], $v['underline'], $v['italic'], $v['color']);
            }
            $extend = $this->getExtend($v);
            $dm->setExtend($extend);
            $resource = $ds->addData($dm);
            if (isset($extend['__asyn'])) {
                $imageDs->updateDataId($extend['__asyn'], $resource);
            }
        }
    }

    /**
     * 递归获取推送数据
     * Enter description here ...
     *
     * @param int $limit
     * @param int $start
     * @param int $times 循环次数
     */
    private function _getPushData($limit, $start = 0, $times = 0)
    {
        $time = Pw::getTime();
        $ds = Wekit::load('design.PwDesignPush');
        $vo = Wekit::load('design.srv.vo.PwDesignPushSo');
        do {
            $vo->setModuleid($this->bo->moduleid);
            $vo->setGtEndTime($time);
            $vo->orderbyPushid(false);
            $data = $ds->searchPush($vo, $limit, $start);
            $i = 0;
            $count = count($data);
            if ($count < 1) {
                return true;
            }
            foreach ($data as $k=>$v) {
                if (in_array($v['push_id'], $this->pushids)) {
                    $i++;
                    continue;
                }
                if ($v['start_time'] > $time) {
                    $i++;
                    continue;
                }
                $this->sourData[] = $this->formatPushData($v);
            }
            if ($count < $limit) {
                return true;
            }
            $start += $limit;
            $times++;
        } while ($i && $times < 20);
    }

    private function _getAutoData($param, $times = 0)
    {
        $limit = $param['limit'];
        $model = $this->bo->getModel();
        if (!$model) {
            return false;
        }
        $cls = sprintf('PwDesign%sDataService', ucwords($model));
        $service = Wekit::load('design.srv.model.'.$model.'.'.$cls);
        $service->setModuleBo($this->bo);
        do {
            $shieldids = $fromids = array();
            $data = $service->buildAutoData($param, $param['order'], $limit, $param['start']);
            $count = count($data);
            if ($count < 1) {
                break;
            }
            $i = 0;
            foreach ($data as $k=>$v) {
                $fromids[] = $v['standard_fromid'];
            }

            $shields = Wekit::load('design.PwDesignShield')->fetchByFromidsAndApp($fromids, $model);
            if ($shields) {
                foreach ($shields as $v) {
                    $shieldids[] = $v['from_id'];
                }
            }

            foreach ($data as $k=>$v) {
                if (!isset($v['standard_title']) || in_array($v['standard_fromid'], $shieldids) || in_array($v['standard_fromid'], $this->autoids)) {
                    unset($data[$k]);
                    $i++;
                    continue;
                } else {
                    $v['from_type'] = 'auto';
                    $this->sourData[] = $v;
                }
            }
            if ($count < $limit) {
                break;
            }
            $param['start'] += $limit;
            $param['limit'] = $i;
            $times++;
        } while ($i && $times < 20);
    }
}
