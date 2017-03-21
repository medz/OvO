<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>.
 *
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwOnlineStatisticsDao.php 8074 2012-04-16 03:04:30Z gao.wanggao $
 */
class PwOnlineStatisticsDao extends PwBaseDao
{
    protected $_table = 'online_statistics';
    protected $_dataStruct = ['signkey', 'number', 'created_time'];

    public function getInfo($key)
    {
        $sql = $this->_bindTable('SELECT * FROM %s WHERE signkey = ? ');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->getOne([$key]);
    }

    public function addInfo($data)
    {
        if (! $data = $this->_filterStruct($data)) {
            return false;
        }
        $sql = $this->_bindSql('REPLACE INTO %s SET %s ', $this->getTable(), $this->sqlSingle($data));

        return $this->getConnection()->execute($sql);
    }

    public function deleteInfo($key)
    {
        $sql = $this->_bindTable('DELETE FROM %s  WHERE signkey = ? ');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->update([$key]);
    }
}
