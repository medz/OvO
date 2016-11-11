<?php

Wind::import('EXT:search.service.srv.action.App_SearchAction');

class App_SearchThread extends App_SearchAction
{
    protected $fid = 0;

    /**
     * 搜索统计帖子
     *
     * @param  PwThreadSo $so
     * @return int
     */
    public function countSearch($so)
    {
        return $this->_getThreadSearch()->countSearchThread($so);
    }

    /**
     * 搜索帖子
     *
     * @param  PwThreadSo $so
     * @param  int        $limit 查询条数
     * @param  int        $start 开始查询的位置
     * @return array
     */
    public function search($so, $limit = 20, $start = 0)
    {
        return $this->_getThreadSearch()->searchThread($so, $limit, $start, PwThread::FETCH_ALL);
    }

    /**
     * 组装帖子数据
     * @param  array        $threads
     * @param  string       $keywords
     * @return unknown_type
     */
    public function build($list, $keywords)
    {
        if (!$list) {
            return false;
        }
        $keywords = (is_array($keywords)) ? $keywords : explode(' ', $keywords);
        $data = array();
        foreach ($list as $t) {
            $t['subject'] = strip_tags($t['subject']);
            $t['content'] = strip_tags($t['content']);
            $t['content'] = Wekit::load('forum.srv.PwThreadService')->displayContent($t['content'], $t['useubb'], array(), 170);
            foreach ($keywords as $keyword) {
                $keyword = stripslashes($keyword);
                $keyword && $t['subject'] = $this->_highlighting($t['subject'], $keyword);
                $keyword && $t['content'] = $this->_highlighting($t['content'], $keyword);
            }
            $data[] = $t;
        }

        return $data;
    }

    /**
     * PwThread
     * @return PwThread
     */
    protected function _getThreadSearch()
    {
        return Wekit::load('EXT:search.service.PwThread');
    }
}
