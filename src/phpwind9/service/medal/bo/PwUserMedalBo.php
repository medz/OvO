<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: xiaoxia.xuxx $>.
 *
 * @author Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwUserMedalBo.php 18821 2012-09-28 03:47:15Z xiaoxia.xuxx $
 */
 class PwUserMedalBo
 {
     public $medals = array();
     public $uid = 0;
     private $_status = array();
     private $_logs = array();

     public function __construct($uid)
     {
         $this->uid = (int) $uid;
         $this->getMedals();
     }

    /**
     * 获取已经领取的勋章.
     */
    public function getAlreadyMedals()
    {
        $_medals = array();
        foreach ($this->_status as $key => $value) {
            if ($value == 4) {
                $_medals[$key] = $this->medals[$key];
            }
        }

        return $_medals;
    }

    /**
     * 组装我参与的勋章及自动勋章列表.
     */
    public function getMyAndAutoMedal()
    {
        $_medals = array();
        //arsort($this->_status);
        foreach ($this->_status as $key => $value) {
            if (!isset($this->medals[$key])) {
                continue;
            }
            $_medals[$key] = $this->medals[$key];
            $_medals[$key]['award_status'] = $value;
            $_medals[$key]['log_id'] = $this->_logs[$key];
        }
        $autos = Wekit::load('medal.PwMedalInfo')->getInfoListByReceiveType(1, 1);
        $sevice = $this->_getMedalService();
        foreach ($autos as $key => $auto) {
            if (!isset($_medals[$key])) {
                $auto['icon'] = $sevice->getMedalImage($auto['path'], $auto['icon']);
                $auto['image'] = $sevice->getMedalImage($auto['path'], $auto['image']);
                $auto['award_status'] = 0;
                $_medals[$key] = $auto;
            }
        }

        return $_medals;
    }

    /**
     * 获取我参与的勋章.
     */
    protected function getMedals()
    {
        $medalIds = array();
        $logs = Wekit::load('medal.PwMedalLog')->getInfoListByUid($this->uid);
        foreach ($logs as $log) {
            $medalIds[] = $log['medal_id'];
            $this->_status[$log['medal_id']] = $log['award_status'];
            $this->_logs[$log['medal_id']] = $log['log_id'];
        }
        $medals = Wekit::load('medal.PwMedalInfo')->fetchMedalInfo($medalIds);
        $sevice = $this->_getMedalService();
        foreach ($medals as &$medal) {
            $medal['icon'] = $sevice->getMedalImage($medal['path'], $medal['icon']);
            $medal['image'] = $sevice->getMedalImage($medal['path'], $medal['image']);
        }
        $this->medals = $medals;
    }

     private function _getMedalService()
     {
         return Wekit::load('SRV:medal.srv.PwMedalService');
     }
 }
