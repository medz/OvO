<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>.
 *
 * @author $Author$ Foxsee@aliyun.com
 * @copyright  ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id$
 */
class PwProcessService
{
    /**
     * 判断一个进程是否存在
     * Enter description here ...
     *
     * @param string $flag
     */
    public function isProcess($flag)
    {
        $time = Pw::getTime();
        $process = Wekit::load('process.PwProcess')->getProcess($flag);
        if (!$process) {
            return false;
        }
        if ($process['expired_time'] < $time) {
            return false;
        }

        return true;
    }

    /**
     * 对进程进行加锁
     * Enter description here ...
     *
     * @param string $flag
     * @param int    $expiredTime
     *
     * @return bool
     */
    public function lockProcess($flag, $expiredTime = 30)
    {
        $ds = Wekit::load('process.PwProcess');
        $time = Pw::getTime();
        $expiredTime += $time;
        $process = $ds->getProcess($flag);
        $ds->deleteProcessByTime($time - 3000);
        if ($process && $process['expired_time'] > $expiredTime) {
            return false;
        } else {
            return $ds->replaceProcess($flag, $expiredTime);
        }
    }

    /**
     * 对进程进行解锁
     * Enter description here ...
     *
     * @param string $flag
     */
    public function unlockProcess($flag)
    {
        $ds = Wekit::load('process.PwProcess');
        $ds->deleteProcess($flag);
    }
}
