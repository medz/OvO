<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author $Author$ Foxsee@aliyun.com
 * @copyright  ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id$
 * @package
 */
class PwCronService
{
    /**
     * 判断一个系统任务是否存在，不存在就加一个,存在就启动并更新运行时间
     */
    public function getSysCron($cronFile, $time = 0)
    {
        Wind::import('SRV:cron.dm.PwCronDm');
        $ds = $this->_getCronDs();
        $cron = $ds->getCronByFile($cronFile);
        if ($cron['cron_id']) {
            if (!$time) {
                list($day, $hour, $minute) = explode('-', $cron['loop_daytime']);
                $time = $this->getNextTime($cron['loop_type'], $day, $hour, $minute);
            }
            $dm = new PwCronDm($cron['cron_id']);
            $dm->setNexttime($time);
            $ds->updateCron($dm);
        } else {
            $dm = new PwCronDm();
            $dm->setSubject($cronFile)
                ->setLooptype('day')
                ->setLoopdaytime('0', '3', '0')
                ->setCronfile($cronFile)
                ->setNexttime($time)
                ->setIsopen(2);
            $ds->addCron($dm);
        }

        return true;
    }

    /**
     * 更新系统计划任务
     *
     * Enter description here ...
     */
    public function updateSysCron()
    {
        $ds = $this->_getCronDs();
        Wind::import('SRV:cron.dm.PwCronDm');
        $path = Wind::getRealPath('SRV:cron.srv.system.systemCron');
        if (!is_file($path)) {
            return false;
        }
        $cron = @include $path;
        $sysCron = $ds->getList(2);
        $_sysCron = array();
        foreach ($sysCron as $k => $v) {
            $_sysCron[$v['cron_file']] = $v;
        }
        foreach ($cron as $k => $v) {
            if (!in_array($v['type'], array('month', 'week', 'day', 'hour', 'now'))) {
                continue;
            }
            if (!$v['file']) {
                continue;
            }
            $cronInfo = $ds->getCronByFile($v['file']);
            //if ($cronInfo) $ds->deleteCron($cronInfo['cron_id']);
            $day = $v['time']['day'];
            $hour = $v['time']['hour'];
            $minute = $v['time']['minute'];
            if ($cronInfo) {
                $dm = new PwCronDm($cronInfo['cron_id']);
            } else {
                $dm = new PwCronDm();
            }
            $dm->setSubject($v['name'])
                ->setLooptype($v['type'])
                ->setCronfile($v['file'])
                ->setIsopen(PwCron::SYSTEM)
                ->setCreatedtime(Pw::getTime());
            switch ($v['type']) {
                case 'month':
                    $nexttime = $this->getNextTime('month', $day, $hour);
                    $dm->setLoopdaytime($day, $hour)->setNexttime($nexttime);
                    break;
                case 'week':
                    $nexttime = $this->getNextTime('week', $day, $hour);
                    $dm->setLoopdaytime($day, $hour)->setNexttime($nexttime);
                    break;
                case 'day':
                    $nexttime = $this->getNextTime('day', 0, $hour);
                    $dm->setLoopdaytime(0, $hour)->setNexttime($nexttime);
                    break;
                case 'hour':
                    $nexttime = $this->getNextTime('hour', 0, 0, $minute);
                    $dm->setLoopdaytime(0, 0, $minute)->setNexttime($nexttime);
                    break;
                case 'now':
                    $nexttime = $this->getNextTime('now', $day, $hour, $minute);
                    $dm->setLoopdaytime($day, $hour, $minute)->setNexttime($nexttime);
                    break;
                default:
                    return false;
            }
            if ($cronInfo) {
                $resource = $ds->updateCron($dm);
            } else {
                $resource = $ds->addCron($dm);
            }
            unset($_sysCron[$v['file']]);
        }
        foreach ($_sysCron as $v) {
            $ds->deleteCron($v['cron_id']);
        }

        return true;
    }

    /**
     * 获得下次执行时间
     *
     * @param string $loopType
     * @param int    $day
     * @param int    $hour
     * @param int    $minute
     */
    public function getNextTime($loopType, $day = 0, $hour = 0, $minute = 0)
    {
        $time = Pw::getTime();
        if ($timezone = Wekit::C('site', 'time.timezone')) {
            $time += $timezone * 3600;
        }
        $_minute = intval(gmdate('i', $time));
        $_hour = gmdate('G', $time);
        $_day = gmdate('j', $time);
        $_week = gmdate('w', $time);
        $_mouth = gmdate('n', $time);
        $_year = gmdate('Y', $time);
        $nexttime = mktime($_hour, 0, 0, $_mouth, $_day, $_year);
        switch ($loopType) {
            case 'month':
                $isLeapYear = date('L', $time);
                $mouthDays = $this->_getMouthDays($_mouth, $isLeapYear);
                if ($day == 99) {
                    $day = $mouthDays;
                }
                $nexttime += ($hour < $_hour ? -($_hour - $hour) : $hour - $_hour) * 3600;
                if ($hour <= $_hour && $day == $_day) {
                    $nexttime += ($mouthDays - $_day + $day) * 86400;
                } else {
                    $nexttime += ($day < $_day ? $mouthDays - $_day + $day : $day - $_day) * 86400;
                }
                break;
            case 'week':
                $nexttime += ($hour < $_hour ? -($_hour - $hour) : $hour - $_hour) * 3600;
                if ($hour <= $_hour && $day == $_week) {
                    $nexttime += (7 - $_week + $day) * 86400;
                } else {
                    $nexttime += ($day < $_week ? 7 - $_week + $day : $day - $_week) * 86400;
                }
                break;
            case 'day':
                $nexttime += ($hour < $_hour ? -($_hour - $hour) : $hour - $_hour) * 3600;
                if ($hour <= $_hour) {
                    $nexttime += 86400;
                }
                break;
            case 'hour':
                $nexttime += $minute < $_minute ? 3600 + $minute * 60 : $minute * 60;
                break;
            case 'now':
                $nexttime = mktime($_hour, $_minute, 0, $_mouth, $_day, $_year);
                $_time = $day * 24 * 60;
                $_time += $hour * 60;
                $_time += $minute;
                $_time = $_time * 60;
                $nexttime += $_time;
                break;
        }
        if ($timezone) {
            $nexttime -= $timezone * 3600;
        }

        return $nexttime;
    }

    /**
     * 递归执行计划任务
     * Enter description here ...
     */
    public function runCron()
    {
        $_time = Pw::getTime();
        $cron = $this->_getCronDs()->getFirstCron();
        if (!$cron || $cron['next_time'] > $_time) {
            return false;
        }
        list($day, $hour, $minute) = explode('-', $cron['loop_daytime']);
        $nexttime = $this->getNextTime($cron['loop_type'], $day, $hour, $minute);
        Wind::import('SRV:cron.dm.PwCronDm');
        $dm = new PwCronDm($cron['cron_id']);
        $dm->setSubject($cron['subject'])
            ->setCronfile($cron['cron_file'])
            ->setModifiedtime($_time)
            ->setNexttime($nexttime);
        $this->_getCronDs()->updateCron($dm);

        if (!$this->_runAction($cron['cron_file'], $cron['cron_id'])) {
            return false;
        }
        $this->runCron();

        return true;
    }

    private function _runAction($filename = '', $cronId = 0)
    {
        if (!$filename || Pw::substrs($filename, 8, 0, false) != 'PwCronDo') {
            return false;
        }
        $fliePath = 'SRV:cron.srv.do.'.$filename;
        Wind::import($fliePath);
        $cron = new $filename();
        $cron->run($cronId);

        return true;
    }


    private function _getMouthDays($month, $isLeapYear)
    {
        if (in_array($month, array('1', '3', '5', '7', '8', '10', '12'))) {
            $days = 31;
        } elseif ($month != 2) {
            $days = 30;
        } else {
            if ($isLeapYear) {
                $days = 29;
            } else {
                $days = 28;
            }
        }

        return $days;
    }

    private function _getCronDs()
    {
        return Wekit::load('cron.PwCron');
    }
}
