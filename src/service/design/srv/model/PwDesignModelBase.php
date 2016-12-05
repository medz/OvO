<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>.
 *
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwDesignModelBase.php 25400 2013-03-14 08:35:54Z gao.wanggao $
 */
abstract class PwDesignModelBase
{
    protected $_data = array();
    protected $_moduleBo;
    private $_viewSet = array();

    /**
     * 根据设置获取数据.
     *
     * @param array $field  可能的查询参数
     * @param int   $order
     * @param int   $limit
     * @param int   $offset
     *
     * @return array
     */
    abstract protected function getData($param, $order, $limit, $offset);

    /**
     * 模块增加额外属性.
     *
     * @param string $model
     *
     * @return array
     */
    abstract public function decorateAddProperty($model);

    /**
     * 模块修改额外属性.
     *
     * @param string $model
     *
     * @return array
     */
    abstract public function decorateEditProperty($moduleBo);

    /**
     * 保存属性时的额外修改
     * Enter description here ...
     *
     * @param array $property
     */
    public function decorateSaveProperty($property, $moduleid)
    {
        return $property;
    }

    final public function buildAutoData($field, $order, $limit, $offset)
    {
        $this->_data = $this->getData($field, $order, $limit, $offset);

        return $this->_formatData();
    }

    final public function buildDataByIds($ids)
    {
        $this->_data = $this->fetchData($ids);

        return $this->_formatData();
    }

    public function setModuleBo(PwDesignModuleBo $moduleBo)
    {
        $this->_moduleBo = $moduleBo;
        $this->_viewSet = $moduleBo->getView();
    }

    /**
     * 用于对推送数据类型的扩展.
     *
     * @param array $ids
     */
    protected function fetchData($ids)
    {
        return array();
    }

    /**
     * 格式化时间.
     *
     * @param int $time
     */
    final protected function _formatTime($time)
    {
        if (!$time) {
            return '';
        }

        return $this->_viewSet['timefmt'] ? Pw::time2str($time, $this->_viewSet['timefmt']) : Pw::time2str($time, 'auto');
    }

    /**
     * 格式化标题.
     *
     * @param string $string
     */
    final protected function _formatTitle($string)
    {
        if (!$string) {
            return '';
        }

        return $this->_viewSet['titlenum'] > 0 ? Pw::substrs($string, $this->_viewSet['titlenum']) : $string;
    }

    /**
     * 格式化简介.
     *
     * @param string $time
     */
    final protected function _formatDes($string)
    {
        $string = Pw::stripWindCode($string);
        $string = preg_replace("/\r\n|\n|\r/", '', $string);
        $string = str_replace(' ', '', $string);

        return $this->_viewSet['desnum'] > 0 ? Pw::substrs($string, $this->_viewSet['desnum']) : $string;
    }

    private function _formatData()
    {
        $_tmp = $_data = array();
        Wind::import('SRV:design.bo.PwDesignModelBo');
        $bo = new PwDesignModelBo($this->_moduleBo->getModel());
        $signKeys = $bo->getSignKeys();
        $standard = $bo->getStandardSign();
        foreach ($standard as &$v) {
            $v = $this->_transformSign($v);
        }
        foreach ($this->_data as $data) {
            foreach ($signKeys as $signKey) {
                list($sign, $name, $key) = $signKey;
                if (!$sign = $this->_transformSign($sign)) {
                    continue;
                }
                if (isset($data[$key])) {
                    $_data[$sign] = $data[$key];
                }
            }
            $_data['standard_title'] = $_data[$standard['sTitle']];
            $_data['standard_fromid'] = $_data[$standard['sFromId']];
            $_data['standard_fromapp'] = $this->_moduleBo->getModel();
            $_data['standard_style'] = $data['__style'];
            $_data['standard'] = $standard;
            $_tmp[] = $_data;
        }

        return $_tmp;
    }

    /**
     * 对单个标签进行进得key转换
     * Enter description here ...
     *
     * @param unknown_type $sign
     */
    private function _transformSign($sign)
    {
        if (preg_match_all('/\{(\w+)\|(.+)}/U', $sign, $matche)) {
            // 对多元标签进行key过滤
            foreach ($matche[1] as $k => $v) {
                return str_replace($matche[0][$k], $v, $sign);
            }
        }
        if (!preg_match('/\{(\w+)}/isU', $sign, $matches)) {
            return false;
        }

        return $matches[1];
    }
}
