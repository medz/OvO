<?php

Wind::import('SRC:library.base.PwBaseDao');
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>.
 *
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwSpaceDao.php 6219 2012-03-19 08:39:40Z gao.wanggao $
 */
class PwSpaceDao extends PwBaseDao
{
    protected $_table = 'space';
    protected $_dataStruct = ['uid', 'space_name', 'space_descrip', 'space_domain', 'space_style', 'back_image', 'visit_count', 'space_privacy', 'visitors', 'tovisitors'];

    public function getSpace($uid)
    {
        $sql = $this->_bindTable('SELECT * FROM %s WHERE uid = ? ');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->getOne([$uid]);
    }

    public function fetchSpace($uids)
    {
        $sql = $this->_bindSql('SELECT * FROM %s WHERE uid IN %s', $this->getTable(), $this->sqlImplode($uids));
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll([], 'uid');
    }

    public function getSpaceByDomain($domain)
    {
        $sql = $this->_bindTable('SELECT * FROM %s WHERE space_domain = ? LIMIT 1');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->getOne([$domain]);
    }

    public function addInfo($data)
    {
        if (! $data = $this->_filterStruct($data)) {
            return false;
        }
        $sql = $this->_bindSql('INSERT INTO %s SET %s', $this->getTable(), $this->sqlSingle($data));

        return $this->getConnection()->execute($sql);
    }

    public function updateInfo($uid, $data)
    {
        if (! $data = $this->_filterStruct($data)) {
            return false;
        }
        $sql = $this->_bindSql('UPDATE %s SET %s WHERE uid = ?', $this->getTable(), $this->sqlSingle($data));
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->update([$uid]);
    }

    public function updateNumber($uid)
    {
        $sql = $this->_bindSql('UPDATE %s SET %s WHERE uid = ?', $this->getTable(), $this->sqlSingleIncrease(['visit_count' => 1]));
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->update([$uid]);
    }

    public function deleteInfo($uid)
    {
        $sql = $this->_bindTable('DELETE FROM %s  WHERE uid = ? ');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->update([$uid]);
    }
}
