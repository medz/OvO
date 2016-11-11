<?php

Wind::import('EXT:search.service.srv.action.App_SearchAction');

class App_SearchForum extends App_SearchAction
{
    protected $fid = 0;

    /**
     * 搜索统计
     *
     * @param  PwThreadSo $so
     * @return int
     */
    public function countSearch($so)
    {
        return $this->_getSearch()->countSearchForum($so);
    }

    /**
     * 搜索
     *
     * @param  PwThreadSo $so
     * @param  int        $limit 查询条数
     * @param  int        $start 开始查询的位置
     * @return array
     */
    public function search($so, $limit = 20, $start = 0)
    {
        return $this->_getSearch()->searchDesignForum($so, $limit, $start);
    }
    /**
     * 组装数据
     * @param  array        $threads
     * @param  string       $keywords
     * @return unknown_type
     */

    public function build($list, $keywords)
    {
        $user = array();
        foreach ($list as $_key => $_item) {
            $_item['username'] = $this->_highlighting(strip_tags($_item['username']), $keywords);
            $user[$_key] = $_item;
        }

        return $user;
    }

    /**
     * PwForum
     *
     * @return PwForum
     */
    protected function _getSearch()
    {
        return Wekit::load('EXT:search.service.PwForum');
    }
}
