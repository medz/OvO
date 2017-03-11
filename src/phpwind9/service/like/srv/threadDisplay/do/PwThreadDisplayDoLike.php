<?php

defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * the last known user to change this file in the repository  <$LastChangedBy: jieyin $>.
 *
 * @author $Author: jieyin $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwThreadDisplayDoLike.php 24889 2013-02-25 08:29:24Z jieyin $
 */
class PwThreadDisplayDoLike extends PwThreadDisplayDoBase
{
    public function bulidRead($read)
    {
        $info = array();
        if (!$read['pid']) {
            $info = Wekit::load('like.PwLikeContent')->getInfoByTypeidFromid(PwLikeContent::THREAD, $read['tid']);
        }
        if ($read['pid'] == 0 && $info['users']) {
            $uids = explode(',', $info['users']);
            if (count($uids) > 10) {
                $uids = array_slice($uids, 0, 10);
            }
            $users = Wekit::load('user.PwUser')->fetchUserByUid($uids);
            foreach ($uids as $uid) {
                if (!$uid) {
                    continue;
                }
                $read['lastLikeUsers'][$uid]['uid'] = $uid;
                $read['lastLikeUsers'][$uid]['username'] = $users[$uid]['username'];
                $read['lastLikeUsers'][$uid]['avatar'] = Pw::getAvatar($uid);
            }
        }

        return $read;
    }
}
