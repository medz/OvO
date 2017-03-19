<?php

 /**
  * 获取打卡显示状态
  *
  * @author jinlong.panjl <jinlong.panjl@aliyun-inc.com>
  * @copyright ©2003-2103 phpwind.com
  * @license http://www.phpwind.com
  *
  * @version $Id$
  */
 class PwPunchService
 {
     /**
     * 获取首页打卡状态
     *
     * @param PwUserBo $user
     *                       return array
     */
    public function getPunch($user = null)
    {
        !$user && $user = Wekit::getLoginUser();
        $punchData = unserialize($user->info['punch']);
        $havePunch = $this->isPunch($punchData);
        if (!$havePunch) {
            $unPunchDays = $punchData['time'] > 0 ? ceil((Pw::str2time(Pw::time2str(Pw::getTime(), 'Y-m-d')) - Pw::str2time(Pw::time2str($punchData['time'], 'Y-m-d'))) / 86400) : 1;
            $punchText = $unPunchDays > 1 ? "{$unPunchDays}天未打卡" : '每日打卡';

            return [true, $punchText, []];
        }
        $behaviorDays = $this->_getBehavior($punchData['time'], $punchData['days']);
        if ($punchData['username'] == $user->username && $havePunch) {
            $behaviorDays or $behaviorDays = 1;
            $punchText = "连续{$behaviorDays}天打卡";

            return [false, $punchText, []];
        }

        return [true, '继续打卡', $punchData];
    }

    /**
     * 获取个人空间打卡状态
     *
     * @param PwUserBo $user
     *                       return array
     */
    public function getSpacePunch(PwSpaceBo $space)
    {
        switch ($space->tome) {
            case PwSpaceBo::VISITOR:
                return [false, '', []];
            case PwSpaceBo::STRANGER:
                return [false, '', []];
            case PwSpaceBo::MYSELF:
                return $this->getPunch();
            case PwSpaceBo::ATTENTION:
                $spaceUser = $space->spaceUser;
                $punchData = unserialize($spaceUser['punch']);
                $havePunch = $this->isPunch($punchData);
                if (!$havePunch) {
                    return [true, '帮Ta打卡', []];
                }
                if ($punchData['username'] != $spaceUser['username']) {
                    $data = unserialize($spaceUser['punch']);

                    return [false, '帮Ta打卡', $data];
                }

                return [false, '帮Ta打卡', []];
        }
    }

    /**
     * 是否已经打卡
     *
     * @param array $punchData
     *                         return bool
     */
    public function isPunch($punchData)
    {
        $todayStart = Pw::str2time(Pw::time2str(Pw::getTime(), 'Y-m-d'));
        $todayEnd = $todayStart + 86400;

        return $punchData['time'] > $todayStart && $punchData['time'] < $todayEnd ? true : false;
    }

    /**
     * 获取打卡配置返回打卡和帮朋友打卡是否开启.
     *
     * @param array $punchData
     *                         return bool
     */
    public function getPunchConfig()
    {
        $config = Wekit::C('site');
        $punchOpen = $config['punch.open'] ? true : false;
        $punchFriendOpen = $config['punch.friend.open'] ? true : false;

        return [$punchOpen, $punchFriendOpen];
    }

    /**
     * 格式化时间.
     *
     * @param int $timestamp
     *                       return bool
     */
    public function formatWeekDay($timestamp)
    {
        $weeksArray = ['周日', '周一', '周二', '周三', '周四', '周五', '周六'];
        $weekDay = Pw::time2str($timestamp, 'w');

        return [Pw::time2str($timestamp, 'm.d'), $weeksArray[$weekDay]];
    }

     private function _getBehavior($time, $number)
     {
         $time = $time + 86400 * 2;
         $time = Pw::str2time(Pw::time2str($time, 'Y-m-d'));

         if ($time > 0 && $time < Pw::getTime()) {
             $number = 0;
         }

         return $number;
     }
 }
