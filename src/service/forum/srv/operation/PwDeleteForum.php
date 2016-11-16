<?php

defined('WEKIT_VERSION') || exit('Forbidden');


Wind::import('SRV:forum.bo.PwForumBo');
Wind::import('HOOK:PwDeleteForum.PwDeleteForumDoDeleTeTopic');

/**
 * 删除帖子及其关联操作(扩展)
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwDeleteForum.php 21318 2012-12-04 09:24:09Z jieyin $
 * @package forum
 */

class PwDeleteForum extends PwDoProcess
{
    public $fid;
    public $forum;
    public $user;

    public function __construct($fid, PwUserBo $user)
    {
        $this->fid = $fid;
        $this->user = $user;
        $this->forum = new PwForumBo($this->fid);
        parent::__construct();
    }

    protected function init()
    {
        $this->appendDo(new PwDeleteForumDoDeleTeTopic($this));
    }

    public function getIds()
    {
        return $this->fid;
    }

    protected function run()
    {
        if (!$this->forum->isForum(true)) {
            return new PwError('BBS:forum.operate.error.exists.not');
        }
        if ($this->forum->getSubForums()) {
            return new PwError('BBS:forum.delete.error.hassub');
        }
        Wekit::load('forum.PwForum')->deleteForum($this->fid);
        Wekit::load('forum.srv.PwForumService')->updateForumStatistics($this->forum->foruminfo['parentid']);

        return true;
    }
}
