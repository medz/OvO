<?php

Wind::import('SRV:forum.srv.manage.PwThreadManageDo');
Wind::import('SRV:forum.dm.PwTopicDm');

/**
 * 帖子管理操作 - 移动
 *
 * @author jinlong.panjl <jinlong.panjl@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id$
 * @package wind
 */
class PwThreadManageDoMove extends PwThreadManageDo
{
    public $fid;
    public $topictype;
    public $forum;

    protected $tids;

    public function __construct(PwThreadManage $srv)
    {
        parent::__construct($srv);
    }

    public function check($permission)
    {
        if (!isset($permission['move']) || !$permission['move']) {
            return false;
        }
        if (!$this->srv->user->comparePermission(Pw::collectByKey($this->srv->data, 'created_userid'))) {
            return new PwError('permission.level.move', array('{grouptitle}' => $this->srv->user->getGroupInfo('name')));
        }
        if (isset($this->fid)) {
            Wind::import('SRV:forum.bo.PwForumBo');
            $this->forum = new PwForumBo($this->fid);
            if (!$this->forum->isForum()) {
                return new PwError('BBS:manage.error.move.targetforum');
            }
            if ($this->topictype && !$this->forum->forumset['topic_type']) {
                return new PwError('BBS:post.topictype.closed');
            }
            if ($this->forum->forumset['topic_type'] && $this->forum->forumset['force_topic_type'] && !$this->topictype) {
                $topicTypes = Wekit::load('SRV:forum.PwTopicType')->getTypesByFid($this->forum->fid);
                if ($topicTypes) {
                    return new PwError('BBS:post.topictype.empty');
                }
            }
        }

        return true;
    }

    public function gleanData($value)
    {
        $this->tids[] = $value['tid'];
    }

    /**
     * 设置需要复制到的版块
     *
     * @param  int $fid
     * @return int
     */
    public function setFid($fid)
    {
        $this->fid = intval($fid);

        return $this;
    }

    /**
     * 设置主题分类
     *
     * @param  int $topictype
     * @return int
     */
    public function setTopictype($topictype)
    {
        $this->topictype = intval($topictype);

        return $this;
    }

    /**
     * 复制帖子 | 复制特殊帖、附件等待做。。。
     *
     * @param  int $topictype
     * @return int
     */
    public function run()
    {
        $threads = Wekit::load('forum.PwThread')->fetchThread($this->tids);
        $topicDm = new PwTopicDm(true);
        $topicDm->setTopictype($this->topictype)
                ->setFid($this->fid);
        $this->_getThreadDs()->batchUpdateThread(array_keys($threads), $topicDm, PwThread::FETCH_MAIN);
        $this->_getAttachDs()->batchUpdateFidByTid($this->tids, $this->fid);

        $fids = array();
        foreach ($threads as $t) {
            if ($t['fid'] == $this->fid) {
                continue;
            }
            $fids[$t['fid']]['thread'] -= 1;
            $fids[$t['fid']]['replies'] -= $t['replies'];
            $fids[$this->fid]['thread'] += 1;
            $fids[$this->fid]['replies'] += $t['replies'];
        }
        if ($fids) {
            foreach ($fids as $fid => $value) {
                Wekit::load('forum.srv.PwForumService')->updateStatistics($fid, $value['thread'], $value['replies']);
            }
        }
        if (!$this->forum->isOpen()) {
            Wekit::load('attention.PwFresh')->batchDeleteByType(PwFresh::TYPE_THREAD_TOPIC, $this->tids);

            //回复与新鲜事的关联
            if ($data = Wekit::load('attention.PwFreshIndex')->fetchByTid($this->tids)) {
                Wekit::load('attention.PwFresh')->batchDelete(array_keys($data));
            }
        }
        //管理日志添加
        Wekit::load('log.srv.PwLogService')->addThreadManageLog($this->srv->user, 'move', $this->srv->getData(), $this->_reason, $this->fid.'|'.$this->topictype);
    }

    /**
     * Enter description here ...
     *
     * @return PwThread
     */
    private function _getThreadDs()
    {
        return Wekit::load('forum.PwThread');
    }

    private function _getAttachDs()
    {
        return Wekit::load('attach.PwThreadAttach');
    }
}
