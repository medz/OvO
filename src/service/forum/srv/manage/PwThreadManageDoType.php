<?php

 
 

/**
 * 帖子管理操作-主题分类.
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwThreadManageDoDigest.php 14445 2012-07-20 09:16:44Z jinlong.panjl $
 */
class PwThreadManageDoType extends PwThreadManageDo
{
    protected $tids;
    protected $isDeductCredit = true;
    protected $topictype = 0;
    protected $subTopicType = 0;
    protected $threadsInfo;
    protected $submit = false;

    /**
     * Enter description here ...
     *
     * @param PwThreadManage $srv
     */
    public function __construct(PwThreadManage $srv)
    {
        parent::__construct($srv);
        $this->threadsInfo = $srv->data;
    }

    /* (non-PHPdoc)
     * @see PwThreadManageDo::check()
     */
    public function check($permission)
    {
        if (!isset($permission['type']) || !$permission['type']) {
            return false;
        }

        return $this->checkTopicType();
    }

    /* (non-PHPdoc)
     * @see PwThreadManageDo::gleanData()
     */
    public function gleanData($value)
    {
        $this->tids[] = $value['tid'];
    }

    /**
     * 设置主题分类的一级分类.
     *
     * @param int $topictype
     *
     * @return PwThreadManageDoType
     */
    public function setTopictype($topictype, $subTopicType = '')
    {
        $this->topictype = intval($topictype);
        $this->subTopicType = intval($subTopicType);
        $this->submit = true;

        return $this;
    }

    /**
     *	获得本板块的所有主题分类.
     *
     *  @return array
     */
    public function getTopicTypes()
    {
        $forumset = $this->_getForum();

        return Wekit::load('forum.srv.PwTopicTypeService')->getTopictypes($forumset['fid']);
    }

    /* (non-PHPdoc)
     * @see PwThreadManageDo::run()
     */
    public function run()
    {
        $topicDm = new PwTopicDm(true);
        $topicDm->setTopictype($this->topictype);
        $this->_getThreadDs()->batchUpdateThread($this->tids, $topicDm, PwThread::FETCH_MAIN);

        //管理日志添加

        Wekit::load('log.srv.PwLogService')->addThreadManageLog($this->srv->user, 'type', $this->threadsInfo, $this->_reason, $this->topictype);
    }

    /**
     * Enter description here ...
     *
     * @return PwError|bool|Ambigous <number, unknown>
     */
    private function _getForum()
    {
        $fid = 0;
        foreach ($this->threadsInfo as $v) {
            if ($fid && $v['fid'] != $fid) {
                return new PwError('MANAGE.multiple_forums_typed');
            }
            $fid = $v['fid'];
        }
        if (!$fid) {
            return true;
        }
        $forum = $this->_getForumDS()->getForum($fid, PwForum::FETCH_EXTRA);
        $forumset = unserialize($forum['settings_basic']);
        $forumset['fid'] = $fid;

        return $forumset;
    }

    /**
     * 检查主题分类.
     *
     * @return PwError|bool
     */
    private function checkTopicType()
    {
        if (!$this->submit) {
            return true;
        }
        $forumset = $this->_getForum();
        $topicTypes = $this->getTopicTypes();
        if ($this->topictype && !$forumset['topic_type']) {
            return new PwError('BBS:post.topictype.closed');
        }
        if ($forumset['topic_type'] && $forumset['force_topic_type'] && !$this->topictype && $topicTypes) {
            return new PwError('BBS:post.topictype.empty');
        }
        //如果设置了一级分类，一级分类不存在，则报错
        //如果设置的一级分类存在的情况下，如果也设置了二级分类，并且二级分类和一级分类树形关系存在，则将用户主题分类设置为该二级分类ID
        if (!array_key_exists($this->topictype, $topicTypes)) {
            return new PwError('BBS:post.topictype.error');
        } elseif ($this->subTopicType && array_key_exists($this->subTopicType, $topicTypes[$this->topictype]['sub_type'])) {
            $this->topictype = $this->subTopicType;
        }

        return true;
    }

    /**
     * 帖子处理DS.
     *
     * @return PwThread
     */
    public function _getThreadDs()
    {
        return Wekit::load('forum.PwThread');
    }

    /**
     * 版块DM.
     *
     * @return PwForum
     */
    private function _getForumDS()
    {
        return Wekit::load('forum.PwForum');
    }
}
