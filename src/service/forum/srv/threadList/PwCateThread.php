<?php

defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('SRV:forum.srv.threadList.PwThreadDataSource');

/**
 * 帖子列表数据接口 / 普通列表.
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwCateThread.php 24749 2013-02-20 03:21:00Z jieyin $
 */
class PwCateThread extends PwThreadDataSource
{
    protected $fid;
    protected $forum;
    protected $forbidFids;
    protected $orderby = '';

    protected $specialSortTids;
    protected $count;

    public function __construct($forum, $forbidFids = array())
    {
        $this->forum = $forum;
        $this->fid = $forum->fid;
        $this->forbidFids = $forbidFids;

        $this->specialSortTids = array_keys($this->_getSpecialSortDs()->getSpecialSortByFid($forum->fid));
        $this->count = count($this->specialSortTids);
    }

    public function setOrderby($order)
    {
        if ($order == 'postdate') {
            $this->orderby = $order;
        }
    }

    public function getTotal()
    {
        return $this->_getThreadCateIndexDs()->countNotInFids($this->fid, $this->forbidFids);
    }

    public function getData($limit, $offset)
    {
        $threaddb = array();
        if ($offset < $this->count) {
            $array = $this->_getThreadDs()->fetchThreadByTid($this->specialSortTids, $limit, $offset);
            foreach ($array as $key => $value) {
                $value['issort'] = true;
                $threaddb[] = $value;
            }
            $limit -= count($threaddb);
        }
        $offset -= min($this->count, $offset);
        if ($limit > 0) {
            $tids = $this->_getThreadCateIndexDs()->fetchNotInFid($this->fid, $this->forbidFids, $limit, $offset, $this->orderby);
            $array = $this->_getThreadDs()->fetchThread($tids);
            $array = $this->_sort($array, $tids);
            foreach ($array as $key => $value) {
                $threaddb[] = $value;
            }
        }

        return $threaddb;
    }

    protected function _getThreadDs()
    {
        return Wekit::load('forum.PwThread');
    }

    protected function _getThreadCateIndexDs()
    {
        return Wekit::load('forum.PwThreadCateIndex');
    }

    protected function _getSpecialSortDs()
    {
        return Wekit::load('forum.PwSpecialSort');
    }

    protected function _sort($data, $sort)
    {
        $result = array();
        foreach ($sort as $tid) {
            $result[$tid] = $data[$tid];
        }

        return $result;
    }
}
