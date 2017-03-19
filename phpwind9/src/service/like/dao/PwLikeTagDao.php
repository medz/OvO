<?php

Wind::import('SRC:library.base.PwBaseDao');
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>.
 *
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwLikeTagDao.php 5763 2012-03-10 09:04:11Z gao.wanggao $
 */
class PwLikeTagDao extends PwBaseDao
{
    protected $_table = 'like_tag';
    protected $_dataStruct = ['tagid', 'uid', 'tagname', 'number'];

    public function getInfo($tagid)
    {
        $sql = $this->_bindTable('SELECT * FROM %s WHERE tagid = ? ');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->getOne([$tagid]);
    }

    public function getInfoByTags($tagids)
    {
        $sql = $this->_bindSql('SELECT * FROM %s WHERE tagid IN  %s ', $this->getTable(), $this->sqlImplode($tagids));
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll([], 'tagid');
    }

    public function getInfoByUid($uid)
    {
        $sql = $this->_bindTable('SELECT * FROM %s WHERE uid = ? ');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll([$uid], 'tagid');
    }

    public function addInfo($data)
    {
        if (!$data = $this->_filterStruct($data)) {
            return false;
        }
        $sql = $this->_bindSql('INSERT INTO %s SET %s', $this->getTable(), $this->sqlSingle($data));
        $this->getConnection()->execute($sql);

        return $this->getConnection()->lastInsertId();
    }

    public function updateInfo($tagid, $data)
    {
        if (!$data = $this->_filterStruct($data)) {
            return false;
        }
        $sql = $this->_bindSql('UPDATE %s SET %s WHERE tagid = ?', $this->getTable(), $this->sqlSingle($data));
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->update([$tagid]);
    }

    public function updateNumber($tagid, $type = true)
    {
        $_array = $type ? ['number' => 1] : ['number' => -1];
        $sql = $this->_bindSql('UPDATE %s SET %s WHERE tagid = ?', $this->getTable(), $this->sqlSingleIncrease($_array));
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->update([$tagid]);
    }

    public function deleteInfo($tagid)
    {
        $sql = $this->_bindTable('DELETE FROM %s  WHERE tagid = ? ');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->update([$tagid]);
    }
}
