<?php

Wind::import('SRC:library.base.PwBaseDao');

/**
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwUserOnlineDao.php 17060 2012-08-31 01:50:31Z gao.wanggao $
 */
class PwUserOnlineDao extends PwBaseDao
{
    protected $_table = 'online_user';
    protected $_dataStruct = ['uid', 'username', 'modify_time', 'created_time', 'tid', 'fid', 'gid', 'request'];

    public function getInfo($uid)
    {
        $sql = $this->_bindTable('SELECT * FROM %s WHERE uid = ? ');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->getOne([$uid]);
    }

    public function fetchInfo($uids)
    {
        $sql = $this->_bindSql('SELECT * FROM %s WHERE uid IN %s ', $this->getTable(), $this->sqlImplode($uids));
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll([], 'uid');
    }

    public function getInfoList($fid, $offset, $limit)
    {
        $where = ($fid > 0) ? ' WHERE fid = ? ' : '';
        $sql = $this->_bindSql('SELECT * FROM %s %s ORDER BY created_time DESC %s ', $this->getTable(), $where, $this->sqlLimit($limit, $offset));
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll([$fid], 'uid');
    }

    public function replaceInfo($data)
    {
        if (! $data = $this->_filterStruct($data)) {
            return false;
        }
        if ($data['uid'] < 1) {
            return false;
        }
        $sql = $this->_bindSql('REPLACE INTO %s SET %s ', $this->getTable(), $this->sqlSingle($data));

        return $this->getConnection()->execute($sql);
    }

    public function deleteInfo($uid)
    {
        $sql = $this->_bindTable('DELETE FROM %s  WHERE uid = ?');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->update([$uid]);
    }

    public function deleteInfos($uids)
    {
        $sql = $this->_bindSql('DELETE FROM %s  WHERE uid IN %s ', $this->getTable(), $this->sqlImplode($uids));
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->update([]);
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
        $_array = [];
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
