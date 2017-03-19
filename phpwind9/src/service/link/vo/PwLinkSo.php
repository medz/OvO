<?php

defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 友情链接搜索条件.
 *
 * @author jinlong.panjl <jinlong.panjl@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id$
 */
class PwLinkSo
{
    protected $_data = [];
    protected $_orderby = [];

    public function getData()
    {
        return $this->_data;
    }

    public function getOrderby()
    {
        return $this->_orderby;
    }

    /**
     * 搜索链接ID.
     *
     * @param array $lid
     */
    public function setLid($lid)
    {
        $this->_data['lid'] = $lid;

        return $this;
    }

    /**
     * 搜索名称.
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->_data['name'] = $name;

        return $this;
    }

    /**
     * 搜索URL.
     *
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->_data['url'] = $url;

        return $this;
    }

    /**
     * 搜索有logo.
     *
     * @param int $logo
     */
    public function setLogo($logo)
    {
        $this->_data['iflogo'] = $logo;

        return $this;
    }

    /**
     * 搜索ifcheck.
     *
     * @param int $ifcheck
     */
    public function setIfcheck($ifcheck)
    {
        $this->_data['ifcheck'] = $ifcheck;

        return $this;
    }

    /**
     * 搜索分类.
     *
     * @param int $typeid
     */
    public function setTypeid($typeid)
    {
        $this->_data['typeid'] = $typeid;

        return $this;
    }

    public function orderbyVieworder($asc)
    {
        $this->_orderby['vieworder'] = (bool) $asc;

        return $this;
    }
}
