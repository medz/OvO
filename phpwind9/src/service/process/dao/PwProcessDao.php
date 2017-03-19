<?php

Wind::import('SRC:library.base.PwBaseDao');
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>.
 *
 * @author $Author$ Foxsee@aliyun.com
 * @copyright  ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id$
 */
class PwProcessDao extends PwBaseDao
{
    protected $_table = 'common_process';
    protected $_dataStruct = ['flag', 'expired_time'];

    public function getProcess($flag)
    {
        $sql = $this->_bindTable('SELECT * FROM %s WHERE `flag`= ? ');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->getOne([$flag]);
    }

    public function replaceProcess($data)
    {
        if (!$data = $this->_filterStruct($data)) {
            return false;
        }
        if (!isset($data['flag'])) {
            return false;
        }
        $sql = $this->_bindSql('REPLACE INTO %s SET %s', $this->getTable(), $this->sqlSingle($data));

        return $this->getConnection()->execute($sql);
    }

    public function deleteProcess($flag)
    {
        $sql = $this->_bindTable('DELETE FROM %s WHERE `flag` = ? ');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->update([$flag]);
    }

    public function deleteProcessByTime($time)
    {
        $sql = $this->_bindTable('DELETE FROM %s WHERE `expired_time` < ? ');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->update([$time]);
    }
}
