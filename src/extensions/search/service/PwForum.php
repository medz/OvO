<?php

defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 版块基础服务
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwForum.php 20973 2012-11-22 10:33:45Z jieyin $
 */
class PwForum
{
    const FETCH_MAIN = 1;        //版块主要信息
    const FETCH_STATISTICS = 2;    //版块统计信息
    const FETCH_EXTRA = 4;        //版块扩展信息
    const FETCH_ALL = 7;

    public function searchDesignForum(PwForumSo $so, $limit = 20, $offset = 0)
    {
        return $this->_getDesignForumDao()->searchForum($so->getData(), $so->getOrderby(), $limit, $offset);
    }

    public function countSearchForum(PwForumSo $so)
    {
        return $this->_getDesignForumDao()->countSearchForum($so->getData());
    }

    protected function _getDesignForumDao()
    {
        return Wekit::loadDao('EXT:search.service.dao.PwDesignForumDao');
    }
}
