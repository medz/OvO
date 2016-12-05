<?php

/**
 * 在线统计记录.
 *
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwOnlineStatistics.php 16817 2012-08-28 12:30:51Z gao.wanggao $
 */
class PwOnlineStatistics
{
    /**
     * 获取一条记录.
     *
     * @param string $key
     *
     * @return array
     */
    public function getInfo($key)
    {
        return  $this->_getOnlineStatisticsDao()->getInfo($key);
    }

    /**
     * 增加一条记录.
     *
     * @param string $key
     * @param int    $count
     *
     * @return bool
     */
    public function addInfo($key, $number = 0, $time = 0)
    {
        $number = (int) $number;
        $data = array(
            'signkey'      => $key,
            'number'       => $number,
            'created_time' => $time,
        );

        return $this->_getOnlineStatisticsDao()->addInfo($data);
    }

    /**
     * 删除一条记录.
     *
     * @param string $key
     */
    public function deleteInfo($key)
    {
        return  $this->_getOnlineStatisticsDao()->deleteInfo($key);
    }

    private function _getOnlineStatisticsDao()
    {
        return Wekit::loadDao('online.dao.PwOnlineStatisticsDao');
    }
}
