<?php

Wind::import('SRC:library.base.PwBaseDao');
/**
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwGuestOnlineDao.php 17070 2012-08-31 02:23:44Z gao.wanggao $
 */
class PwGuestOnlineDao extends PwBaseDao
{
    protected $_table = 'online_guest';
    protected $_dataStruct = ['ip', 'created_time', 'modify_time', 'tid', 'fid', 'request'];

    public function getInfo($ip, $createdTime)
    {
        $sql = $this->_bindTable('SELECT * FROM %s WHERE ip = ? AND created_time = ? ');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->getOne([$uid, $createdTime]);
    }

    public function fetchInfo($ip)
    {
        $sql = $this->_bindTable('SELECT * FROM %s WHERE ip = ? ');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll([$ip]);
    }

    public function replaceInfo($data)
    {
        if (!$data = $this->_filterStruct($data)) {
            return false;
        }
        if (!$data['ip'] || !$data['created_time']) {
            return false;
        }
        $sql = $this->_bindSql('REPLACE INTO %s SET %s ', $this->getTable(), $this->sqlSingle($data));

        return $this->getConnection()->execute($sql);
    }

    public function deleteInfo($ip, $createdTime)
    {
        $sql = $this->_bindTable('DELETE FROM %s  WHERE ip = ? AND created_time = ? ');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->update([$uid, $createdTime]);
    }

    public function deleteInfos($ip)
    {
        $sql = $this->_bindTable('SELECT * FROM %s WHERE ip = ? ');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->update([$ip]);
    }

    public function deleteByLastTime($lasttime)
    {
        $sql = $this->_bindTable('DELETE FROM %s  WHERE modify_time < ? ');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->update([$lasttime]);
    }

    public function getOnlineCount($fid, $tid)
    {
        $where = 'WHERE 1';
        $_array = [$type];
        if ($fid > 0) {
            $where .= ' AND fid = ? ';
            $_array[] = $fid;
        }
        if ($tid > 0) {
            $where .= ' AND tid = ? ';
            $_array[] = $tid;
        }
        $sql = $this->_bindSql('SELECT COUNT(*) AS count FROM %s %s ', $this->getTable(), $where);
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->getValue($_array);
    }
}
