<?php


/**
 * the last known user to change this file in the repository  <$LastChangedBy: jieyin $>.
 *
 * @author $Author: jieyin $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwMiscThreadDo.php 18510 2012-09-19 01:55:21Z jieyin $
 */
class PwMiscThreadDo extends PwPostDoBase
{
    public function addThread($tid)
    {
        $userBo = Wekit::getLoginUser();
        $ds = Wekit::load('user.PwUserBehavior');
        $time = Pw::getTime();
        $ds->replaceBehavior($userBo->uid, 'thread_days', $time);
        $ds->replaceBehavior($userBo->uid, 'thread_count');
    }
}
