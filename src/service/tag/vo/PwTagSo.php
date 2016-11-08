<?php

defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 话题搜索条件
 *
 * @author jinlong.panjl <jinlong.panjl@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id$
 * @package wind
 */
class PwTagSo
{
    protected $_data = array();
    protected $_orderby = array();

    public function getData()
    {
        return $this->_data;
    }

    public function getOrderby()
    {
        return $this->_orderby;
    }

    /**
     * 搜索话题ID
     * @param array $tag_ids
     */
    public function setTagId($tag_ids)
    {
        $this->_data['tag_id'] = $tag_ids;

        return $this;
    }

    /**
     * 搜索话题分类
     * @param int $category_id
     */
    public function setCategoryId($category_id)
    {
        $this->_data['category_id'] = $category_id;

        return $this;
    }

    /**
     * 搜索是否热门
     * @param int $logo
     */
    public function setIfhot($ifhot)
    {
        $this->_data['ifhot'] = $ifhot;

        return $this;
    }

    /**
     * 搜索有封面话题
     * @param int $logo
     */
    public function setIflogo($logo)
    {
        $this->_data['iflogo'] = $logo;

        return $this;
    }

    public function orderbyAttentionCount($asc = false)
    {
        $this->_orderby['attention_count'] = (bool) $asc;

        return $this;
    }

    public function orderbyContentCount($asc = false)
    {
        $this->_orderby['content_count'] = (bool) $asc;

        return $this;
    }

    public function orderbyCreatedTime($asc = false)
    {
        $this->_orderby['created_time'] = (bool) $asc;

        return $this;
    }
}
