<?php

 
/**
 * the last known user to change this file in the repository  <$LastChangedBy: jieyin $>.
 *
 * @author $Author: jieyin $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwMiscPostDo.php 20488 2012-10-30 08:00:43Z jieyin $
 */
class PwMiscPostDo extends PwPostDoBase
{
    public function addPost($pid, $tid)
    {
        $userBo = Wekit::getLoginUser();
        $ds = Wekit::load('user.PwUserBehavior');
        $time = Pw::getTime();
        $ds->replaceBehavior($userBo->uid, 'post_days', $time);
        $posts = Wekit::load('forum.PwThread')->getPostByTid($tid, 1, 0, true);
        if (array_key_exists($pid, $posts)) {
            $ds->replaceBehavior($userBo->uid, 'safa_times');
        }
    }
}
