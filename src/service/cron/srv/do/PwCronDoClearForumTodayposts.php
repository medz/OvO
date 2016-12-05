<?php

Wind::import('SRV:cron.srv.base.AbstractCronBase');
Wind::import('SRV:forum.dm.PwForumDm');
Wind::import('SRV:site.dm.PwBbsinfoDm');

/**
 * 计划任务，版块今日发帖清零
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @license http://www.phpwind.com
 *
 * @version $Id: PwCronDoClearForumTodayposts.php 21328 2012-12-04 11:32:35Z jieyin $
 */
class PwCronDoClearForumTodayposts extends AbstractCronBase
{
    public function run($cronId)
    {
        $srv = Wekit::load('forum.PwForum');
        $all = $srv->getForumList(PwForum::FETCH_MAIN | PwForum::FETCH_STATISTICS);

        $ypost = 0;
        foreach ($all as $value) {
            if ($value['type'] == 'category') {
                $ypost += $value['todayposts'];
            }
        }

        $bbsinfo = Wekit::load('site.PwBbsinfo')->getInfo(1);
        $dm = new PwBbsinfoDm();
        $dm->setYposts($ypost);
        if ($ypost > $bbsinfo['hposts']) {
            $dm->setHposts($ypost);
        }
        Wekit::load('site.PwBbsinfo')->updateInfo($dm);

        $dm = new PwForumDm(true);
        $dm->setTodayPosts(0);
        $srv->batchUpdateForum(array_keys($all), $dm, PwForum::FETCH_STATISTICS);
    }
}
