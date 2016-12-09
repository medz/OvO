<?php

Wind::import('SRV:forum.srv.post.do.PwPostDoBase');
Wind::import('SRV:medal.srv.PwAutoAwardMedal');
/**
 * @author Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwMedalPostDo.php 20488 2012-10-30 08:00:43Z jieyin $
 */
class PwMedalPostDo extends PwPostDoBase
{
    /**
     * @var PwUserBo
     */
    private $userBo = null;

    /**
     * Enter description here ...
     *
     * @param PwPost $pwpost
     */
    public function __construct(PwPost $pwpost)
    {
        $this->userBo = $pwpost->user;
    }

    /* (non-PHPdoc)
     * @see PwPostDoBase::addPost()
     */
    public function addPost($pid, $tid)
    {
        /* @var $ds PwUserBehavior */
        $ds = Wekit::load('user.PwUserBehavior');
        $time = Pw::getTime();
        $behavior = $ds->getBehaviorList($this->userBo->uid);

        $condition = isset($behavior['post_days']['number']) ? (int) $behavior['post_days']['number'] : 0;
        $bp = new PwAutoAwardMedal($this->userBo);
        $bp->autoAwardMedal(2, $condition);

        $posts = Wekit::load('forum.PwThread')->getPostByTid($tid, 1, 0, true);
        if (array_key_exists($pid, $posts)) {
            $condition = isset($behavior['safa_times']['number']) ? (int) $behavior['safa_times']['number'] : 0;
            $bp->autoAwardMedal(4, $condition);
        }

        return true;
    }
}
