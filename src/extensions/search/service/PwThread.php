<?php

defined('WEKIT_VERSION') || exit('Forbidden');
/**
 * 帖子搜索服务
 */
class PwThread
{
    const FETCH_MAIN = 1;        //帖子基本信息
    const FETCH_CONTENT = 2;    //帖子内容相关信息
    const FETCH_ALL = 3;

    const SPECIAL_SORT_TOP1 = 101;
    const SPECIAL_SORT_TOP2 = 102;
    const SPECIAL_SORT_TOP3 = 103;

    const STATUS_LOCKED = 1;
    const STATUS_CLOSED = 2;
    const STATUS_DOWNED = 3;
    const STATUS_OPERATORLOG = 4; //是否有帖子操作日志

    /**
     * 统计帖子数(搜索).
     *
     * @param object $so
     *
     * @return int
     */
    public function countSearchThread(PwThreadSo $so)
    {
        return $this->_getThreadMergeDao()->countSearchThread($so->getData());
    }

    /**
     * 搜索帖子.
     *
     * @param object $so
     *
     * @return array
     */
    public function searchThread(PwThreadSo $so, $limit = 20, $offset = 0, $fetchmode = self::FETCH_MAIN)
    {
        return $this->_getThreadMergeDao()->searchThread($fetchmode, $so->getData(), $so->getOrderby(), $limit, $offset);
    }

    protected function _getThreadMergeDao()
    {
        return Wekit::loadDao('EXT:search.service.dao.PwThreadMergeDao');
    }
}
