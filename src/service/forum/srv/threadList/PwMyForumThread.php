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
 * @version $Id: PwMyForumThread.php 19290 2012-10-12 08:13:34Z xiaoxia.xuxx $
 */
class PwMyForumThread extends PwThreadDataSource
{
    protected $fids;
    protected $order;
    protected $specialSortTids;
    protected $count;

    public function __construct(PwUserBo $user)
    {
        $fids = array_keys(Wekit::load('forum.PwForumUser')->getFroumByUid($user->uid));
        $this->fids = Wekit::load('forum.srv.PwForumService')->getAllowVisitForum($user, Wekit::load('forum.PwForum')->fetchForum($fids));
        $this->specialSortTids = array_keys($this->_getSpecialSortDs()->getSpecialSortByTypeExtra('topped', 3));
        $this->count = count($this->specialSortTids);
    }

    public function setOrderBy($order)
    {
        $this->order = $order;
        if ($order != 'lastpost') {
            $this->urlArgs['order'] = $order;
        }
    }

    public function getTotal()
    {
        return $this->_getThreadIndexDs()->countThreadInFids($this->fids) + $this->count;
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
            $tids = $this->_getThreadIndexDs()->fetchInFid($this->fids, $limit, $offset, $this->order);
            $tmp = $this->_getThreadDs()->fetchThread($tids);
            $tmp = $this->_sort($tmp, $tids);
            $tmp && $threaddb = array_merge($threaddb, $tmp);
        }

        return $threaddb;
    }

    protected function _getThreadDs()
    {
        return Wekit::load('forum.PwThread');
    }

    protected function _getThreadIndexDs()
    {
        return Wekit::load('forum.PwThreadIndex');
    }

    protected function _sort($data, $sort)
    {
        $result = array();
        foreach ($sort as $tid) {
            $result[$tid] = $data[$tid];
        }

        return $result;
    }

    protected function _getSpecialSortDs()
    {
        return Wekit::load('forum.PwSpecialSort');
    }
}
