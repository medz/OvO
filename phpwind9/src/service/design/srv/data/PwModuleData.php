<?php


/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>.
 *
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwModuleData.php 24340 2013-01-29 03:08:31Z gao.wanggao $
 */
class PwModuleData
{
    protected $bo;
    protected $time;

    protected $sourData = [];
    protected $multiSign = [];
    protected $designData = [];

    protected $pushids = [];
    protected $autoids = [];

    private $_substrSign = []; //需要截取的标签

    public function __construct($moduleid)
    {
        $this->bo = new PwDesignModuleBo($moduleid);
        $this->time = Pw::getTime();
    }

    /**
     * 对排序进行修正
     * Enter description here ...
     */
    public function reviseOrder()
    {
        $fixed = [];
        $data = Wekit::load('design.PwDesignData')->getDataByModuleid($this->bo->moduleid);
        foreach ($data as $k=>$v) {
            if ($v['data_type'] == 2) {
                $fixed[] = $v['vieworder'];
            }
        }
        if (! $fixed) {
            return true;
        }
        $i = 1;
        $ds = Wekit::load('design.PwDesignData');
        foreach ($data as $k=>$v) {
            if ($v['data_type'] == 2) {
                continue;
            }
            while (in_array($i, $fixed)) {
                $i++;
            }
            $ds->updateOrder($v['data_id'], $i);
            $i++;
        }

        return true;
    }

    /**
     * 根据指定ID获取数据
     * 用于推送数据获取.
     *
     * @param int|array $fromid
     */
    public function buildDataByIds($fromid)
    {
        Wekit::load('design.PwDesignData');
        $model = $this->bo->getModel();
        if (! is_array($fromid)) {
            $fromid = [$fromid];
        }
        $cls = sprintf('PwDesign%sDataService', ucwords($model));
        $service = Wekit::load('design.srv.model.'.$model.'.'.$cls);
        $service->setModuleBo($this->bo);
        $data = $service->buildDataByIds($fromid);
        if (! $data) {
            return [];
        }
        foreach ($data as  $k=>$v) {
            $v['from_type'] = 'auto'; //新推送的数据，指写为自运获取类型，用于数据处理
            $v['data_type'] = PwDesignData::AUTO;
            $data[$k]['extend'] = $this->getExtend($v);
        }

        return  $data;
    }

    protected function setDesignData()
    {
        $usedDataid = $delDataIds = $_data = [];
        $delImages = '';
        $delImgIds = [];
        $ds = Wekit::load('design.PwDesignData');
        $data = $ds->getDataByModuleid($this->bo->moduleid);
        $limit = $this->getLimit();
        foreach ($data as $v) {
            if ($v['is_reservation']) {
                $delDataIds[] = $v['data_id']; //不删附件
                continue;
            }
            if ($v['data_type'] == PwDesignData::AUTO && ! $v['is_edited']) {
                $delDataIds[] = $v['data_id'];
                $_data[] = [];
                if ($v['from_type'] != PwDesignData::FROM_PUSH) {
                    $extend = unserialize($v['extend_info']);
                    $delImages .= $extend['standard_image'];
                    isset($extend['__asyn']) && $delImgIds[] = $extend['__asyn'];
                }
                continue;
            }
            if ($v['end_time'] > 0 && $v['end_time'] < $this->time) {
                $delDataIds[] = $v['data_id'];
                $_data[] = [];
                if ($v['from_type'] != PwDesignData::FROM_PUSH) {
                    $extend = unserialize($v['extend_info']);
                    $delImages .= $extend['standard_image'];
                    isset($extend['__asyn']) && $delImgIds[] = $extend['__asyn'];
                }
                continue;
            }

            if ($v['from_type'] == PwDesignData::FROM_PUSH) {
                $this->pushids[] = $v['from_id'];
            } else {
                $this->autoids[] = $v['from_id'];
            }
            $_data[] = $v;
        }

        //格式化门户数据系列，无数据的补空；
        for ($i = 0; $i < $limit; $i++) {
            $this->designData[] = isset($_data[$i]) ? $_data[$i] : [];
            $_data[$i]['data_id'] && $usedDataid[] = $_data[$i]['data_id'];
        }

        foreach ($data as $v) {
            if ($v['data_id'] && ! in_array($v['data_id'], $usedDataid)) {
                $delDataIds[] = $v['data_id'];
            }
        }
        $ds->batchDelete($delDataIds);
        if ($delImages) {
            Wekit::load('design.srv.PwDesignImage')->clearFiles($this->bo->moduleid, explode('|||', $delImages));
        }
        if ($delImgIds) {
            Wekit::load('design.PwDesignAsynImage')->batchDelete($delImgIds);
        }
    }

    protected function getLimit()
    {
        //if ($this->_limit) return $this->_limit;
        return $this->bo->getLimit();
    }

    /**
     * 过滤不需要的data
     * Enter description here ...
     *
     * @param array $data
     */
    protected function getExtend($data, $order = null)
    {
        $_data = [];
        $params = $this->getComponentValue($this->bo->getTemplate(), implode('', $this->bo->getStandardSign()), $order);
        if ($data['from_type'] == 'auto' && $data['data_type'] == PwDesignData::AUTO && ! $data['is_edited']) {
            //if ($this->bo->getLimit() > 10) {
                $data = $this->asynCutImg($data);
            //} else {
            //	$data = $this->cutImg($data);
            //}
        }
        foreach ($params as $param) {
            if (isset($data[$param])) {
                //在输出阶段截取
                //$_data[$param] = isset($this->_substrSign[$param]) ? $this->substr($data[$param], $this->_substrSign[$param]) : $data[$param];
                $_data[$param] = $data[$param];
            }
            $_data['standard_image'] = $data['standard_image'];
            isset($data['__asyn']) && $_data['__asyn'] = $data['__asyn'];
        }

        return $_data;
    }

    /**
     * 生成门户图片
     * Enter description here ...
     *
     * @param array $data
     */
    protected function cutImg($data)
    {
        if (! $this->multiSign['img']) {
            return $data;
        }
        $srv = Wekit::load('design.srv.PwDesignImage');
        foreach ((array) $this->multiSign['img'] as $k=>$v) {
            $data['standard_image'] = '';
            if (! $data[$k]) {
                continue;
            }
            list($thumbW, $thumbH) = $v;
            if ($thumbW < 1 && $thumbH < 1) {
                $data[$k] = Pw::getPath($data[$k]);
            } else {
                $srv->setInfo($this->bo->moduleid, $data[$k], $thumbW, $thumbH);
                $array = $srv->cut();
                list($dir, $filename, $url) = $array;
                if ($dir) {
                    list($dir, $filename, $url) = $array;
                    $data[$k] = $url.$dir.$filename;
                    $data['standard_image'] .= $filename.'|||';
                } else {
                    $data[$k] = Pw::getPath($data[$k]);
                }
            }
        }

        return $data;
    }

    /**
     * 图片异步缩略
     * Enter description here ...
     *
     * @param unknown_type $data
     */
    protected function asynCutImg($data)
    {
        if (! $this->multiSign['img']) {
            return $data;
        }
        $ds = Wekit::load('design.PwDesignAsynImage');

        foreach ((array) $this->multiSign['img'] as $k=>$v) {
            $data['standard_image'] = '';
            if (! $data[$k]) {
                continue;
            }
            list($thumbW, $thumbH) = $v;
            if ($thumbW < 1 && $thumbH < 1) {
                $data[$k] = Pw::getPath($data[$k]);
            } else {
                $dm = new PwDesignAsynImageDm();
                $dm->setHeight($thumbH)->setWidth($thumbW)->setPath($data[$k])->setModuleid($this->bo->moduleid)->setSign($k);
                $result = $ds->addImage($dm);
                if ($result instanceof PwError) {
                    $data[$k] = Pw::getPath($data[$k]);
                } else {
                    $data[$k] = WindUrlHelper::createUrl('design/image/run', ['id' => (int) $result], '', 'pw');
                    $data['__asyn'] = (int) $result;
                }
                $data['standard_image'] = '';
            }
        }

        return $data;
    }

    protected function getComponentValue($string, $standardSign, $order = null)
    {
        if (isset($order)) {
            if (preg_match('/\<if:(\d+)>(.+)<else:>(.+)<\/if>/isU', $string, $matche)) {
                if ($order == $matche[1] + 1) {
                    $string = $matche[2];
                }
            }

            if (preg_match('/\<if:odd>(.+)<else:>(.+)<\/if>/isU', $string, $matche)) {
                if (! is_int($order / 2)) {
                    $string = $matche[1];
                }
            }

            if (preg_match('/\<if:even>(.+)<else:>(.+)<\/if>/isU', $string, $matche)) {
                if (is_int($order / 2)) {
                    $string = $matche[1];
                }
            }
        }

        $string .= $standardSign;
        //对三元标签（图片）进行处理
        if (preg_match_all('/\{(\w+)\|(\d+)\|(\d+)}/U', $string, $matche)) {
            foreach ($matche[1] as $k=>$v) {
                //if ($matche[2][$k] || $matche[3][$k]) {
                $this->multiSign['img'][$v] = [$matche[2][$k], $matche[3][$k]];
                //}
                $string = str_replace($matche[0][$k], '{'.$v.'}', $string);
            }
        }

        //对二元标签进行处理
        if (preg_match_all('/\{(\w+)\|(\d+)}/U', $string, $matche)) {
            foreach ($matche[1] as $k=>$v) {
                $string = str_replace($matche[0][$k], '{'.$v.'}', $string);
            }
            if ($matche[2][0] && $matche[1][0]) {
                $this->_substrSign[$matche[1][0]] = $matche[2][0];
            }
        }

        //对二元标签进行处理
        if (preg_match_all('/\{(\w+)\|(\w+)}/U', $string, $matche)) {
            foreach ($matche[1] as $k=>$v) {
                $string = str_replace($matche[0][$k], '{'.$v.'}', $string);
            }
        }

        if (! preg_match_all('/\{(\w+)}/isU', $string, $matches)) {
            return [];
        }

        return array_unique($matches[1]);
    }

    /**
     * 格式化推送数据用于Data插入
     * Enter description here ...
     */
    protected function formatPushData($data)
    {
        $_data = [];
        $_data['standard_title'] = $data['push_id'];
        $_data['standard_fromid'] = $data['push_id'];
        $_data['standard_fromapp'] = $data['push_from_model'];
        $_data['standard'] = unserialize($data['push_standard']);
        $_data['standard_style'] = explode('|', $data['push_style']); //$bold,$underline,$italic,$color
        $_data['vieworder'] = $data['push_orderid'];
        $_data['start_time'] = $data['start_time'];
        $_data['end_time'] = $data['end_time'];
        $_data['from_type'] = 'push';
        $_data['data_type'] = 1;
        $extend = unserialize($data['push_extend']);
        $_data['standard_image'] = $extend['standard_image'];
        $_data = array_merge($_data, $extend);

        return $_data;
    }

    /**
     * 转换data数据用于更新
     * Enter description here ...
     */
    protected function formatDesginData($data)
    {
        $_data = [];
        $_data['standard_title'] = $data['from_id'];
        $_data['standard_fromid'] = $data['from_id'];
        $_data['standard_fromapp'] = $data['from_app'];
        $_data['standard'] = unserialize($data['standard']);
        $_data['standard_style'] = explode('|', $data['style']); //$bold,$underline,$italic,$color
        $_data['vieworder'] = $data['vieworder'];
        $_data['is_edited'] = $data['is_edited'];
        $_data['start_time'] = $data['start_time'];
        $_data['end_time'] = $data['end_time'];
        $_data['data_type'] = $data['data_type'];
        $extend = unserialize($data['extend_info']);
        $_data['standard_image'] = $extend['standard_image'];
        $_data = array_merge($_data, $extend);

        return $_data;
    }

    /**
     * 截取字符串
     * Enter description here ...
     *
     * @param string $string
     * @param int    $length
     */
    protected function substr($string, $length = 0)
    {
        if (! $length) {
            return $string;
        }
        if (! $string) {
            return '';
        }
        $string = Pw::stripWindCode($string);
        $string = preg_replace("/\r\n|\n|\r/", '', $string);
        $string = str_replace(' ', '', $string);

        return Pw::substrs($string, $length);
    }
}
