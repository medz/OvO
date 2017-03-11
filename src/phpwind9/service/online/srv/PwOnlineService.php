<?php

/**
 * 在线服务记录接口.
 *
 * @author $Author: jieyin $ Foxsee@aliyun.com
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwOnlineService.php 28793 2013-05-24 03:55:18Z jieyin $
 */
class PwOnlineService
{
    public $time; //当前时间
    public $isVisitorTime; //有效用户时间(秒) 访问网站多久开始算在线
    public $spaceTime; //记录活动间隔时间
    public $offlineTime; //无活动离线时间(秒)

    public function __construct()
    {
        $config = Wekit::C('site');
        $this->time = Pw::getTime();
        $this->isVisitorTime = 1;
        $this->spaceTime = 10;
        $this->offlineTime = $config['onlinetime'] * 60;
    }

    /**
     * 切换版块时更新状态
     *
     * @param int $fid
     */
    public function forumOnline($fid)
    {
        $vistor = $this->getVisitor(true);
        if (!is_array($vistor)) {
            return $this->time;
        }
        list($ip, $createdTime, $modifyTime, $ext) = $vistor;
        $onlineTime = $this->time - (int) $modifyTime;
        $ext = unserialize($ext);
        if (isset($ext['currentFid']) && $ext['currentFid'] == $fid && $onlineTime < $this->spaceTime) {
            return false;
        }
        if (!$ip || !$createdTime || !$modifyTime) {
            $this->signVisitor($ip, $this->time, $this->time, array('currentFid' => $fid));    //初始标记
            return $this->time;
        } else {                                                                            //更新标记
            $this->signVisitor($ip, $createdTime, $this->time, array('currentFid' => $fid, 'beforeFid' => $ext['currentFid']));

            return $createdTime;
        }
    }

    /**
     * 切换空间时更新状态
     *
     * @param int $spaceUid
     */
    public function spaceOnline($spaceUid)
    {
        $vistor = $this->getVisitor(true);
        if (!is_array($vistor)) {
            return $this->time;
        }
        list($ip, $createdTime, $modifyTime, $ext) = $vistor;
        $onlineTime = $this->time - (int) $modifyTime;
        $ext = unserialize($ext);
        if (isset($ext['currentSpace']) && $ext['currentSpace'] == $spaceUid && $onlineTime < $this->spaceTime) {
            return false;
        }
        if (!$ip || !$createdTime || !$modifyTime) {
            $this->signVisitor($ip, $this->time, $this->time, array('currentSpace' => $spaceUid));    //初始标记
            return $this->time;
        } else {                                                                                    //更新标记
            $this->signVisitor($ip, $createdTime, $this->time, array('currentSpace' => $spaceUid, 'beforeSpace' => $ext['currentSpace']));

            return $createdTime;
        }
    }

    /**
     * 浏览时更新状态
     *
     * @param string $clientIp
     *
     * @return bool|string
     */
    public function visitOnline($clientIp)
    {
        $vistor = $this->getVisitor();
        if ($vistor === false || empty($clientIp)) {                    //不标记
            return false;
        } elseif ($vistor === true) {                                    //初始标记
            $this->signVisitor($clientIp, $this->time, $this->time);

            return false;
        } else {
            list($ip, $createdTime, $modifyTime) = $vistor;
            if (!$ip || !$createdTime || !$modifyTime) {
                $this->signVisitor($clientIp, $this->time, $this->time);    //初始标记
                return false;
            } else {                                                    //更新标记
                $this->signVisitor($ip, $createdTime, $this->time);

                return $createdTime;
            }
        }
    }

    /**
     * 登录时更新状态
     *
     * @param int    $uid
     * @param string $username
     * @param int    $gid
     */
    public function loginOnline($uid, $username, $gid, $ip)
    {
        if ($uid < 0) {
            return false;
        }
        $dm = Wekit::load('online.dm.PwOnlineDm');
        $dm->setUid($uid)
            ->setUsername($username)
            ->setModifytime($this->time)
            ->setCreatedtime($this->time)
            ->setGid($gid);
        Wekit::load('online.PwUserOnline')->replaceInfo($dm);

        //游客转为登录用户
        $vistor = $this->getVisitor();
        if (!is_array($vistor)) {
            return false;
        }
        list($ip, $createdTime, $modifyTime) = $vistor;
        Wekit::load('online.PwGuestOnline')->deleteInfo($ip, $createdTime);
        $this->signVisitor($ip, $this->time, $this->time);
    }

    /**
     * 登出时更新状态
     *
     * @param int $uid
     */
    public function logoutOnline($uid)
    {
        $vistor = $this->getVisitor();
        if (!is_array($vistor)) {
            return false;
        }
        list($ip, $createdTime, $modifyTime) = $vistor;
        //用户转换为游客
        $dm = Wekit::load('online.dm.PwOnlineDm');
        $dm->setIp($uid)
            ->setModifytime($this->time)
            ->setCreatedtime($this->time);
        Wekit::load('online.PwGuestOnline')->replaceInfo($dm);
        Wekit::load('online.PwUserOnline')->deleteInfo($uid);
        $this->signVisitor($ip, $this->time, $this->time);
    }

    /**
     * 标记一个访问者.
     *
     * @param string $ip
     * @param int    $createdTime
     * @param int    $modifyTime
     */
    public function signVisitor($ip, $createdTime, $modifyTime, $extension = array())
    {
        $ip = ip2long($ip);
        $sign = Pw::encrypt($ip.'_'.$createdTime.'_'.$modifyTime.'_'.serialize($extension));

        return Pw::setCookie('visitor', $sign);
    }

    /**
     * 获取本地的访问标记.
     *
     * @param $isRefresh  bool 是否强制刷新
     *
     * @return bool|array
     */
    public function getVisitor($isRefresh = false)
    {
        $sign = Pw::getCookie('visitor');
        if (empty($sign)) {
            return true;
        }
        $sign = Pw::decrypt($sign);
        $signs = explode('_', $sign);
        if ($isRefresh) {
            return $signs;
        }
        list($ip, $createdTime, $modifyTime) = $signs;
        $modifyTime = (int) $modifyTime;
        $createdTime = (int) $createdTime;
        if ($createdTime < 1 || $modifyTime < 1) {
            return true;
        }
        $onlineTime = $this->time - $modifyTime;
        if ($createdTime == $modifyTime && $onlineTime >= $this->isVisitorTime) {
            return $signs;
        }
        if ($onlineTime >= $this->spaceTime) {
            return $signs;
        }

        return false;
    }

    public function clearNotOnline()
    {
        $expiredTime = $this->time - $this->offlineTime;
        Wekit::load('online.PwUserOnline')->deleteInfoByTime($expiredTime);
        Wekit::load('online.PwGuestOnline')->deleteInfoByTime($expiredTime);
    }

    public function updateCountOnlineTime($uid, $startTime, $endTime)
    {
        $onlineTime = (int) $endTime - (int) $startTime;
        $uid = (int) $uid;
        if ($uid < 0) {
            return false;
        }

        $userDm = new PwUserInfoDm($uid);
        $userDm->setOnline($onlineTime);
        $ds = Wekit::load('user.PwUser');
        $ds->editUser($userDm, PwUser::FETCH_DATA);
    }
}
