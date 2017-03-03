<?php

defined('WEKIT_VERSION') || exit('Forbidden');

 

/**
 * 帖子列表数据接口 / 普通列表.
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwCommonThread.php 16394 2012-08-23 06:28:06Z long.shi $
 */
class PwCommonThread extends PwThreadDataSource
{
    protected $forum;
    protected $specialSortTids;
    protected $count;

    public function __construct($forum)
    {
        $this->forum = $forum;
        $this->specialSortTids = array_keys($this->_getSpecialSortDs()->getSpecialSortByFid($forum->fid));
        $this->count = count($this->specialSortTids);
    }

    public function getTotal()
    {
        return $this->forum->foruminfo['threads'] + $this->count;
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
            $array = $this->_getThreadDs()->getThreadByFid($this->forum->fid, $limit, $offset);
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

    protected function _getSpecialSortDs()
    {
        return Wekit::load('forum.PwSpecialSort');
    }
}
