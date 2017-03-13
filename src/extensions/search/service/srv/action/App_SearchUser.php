<?php

Wind::import('EXT:search.service.srv.action.App_SearchAction');

class App_SearchUser extends App_SearchAction
{
    protected $fid = 0;

    /**
     * 搜索统计
     *
     * @param PwThreadSo $so
     *
     * @return int
     */
    public function countSearch($so)
    {
        return $this->_getSearch()->countSearchUser($so);
    }

    /**
     * 搜索.
     *
     * @param PwThreadSo $so
     * @param int        $limit 查询条数
     * @param int        $start 开始查询的位置
     *
     * @return array
     */
    public function search($so, $limit = 20, $start = 0)
    {
        return $this->_getSearch()->searchUserAllData($so, $limit, $start);
    }

    /**
     * 组装帖子.
     *
     * @param array  $threads
     * @param string $keywords
     *
     * @return unknown_type
     */
    public function build($list, $keywords)
    {
        $user = [];
        foreach ($list as $_key => $_item) {
            $_item['username'] = $this->_highlighting(strip_tags($_item['username']), $keywords);
            $user[$_key] = $_item;
        }

        return $user;
    }

    /**
     * PwUserSearch.
     *
     * @return PwUserSearch
     */
    protected function _getSearch()
    {
        return Wekit::load('EXT:search.service.PwUserSearch');
    }
}
