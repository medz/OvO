<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>.
 *
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwMedalCache.php 22363 2012-12-21 12:16:44Z gao.wanggao $
 */
class PwMedalCache
{
    /**
     * 从缓存表获取一个用户的勋章.
     *
     * 非cahce:PwMedalService->getUserMedal($uid)
     *
     * @param string $userMedals
     */
    public function fetchMedal($medalIds)
    {
        if (!is_array($medalIds)) {
            return array();
        }
        $_medals = array();
        $cacheDs = Wekit::cache();
        $medals = $cacheDs->get('medal_all');
        $attachUrl = Pw::getPath('').'medal/';
        $localUrl = WindUrlHelper::checkUrl(PUBLIC_RES.'/images/medal/', PUBLIC_URL).'/';
        foreach ($medalIds as $id) {
            if (!isset($medals[$id])) {
                continue;
            }
            $path = $medals[$id]['path'] ? $attachUrl : $localUrl;
            $medals[$id]['image'] = $path.$medals[$id]['image'];
            $medals[$id]['icon'] = $path.$medals[$id]['icon'];
            $_medals[] = $medals[$id];
        }

        return $_medals;
    }

    /**
     * 从用户缓存表获取多个用户的勋章
     * 非cahce:PwMedalService->fetchUserMedal($uid).
     *
     * @param array $userMedals array[uid]=medalid
     */
    public function fetchUserMedal($userMedals)
    {
        if (!is_array($userMedals)) {
            return array();
        }
        $_userMedalIds = $_allMedalId = $_medals = array();
        foreach ($userMedals as $uid => $medalids) {
            $_userMedalIds[$uid] = !$userMedals[$uid] ? array() : explode(',', $userMedals[$uid]);
            $_allMedalId = array_merge($_allMedalId, $_userMedalIds[$uid]);
        }
        $_allMedalId = array_unique($_allMedalId);
        $cacheDs = Wekit::cache();
        $medals = $cacheDs->get('medal_all');
        $attachUrl = Pw::getPath('').'medal/';
        $localUrl = WindUrlHelper::checkUrl(PUBLIC_RES.'/images/medal/', PUBLIC_URL).'/';
        foreach ($_userMedalIds as $uid => $medalIds) {
            $_medalInfo = array();
            foreach ($medalIds as $id) {
                if (!$medals[$id]) {
                    continue;
                }
                $path = $medals[$id]['path'] ? $attachUrl : $localUrl;
                $_tmp = $medals[$id];
                $_tmp['image'] = $path.$_tmp['image'];
                $_tmp['icon'] = $path.$_tmp['icon'];

                $_medalInfo[] = $_tmp;
            }
            $_medals[$uid] = $_medalInfo;
        }

        return $_medals;
    }

    /**
     * 组装我参与的勋章及自动勋章列表.
     *
     * 非cahce:PwUserMedalBo->getMyAndAutoMedal()
     * Enter description here ...
     *
     * @param int $uid
     */
    public function getMyAndAutoMedal($uid)
    {
        if (!$uid) {
            return array();
        }
        $_medals = $myMedalIds = $status = array();
        $logs = Wekit::load('medal.PwMedalLog')->getInfoListByUid($uid);
        foreach ($logs as $log) {
            $myMedalIds[] = $log['medal_id'];
            $status[$log['medal_id']] = $log['award_status'];
        }
        $cacheDs = Wekit::cache();
        $autoMedalIds = $cacheDs->get('medal_auto');
        $medals = $cacheDs->get('medal_all');
        $attachUrl = Pw::getPath('').'medal/';
        $localUrl = WindUrlHelper::checkUrl(PUBLIC_RES.'/images/medal/', PUBLIC_URL).'/';
        $medalIds = array_merge($myMedalIds, $autoMedalIds);
        $medalIds = array_unique($medalIds);
        foreach ($medalIds as $id) {
            if (!isset($medals[$id])) {
                continue;
            }
            $medals[$id]['award_status'] = isset($status[$id]) ? $status[$id] : 0;
            $path = $medals[$id]['path'] ? $attachUrl : $localUrl;
            $_tmp = $medals[$id];
            $_tmp['image'] = $path.$_tmp['image'];
            $_tmp['icon'] = $path.$_tmp['icon'];
            $_medals[] = $_tmp;
        }

        return $_medals;
    }
}
