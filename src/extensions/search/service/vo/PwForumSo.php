<?php

defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 版块门户调用搜索条件
 *
 * @author $Author: jinlong.panjl $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwForumSo.php 20513 2012-10-30 09:29:24Z jinlong.panjl $
 * @package
 */

class PwForumSo
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
     * 搜索版块名称
     */
    public function setName($name)
    {
        $this->_data['name'] = trim($name);

        return $this;
    }

    /**
     * 搜索版块
     */
    public function setFid($fid)
    {
        $this->_data['fid'] = $fid;

        return $this;
    }

    /**
     * 回复总数排序
     */
    public function orderbyPosts($asc)
    {
        $this->_orderby['posts'] = (bool) $asc;

        return $this;
    }

    /**
     * 总帖数排序
     */
    public function orderbyArticle($asc)
    {
        $this->_orderby['article'] = (bool) $asc;

        return $this;
    }

    /**
     * 主题总数排序
     */
    public function orderbyThreads($asc)
    {
        $this->_orderby['threads'] = (bool) $asc;

        return $this;
    }

    /**
     * 最后回复排序
     */
    public function orderbyLastPostTime($asc)
    {
        $this->_orderby['lastpost_time'] = (bool) $asc;

        return $this;
    }

    /**
     * 今日发帖排序
     */
    public function orderbyTodaythreads($asc)
    {
        $this->_orderby['todaythreads'] = (bool) $asc;

        return $this;
    }

    /**
     * 今日回复排序
     */
    public function orderbyTodayposts($asc)
    {
        $this->_orderby['todayposts'] = (bool) $asc;

        return $this;
    }
}
