<?php

Wind::import('SRC:library.base.PwBaseDao');
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>.
 *
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwLikeContentDao.php 8487 2012-04-19 08:09:57Z gao.wanggao $
 */
class PwLikeContentDao extends PwBaseDao
{
    protected $_table = 'like_content';
    protected $_dataStruct = ['likeid', 'typeid', 'fromid', 'isspecial', 'users', 'reply_pid'];

    public function getInfo($likeid)
    {
        $sql = $this->_bindTable('SELECT * FROM %s WHERE likeid = ? ');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->getOne([$likeid]);
    }

    public function fetchInfo($likeids)
    {
        $sql = $this->_bindSql('SELECT * FROM %s WHERE likeid IN %s', $this->getTable(), $this->sqlImplode($likeids));
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll([], 'likeid');
    }

    public function getInfoByTypeidFromid($typeid, $fromid)
    {
        $sql = $this->_bindTable('SELECT * FROM %s WHERE typeid = ? AND fromid = ? ');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->getOne([$typeid, $fromid]);
    }

    public function addInfo($data)
    {
        if (! $data = $this->_filterStruct($data)) {
            return false;
        }
        $sql = $this->_bindSql('INSERT INTO %s SET %s', $this->getTable(), $this->sqlSingle($data));
        $this->getConnection()->execute($sql);

        return $this->getConnection()->lastInsertId();
    }

    public function updateInfo($likeid, $data)
    {
        if (! $data = $this->_filterStruct($data)) {
            return false;
        }
        $sql = $this->_bindSql('UPDATE %s SET %s  WHERE likeid = ? ', $this->getTable(), $this->sqlSingle($data));
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->update([$likeid]);
    }

    public function deleteInfo($likeid)
    {
        $sql = $this->_bindTable('DELETE FROM %s  WHERE likeid = ? ');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->update([$likeid]);
    }
}
