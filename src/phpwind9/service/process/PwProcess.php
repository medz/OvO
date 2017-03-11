<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>.
 *
 * @author $Author$ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id$
 */
class PwProcess
{
    /**
     * 获取一条进程信息
     * Enter description here ...
     *
     * @param string $flag
     */
    public function getProcess($flag)
    {
        if (!$flag) {
            return array();
        }

        return $this->_getDao()->getProcess($flag);
    }

    /**
     * 更新一条进程信息.
     *
     * @param string $flag
     * @param string $time 过期时间
     */
    public function replaceProcess($flag, $time)
    {
        $time = (int) $time;
        if (empty($flag) || empty($time)) {
            return false;
        }
        $data = array();
        $data['flag'] = $flag;
        $data['expired_time'] = $time;

        return $this->_getDao()->replaceProcess($data);
    }

    public function deleteProcess($flag)
    {
        if (!$flag) {
            return false;
        }

        return $this->_getDao()->deleteProcess($flag);
    }

    /**
     * 删除缰死的进程.
     *
     * @param int $time
     */
    public function deleteProcessByTime($time)
    {
        $time = (int) $time;
        if ($time < 0) {
            return false;
        }

        return $this->_getDao()->deleteProcessByTime($time);
    }

    private function _getDao()
    {
        return Wekit::loadDao('process.dao.PwProcessDao');
    }
}
