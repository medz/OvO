<?php

/**
 * @提醒service
 *
 * @author jinlong.panjl <jinlong.panjl@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id$
 */
class PwRemindService
{
    /**
     * 根据内容组装@提醒人.
     *
     * @param string $content
     *
     * @return array
     */
    public function bulidRemind($content)
    {
        if (strpos($content, '@') === false) {
            return [];
        }
        $config = Wekit::C('register');
        $min = $config['security.username.min'];
        $max = $config['security.username.max'];

        $pattern = '/@([\x7f-\xff\dA-Za-z\.\_]+)/is';
        preg_match_all($pattern, $content, $matches);
        if (!$matches[1]) {
            return [];
        }
        $reminds = [];
        foreach ($matches[1] as $v) {
            $v = trim($v);
            if (!$v) {
                continue;
            }
            $reminds[] = $v;
        }

        return $reminds;
    }

    /**
     * 增加@提醒人.
     *
     * @param string $content
     *
     * @return array
     */
    public function addRemind($uid, $reminds)
    {
        $uid = intval($uid);
        if ($uid < 1 || !$reminds) {
            return false;
        }
        $remind = $this->_getRemindDs()->getByUid($uid);
        $remind = $remind['touid'] ? unserialize($remind['touid']) : [];
        $remind = array_unique(array_merge($reminds, $remind));
        $remind = array_slice($remind, 0, 10, true);
        $this->_getRemindDs()->replaceRemind($uid, serialize($remind));
    }

    /**
     * 组装最新@人数据.
     *
     * @param array $users array('uid' => username)
     *
     * @return string uid,username,uid,username....
     */
    public function buildUsers($uid, $reminds, $maxNum = 0)
    {
        $reminds = array_unique($reminds);
        if (!$reminds) {
            return [];
        }
        $users = $this->_getUserDs()->fetchUserByName($reminds);
        $_tmp = $array = [];
        foreach ($users as $v) {
            if ($uid == $v['uid']) {
                continue;
            }
            $_tmp[$v['username']] = $v['uid'];
        }
        $i = 0;
        foreach ($reminds as $v) {
            if (!isset($_tmp[$v])) {
                continue;
            }
            if ($maxNum && $i >= $maxNum) {
                break;
            }
            $array[$_tmp[$v]] = $v;
            $i++;
        }

        return $array;
    }

    /**
     * 格式化用户数组.
     *
     * @param array $users array('uid' => username)
     *
     * @return string uid,username,uid,username....
     */
    public function formatReminds($users)
    {
        if (!$users) {
            return false;
        }
        $user = '';
        foreach ($users as $uid => $username) {
            $user .= $uid.','.$username.',';
        }

        return rtrim($user, ',');
    }

    /**
     * @return PwRemind
     */
    private function _getRemindDs()
    {
        return Wekit::load('remind.PwRemind');
    }

    /**
     * PwUser.
     *
     * @return PwUser
     */
    private function _getUserDs()
    {
        return Wekit::load('user.PwUser');
    }
}
