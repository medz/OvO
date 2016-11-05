<?php

defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 帖子基础服务
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwThread.php 23306 2013-01-08 06:57:50Z jieyin $
 * @package forum
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
     * 获取单个帖子信息
     *
     * @param  int   $tid       帖子id
     * @param  int   $fetchmode 帖子资料 <必然为FETCH_*的一种或者组合>
     * @return array
     */
    public function getThread($tid, $fetchmode = self::FETCH_MAIN)
    {
        if (empty($tid)) {
            return array();
        }

        return $this->_getThreadDao($fetchmode)->getThread($tid);
    }

    /**
     * 获取多个帖子信息
     *
     * @param  array $tids      tid序列
     * @param  int   $fetchmode 帖子资料 <必然为FETCH_*的一种或者组合>
     * @return array
     */
    public function fetchThread($tids, $fetchmode = self::FETCH_MAIN)
    {
        if (empty($tids) || !is_array($tids)) {
            return array();
        }

        return $this->_getThreadDao($fetchmode)->fetchThread($tids);
    }

    /**
     * 获取某个版块的帖子列表 (按最后回复排序)
     *
     * @param  int   $fid       版块id
     * @param  int   $limit     个数
     * @param  int   $offset    起始偏移量
     * @param  int   $fetchmode 帖子资料 <必然为FETCH_*的一种或者组合>
     * @return array
     */
    public function getThreadByFid($fid, $limit, $offset = 0, $fetchmode = self::FETCH_MAIN)
    {
        return $this->_getThreadDao($fetchmode)->getThreadByFid($fid, $limit, $offset);
    }

    public function fetchThreadByTid($tids, $limit, $start, $fetchmode = self::FETCH_MAIN)
    {
        return $this->_getThreadDao($fetchmode)->fetchThreadByTid($tids, $limit, $start);
    }

    /**
     * 统计版块的帖子数/回复数
     *
     * @param  int   $fid 版块fid
     * @return array
     */
    public function countPosts($fid)
    {
        return $this->_getThreadDao()->countPosts($fid);
    }

    /**
     * 获取主题分类的帖子列表
     */
    public function getThreadByFidAndType($fid, $type, $limit, $start, $fetchmode = self::FETCH_MAIN)
    {
        return $this->_getThreadDao($fetchmode)->getThreadByFidAndType($fid, $type, $limit, $start);
    }

    public function countThreadByFidAndType($fid, $type)
    {
        return $this->_getThreadDao(self::FETCH_MAIN)->countThreadByFidAndType($fid, $type);
    }

    /**
     * 统计用户发帖数
     *
     * @param  int $uid
     * @return int
     */
    public function countThreadByUid($uid)
    {
        if (empty($uid)) {
            return 0;
        }

        return $this->_getThreadDao(self::FETCH_MAIN)->countThreadByUid($uid);
    }

    /**
     * 获取用户的帖子
     *
     * @param  int   $uid       用户id
     * @param  int   $limit     个数
     * @param  int   $offset    起始偏移量
     * @param  int   $fetchmode 帖子资料 <必然为FETCH_*的一种或者组合>
     * @return array
     */
    public function getThreadByUid($uid, $limit = 0, $offset = 0, $fetchmode = self::FETCH_MAIN)
    {
        if (empty($uid)) {
            return array();
        }

        return $this->_getThreadDao($fetchmode)->getThreadByUid($uid, $limit, $offset);
    }

    /**
     * 获取某个版块用户的帖子
     *
     * @param  int   $fid       版块id
     * @param  mixed $uids      用户id (int|array)
     * @param  int   $limit     个数
     * @param  int   $offset    起始偏移量
     * @param  int   $fetchmode 帖子资料 <必然为FETCH_*的一种或者组合>
     * @return array
     */
    public function getThreadsByFidAndUids($fid, $uids, $limit = 0, $offset = 0, $fetchmode = self::FETCH_MAIN)
    {
        if (empty($fid) || empty($uids)) {
            return array();
        }
        is_array($uids) || $uids = array($uids);

        return $this->_getThreadDao($fetchmode)->getThreadsByFidAndUids($fid, $uids, $limit, $offset);
    }

    /**
     * 增加帖子
     * 注：本接口只提供数据层的相关操作，完整的帖子发布接口请参照 PwPost::execute()
     *
     * @param  object $topicDm 帖子数据模型
     * @return mixed
     */
    public function addThread(PwTopicDm $topicDm)
    {
        if (($result = $topicDm->beforeAdd()) !== true) {
            return $result;
        }

        return $this->_getThreadDao(self::FETCH_ALL)->addThread($topicDm->getSetData());
    }

    /**
     * 更新帖子
     *
     * @param  object $topicDm   帖子数据模型
     * @param  int    $fetchmode 帖子资料 <必然为FETCH_*的一种或者组合>
     * @return mixed
     */
    public function updateThread(PwTopicDm $topicDm, $fetchmode = self::FETCH_ALL)
    {
        if (($result = $topicDm->beforeUpdate()) !== true) {
            return $result;
        }

        return $this->_getThreadDao($fetchmode)->updateThread($topicDm->tid, $topicDm->getData(), $topicDm->getIncreaseData(), $topicDm->getBitData());
    }

    /**
     * 批量更新帖子
     *
     * @param  array  $tids      帖子id
     * @param  object $topicDm   帖子数据模型
     * @param  int    $fetchmode 帖子资料 <必然为FETCH_*的一种或者组合>
     * @return mixed
     */
    public function batchUpdateThread($tids, PwTopicDm $topicDm, $fetchmode = self::FETCH_ALL)
    {
        if (empty($tids)) {
            return false;
        }
        if (($result = $topicDm->beforeUpdate()) !== true) {
            return $result;
        }

        return $this->_getThreadDao($fetchmode)->batchUpdateThread($tids, $topicDm->getData(), $topicDm->getIncreaseData(), $topicDm->getBitData());
    }

    /**
     * 还原帖子disabled属性
     *
     * @param  array $tids
     * @return bool
     */
    public function revertTopic($tids)
    {
        if (empty($tids) || !is_array($tids)) {
            return false;
        }

        return $this->_getThreadDao(self::FETCH_MAIN)->revertTopic($tids);
    }

    /**
     * 删除帖子
     * 注：本接口只提供数据层的相关操作，完整的帖子删除接口请参照 PwDeleteTopic::execute()
     *
     * @param int $tid
     */
    public function deleteThread($tid)
    {
        if (!$tid) {
            return false;
        }

        return $this->_getThreadDao(self::FETCH_ALL)->deleteThread($tid);
    }

    /**
     * 批量删除帖子
     * 注：本接口只提供数据层的相关操作，完整的帖子删除接口请参照 PwDeleteTopic::execute()
     *
     * @param array $tids
     */
    public function batchDeleteThread($tids)
    {
        if (empty($tids) || !is_array($tids)) {
            return false;
        }

        return $this->_getThreadDao(self::FETCH_ALL)->batchDeleteThread($tids);
    }

    /**
     * 统计帖子数(搜索)
     *
     * @param  object $so
     * @return int
     */
    public function countSearchThread(PwThreadSo $so)
    {
        return $this->_getThreadMergeDao()->countSearchThread($so->getData());
    }

    /**
     * 搜索帖子
     *
     * @param  object $so
     * @return array
     */
    public function searchThread(PwThreadSo $so, $limit = 20, $offset = 0, $fetchmode = self::FETCH_MAIN)
    {
        return $this->_getThreadMergeDao()->searchThread($fetchmode, $so->getData(), $so->getOrderby(), $limit, $offset);
    }


    /****************** 以上是主题接口 ******************\

    \****************** 以下是回复接口 ******************/



    /**
     * 获取一个回复
     *
     * @param  int   $pid 回复id
     * @return array
     */
    public function getPost($pid)
    {
        if (!$pid) {
            return array();
        }

        return $this->_getPostDao()->getPost($pid);
    }

    /**
     * 获取多个回复
     *
     * @param  array $pids 回复ids
     * @return array
     */
    public function fetchPost($pids)
    {
        if (empty($pids) || !is_array($pids)) {
            return false;
        }

        return $this->_getPostDao()->fetchPost($pids);
    }

    /**
     * 获取一个帖子的回复列表
     *
     * @param  int   $tid    帖子id
     * @param  int   $limit
     * @param  int   $offset
     * @return array
     */
    public function getPostByTid($tid, $limit = 20, $offset = 0, $asc = true)
    {
        if (empty($tid)) {
            return array();
        }

        return $this->_getPostDao()->getPostByTid($tid, $limit, $offset, $asc);
    }

    /**
     * 统计用户的回复数
     *
     * @param  int $uid
     * @return int
     */
    public function countPostByUid($uid)
    {
        if (empty($uid)) {
            return 0;
        }

        return $this->_getPostDao()->countPostByUid($uid);
    }

    /**
     * 获取用户的回复
     *
     * @param  int   $uid    用户id
     * @param  int   $limit  个数
     * @param  int   $offset 起始偏移量
     * @return array
     */
    public function getPostByUid($uid, $limit = 20, $offset = 0)
    {
        if (empty($uid)) {
            return array();
        }

        return $this->_getPostDao()->getPostByUid($uid, $limit, $offset);
    }

    /**
     * 统计用户(A)在帖子(B)中的回复数
     *
     * @param  int $tid
     * @param  int $uid
     * @return int
     */
    public function countPostByTidAndUid($tid, $uid)
    {
        if (empty($tid) || empty($uid)) {
            return 0;
        }

        return $this->_getPostDao()->countPostByTidAndUid($tid, $uid);
    }

    /**
     * 统计帖子(A)中的ID小于回复(B)的回复个数
     *
     * @param  int $tid
     * @param  int $pid
     * @return int
     */
    public function countPostByTidUnderPid($tid, $pid)
    {
        if (empty($tid) || empty($pid)) {
            return 0;
        }

        return $this->_getPostDao()->countPostByTidUnderPid($tid, $pid);
    }

    /**
     * 获取用户(A)在帖子(B)中的回复
     *
     * @param  int   $tid
     * @param  int   $uid
     * @param  int   $limit
     * @param  int   $offset
     * @param  bool  $asc
     * @return array
     */
    public function getPostByTidAndUid($tid, $uid, $limit = 20, $offset = 0, $asc = true)
    {
        if (empty($tid) || empty($uid)) {
            return array();
        }

        return $this->_getPostDao()->getPostByTidAndUid($tid, $uid, $limit, $offset, $asc);
    }

    /**
     * 统计回复数(搜索)
     *
     * @param  object $so
     * @return int
     */
    public function countSearchPost(PwPostSo $so)
    {
        return $this->_getPostDao()->countSearchPost($so->getData());
    }

    /**
     * 搜索回复
     *
     * @param  object $so
     * @return array
     */
    public function searchPost(PwPostSo $so, $limit = 20, $offset = 0)
    {
        return $this->_getPostDao()->searchPost($so->getData(), $so->getOrderby(), $limit, $offset);
    }

    /**
     * 增加一个回复
     *
     * @param  object $replyDm 回复数据模型
     * @return array
     */
    public function addPost(PwReplyDm $replyDm)
    {
        if (($result = $replyDm->beforeAdd()) !== true) {
            return $result;
        }

        return $this->_getPostDao()->addPost($replyDm->getData());
    }

    /**
     * 更新回复
     *
     * @param  int    $pid     回复id
     * @param  object $replyDm 回复数据模型
     * @return mixed
     */
    public function updatePost(PwReplyDm $replyDm)
    {
        if (($result = $replyDm->beforeUpdate()) !== true) {
            return $result;
        }

        return $this->_getPostDao()->updatePost($replyDm->pid, $replyDm->getData(), $replyDm->getIncreaseData());
    }

    /**
     * 批量更新帖子
     *
     * @param  array  $pids    回复id
     * @param  object $replyDm 帖子数据模型
     * @return mixed
     */
    public function batchUpdatePost($pids, PwReplyDm $replyDm)
    {
        if (empty($pids) || !is_array($pids)) {
            return false;
        }
        if (($result = $replyDm->beforeUpdate()) !== true) {
            return $result;
        }

        return $this->_getPostDao()->batchUpdatePost($pids, $replyDm->getData(), $replyDm->getIncreaseData());
    }

    /**
     * 批量更新帖子
     *
     * @param  array  $tids    帖子id
     * @param  object $replyDm 帖子数据模型
     * @return mixed
     */
    public function batchUpdatePostByTid($tids, PwReplyDm $replyDm)
    {
        if (empty($tids)) {
            return false;
        }
        if (($result = $replyDm->beforeUpdate()) !== true) {
            return $result;
        }

        return $this->_getPostDao()->batchUpdatePostByTid($tids, $replyDm->getData(), $replyDm->getIncreaseData());
    }

    /**
     * 还原帖子disabled属性
     *
     * @param  array $tids
     * @return bool
     */
    public function revertPost($tids)
    {
        if (empty($tids) || !is_array($tids)) {
            return false;
        }

        return $this->_getPostDao()->revertPost($tids);
    }

    /**
     * 根据回复id批量删除回复
     *
     * @param  array $pids 回复id
     * @return bool
     */
    public function batchDeletePost($pids)
    {
        if (empty($pids) || !is_array($pids)) {
            return false;
        }

        return $this->_getPostDao()->batchDeletePost($pids);
    }

    /**
     * 根据帖子id批量删除回复
     *
     * @param  array $tids 帖子id
     * @return bool
     */
    public function batchDeletePostByTid($tids)
    {
        if (empty($tids) || !is_array($tids)) {
            return false;
        }

        return $this->_getPostDao()->batchDeletePostByTid($tids);
    }

    public function getHit($tid)
    {
        return $this->_getThreadHitsDao()->get(intval($tid));
    }

    public function fetchHit($tids)
    {
        if (empty($tids) || !is_array($tids)) {
            return array();
        }

        return $this->_getThreadHitsDao()->fetch($tids);
    }

    public function updateHits($tid, $hits)
    {
        return $this->_getThreadHitsDao()->update(intval($tid), intval($hits));
    }

    public function syncHits()
    {
        return $this->_getThreadHitsDao()->syncHits();
    }

    protected function _getDaoMap()
    {
        return array(
            self::FETCH_MAIN => 'forum.dao.PwThreadsDao',
            self::FETCH_CONTENT => 'forum.dao.PwThreadsContentDao',
        );
    }

    protected function _getThreadDao($fetchmode = self::FETCH_MAIN)
    {
        return Wekit::loadDaoFromMap($fetchmode, $this->_getDaoMap(), 'PwThread');
    }

    protected function _getThreadMergeDao()
    {
        return Wekit::loadDao('forum.dao.PwThreadMergeDao');
    }

    protected function _getPostDao()
    {
        return Wekit::loadDao('forum.dao.PwPostsDao');
    }

    protected function _getThreadHitsDao()
    {
        return Wekit::loadDao('forum.dao.PwThreadsHitsDao');
    }
}
