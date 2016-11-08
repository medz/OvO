<?php

defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('SRV:forum.bo.PwForumBo');
Wind::import('SRV:user.dm.PwUserInfoDm');

/**
 * 帖子操作行为基类
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwPostAction.php 28950 2013-05-31 05:58:25Z jieyin $
 * @package forum
 */

abstract class PwPostAction extends PwBaseHookService
{
    public $forum;
    public $user;
    public $isBM;
    protected $userDm = null;
    protected $postDm;

    public function __construct($fid, PwUserBo $user = null)
    {
        $this->forum = new PwForumBo($fid);
        $this->user = $user ? $user : Wekit::getLoginUser();
        $this->isBM = $this->forum->isBM($this->user->username);
        parent::__construct();
    }

    /**
     * 检测数据是否初始化成功
     *
     * return bool
     */
    abstract public function isInit();

    /**
     * 当前的帖子类型
     */
    public function getSpecial()
    {
        return 'default';
    }

    /**
     * 检测是否有操作权限
     *
     * return bool
     */
    abstract public function check();

    /**
     * 获取数据模型
     *
     * return object PwPostDm
     */
    abstract public function getDm();

    /**
     * 获取数据
     *
     * return array
     */
    abstract public function getInfo();

    /**
     * 获取附件数据
     *
     * return array
     */
    abstract public function getAttachs();

    /**
     * 数据处理
     *
     * @param object $postDm 帖子数据模型
     *                       return void
     */
    abstract public function dataProcessing(PwPostDm $postDm);

    /**
     * 发布
     *
     * return bool
     */
    abstract public function execute();

    abstract public function getNewId();

    /**
     * 发布前置动作
     *
     * return bool
     */
    public function beforeRun($postDm)
    {
        return $this->runWithVerified('check', $postDm);
    }

    /**
     * 发布后置动作
     *
     * return bool
     */
    abstract public function afterRun();

    public function getCreditOperate()
    {
        return '';
    }

    public function isDisabled()
    {
        $config = Wekit::C('bbs');
        if ($config['post.check.open'] && !$this->user->inGroup($config['post.check.groups']) && PwPost::inTime($config['post.check.start_hour'], $config['post.check.start_min'], $config['post.check.end_hour'], $config['post.check.end_min'])) {
            return 1;
        }
        switch ($this->user->getPermission('post_check')) {
            case '2':
                $disabled = 0; break;
            case '1':
                $disabled = $this->isForumContentCheck() ? 1 : 0; break;
            default:
                $disabled = 1;
        }

        return $disabled;
    }

    abstract public function isForumContentCheck();

    /**
     * 检查发帖时间间隔
     */
    public function checkPostPertime()
    {
        $pertime = $this->user->getPermission('post_pertime'); //防灌水
        if ($pertime && Pw::getTime() - $this->user->info['lastpost'] < $pertime) {
            return new PwError('BBS:post.pertime', array('{pertime}' => $pertime));
        }

        return true;
    }

    public function checkPostNum()
    {
        $allow = $this->user->getPermission('threads_perday');
        if ($allow > 0 && $this->user->info['todaypost'] >= $allow && $this->user->info['lastpost'] > Pw::getTdtime()) {
            return new PwError(array('BBS:post.perday.max', array('{max}' => $allow)));
        }

        return true;
    }

    /**
     * 检查主题分类
     *
     * @param  PwPostDm     $postDm
     * @return bool|PwError
     */
    public function checkTopictype(PwPostDm $postDm)
    {
        $topicType = $postDm->getTopictype();
        if ($topicType && !$this->forum->forumset['topic_type']) {
            return new PwError('BBS:post.topictype.closed');
        }
        if ($this->forum->forumset['topic_type'] && $this->forum->forumset['force_topic_type'] && !$postDm->getTopictype()) {
            $topicTypes = Wekit::load('SRV:forum.PwTopicType')->getTypesByFid($this->forum->fid);
            if ($topicTypes) {
                return new PwError('BBS:post.topictype.empty');
            }
        }
        $permission = $this->user->getPermission('operate_thread');
        if ($topicType && !$permission['type']) {
            $topicTypes or $topicTypes = Wekit::load('SRV:forum.PwTopicType')->getTypesByFid($this->forum->fid);
            if ($topicTypes[$topicType]['issys']) {
                return new PwError('BBS:post.topictype.admin');
            }
        }

        return true;
    }

    /**
     * 检测是否内容重复
     *
     * @param  string       $str 内容
     * @return bool|PwError
     */
    public function checkContentHash($str)
    {
        if (Wekit::C('bbs', 'content.duplicate') && $this->getHash($str) == $this->user->info['postcheck']) {
            return new PwError('BBS:post.content.duplicate');
        }

        return true;
    }

    public function updateUser()
    {
        $userDm = $this->getUserDm();
        $userDm->setLastpost(Pw::getTime());
        if ($this->user->info['lastpost'] < Pw::getTdtime()) {
            $userDm->setTodaypost(0)->setTodayupload(0);
        }

        return $userDm;
    }

    final public function getUserDm($force = true)
    {
        if (!is_object($this->userDm) && $force) {
            $this->userDm = new PwUserInfoDm($this->user->uid);
        }

        return $this->userDm;
    }

    public function getHash($str)
    {
        $str = trim($str);
        strlen($str) > 200 && $str = substr($str, -200);

        return substr(md5($str), 8, 16);
    }

    protected function _getInterfaceName()
    {
        return 'PwPostDoBase';
    }
}
