<?php

defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 版块会员公共服务
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @license http://www.phpwind.com
 *
 * @version $Id: PwForumUserService.php 19492 2012-10-15 08:05:10Z jieyin $
 */
class PwForumUserService
{
    /**
     * 获取版块活跃用户.
     *
     * @param int $fid
     * @param int $day
     * @param int $num
     *
     * @return array
     */
    public function getActiveUser($fid, $day = 7, $num = 12)
    {
        $key = "active_user_{$fid}_{$day}_{$num}";
        if (!$result = Wekit::cache()->get($key)) {
            $result = $this->_getActiveUser($fid, $day, $num);
            Wekit::cache()->set($key, $result, [], 3600);
        }

        return $result;
    }

    protected function _getActiveUser($fid, $day, $num)
    {
        $time = Pw::getTime() - ($day * 86400);
        $array = [];
        $thread = Wekit::load('forum.PwThreadExpand')->countUserThreadByFidAndTime($fid, $time, $num);
        $post = Wekit::load('forum.PwThreadExpand')->countUserPostByFidAndTime($fid, $time, $num);
        foreach ($thread as $key => $value) {
            if (!$key) {
                continue;
            }
            $array[$key] = $value['count'];
        }
        foreach ($post as $key => $value) {
            if (!$key) {
                continue;
            }
            if (isset($array[$key])) {
                $array[$key] += $value['count'];
            } else {
                $array[$key] = $value['count'];
            }
        }
        arsort($array);

        return array_slice($array, 0, $num, true);
    }
}
