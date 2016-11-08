<?php

Wind::import('SRC:library.base.PwBaseDao');

/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwDesignShieldDao.php 20146 2012-10-24 02:51:25Z gao.wanggao $
 * @package
 */

class PwDesignShieldDao extends PwBaseDao
{
    protected $_pk = 'shield_id';
    protected $_table = 'design_shield';
    protected $_dataStruct = array('shield_id', 'from_app', 'from_id', 'module_id', 'shield_title', 'shield_url');

    public function getShieldByModuleId($moduleid)
    {
        $sql = $this->_bindTable('SELECT * FROM %s WHERE `module_id` = ?');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll(array($moduleid));
    }

    public function fetchByFromidsAndApp($fromids, $fromapp)
    {
        $sql = $this->_bindSql('SELECT * FROM %s WHERE `from_id` IN %s AND `from_app` = ?', $this->getTable(), $this->sqlImplode($fromids));
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll(array($fromapp));
    }

    public function getShieldList($moduleid, $offset, $limit)
    {
        $where = '';
        $array = array();
        if ($moduleid) {
            $where = ' WHERE `module_id` = ?' ;
            $array = array($moduleid);
        }
        $sql = $this->_bindSql('SELECT * FROM %s %s ORDER BY shield_id DESC %s ', $this->getTable(), $where, $this->sqlLimit($limit, $offset));
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll($array);
    }

    public function countShield($moduleid)
    {
        $where = '';
        $array = array();
        if ($moduleid) {
            $where = ' WHERE `module_id` = ?' ;
            $array = array($moduleid);
        }
        $sql = $this->_bindSql('SELECT count(*) FROM %s %s ', $this->getTable(), $where);
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->getValue($array);
    }

    public function add($data)
    {
        return $this->_add($data);
    }

    public function delete($shieldid)
    {
        return $this->_delete($shieldid);
    }

    public function deleteByModuleId($moduleid)
    {
        $sql = $this->_bindTable('DELETE FROM %s WHERE `module_id` = ?');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->update(array($moduleid));
    }

    public function antiDelete($ids)
    {
        $sql = $this->_bindSql('DELETE FROM %s WHERE `shield_id` NOT IN %s', $this->getTable(), $this->sqlImplode($ids));
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->update(array());
    }
}
