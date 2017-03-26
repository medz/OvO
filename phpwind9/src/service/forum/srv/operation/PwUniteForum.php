<?php


/**
 * 删除帖子及其关联操作(扩展).
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwUniteForum.php 22211 2012-12-19 17:45:08Z jieyin $
 */
class PwUniteForum extends PwDoProcess
{
    public $fid;
    public $tofid;

    public function __construct($fid, $tofid)
    {
        $this->fid = $fid;
        $this->tofid = $tofid;
        parent::__construct();
    }

    protected function init()
    {
        $this->appendDo(new PwUniteForumDoMoveActicle($this));
        $this->appendDo(new PwUniteForumDoMoveAttach($this));
    }

    public function getIds()
    {
        return $this->fid;
    }

    protected function run()
    {
        $forum = new PwForumBo($this->fid, true);
        if (! $forum->isForum(true)) {
            return new PwError('BBS:forum.unite.error.fid.exists.not');
        }
        if ($forum->foruminfo['type'] == 'category') {
            return new PwError('BBS:forum.unite.error.fid.category');
        }
        if ($forum->getSubForums()) {
            return new PwError('BBS:forum.unite.error.hassub');
        }

        $toforum = new PwForumBo($this->tofid);
        if (! $toforum->isForum(true)) {
            return new PwError('BBS:forum.unite.error.tofid.exists.not');
        }
        if ($toforum->foruminfo['type'] == 'category') {
            return new PwError('BBS:forum.unite.error.tofid.category');
        }
        if ($this->fid == $this->tofid) {
            return new PwError('BBS:forum.unite.error.same');
        }

        Wekit::load('forum.PwForum')->deleteForum($this->fid);

        $dm = new PwForumDm($this->tofid);
        $dm->addThreads($forum->foruminfo['threads'])->addPosts($forum->foruminfo['posts']);
        Wekit::load('forum.PwForum')->updateForum($dm, PwForum::FETCH_STATISTICS);

        Wekit::load('forum.srv.PwForumService')->updateForumStatistics($forum->foruminfo['parentid']);
        Wekit::load('forum.srv.PwForumService')->updateForumStatistics($this->tofid);

        return true;
    }
}
