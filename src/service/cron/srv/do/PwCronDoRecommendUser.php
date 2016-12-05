<?php

Wind::import('SRV:cron.srv.base.AbstractCronBase');

/**
 * 可能认识的人队列更新.
 *
 * @author jinlong.panjl <jinlong.panjl@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id$
 */
class PwCronDoRecommendUser extends AbstractCronBase
{
    public function run($cronId)
    {
        /*		$service = Wekit::load('attention.srv.PwAttentionRecommendFriendsService');
        $ds = Wekit::load('attention.PwAttentionRecommendCron');
        $list = $ds->getAllCron();
        foreach ($list AS $v) {
            $service->updateRecommendFriend($v['uid']);
            $ds->deleteCron($v['uid']);
            sleep(2);
        }*/
    }
}
