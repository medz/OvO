<?php

Wind::import('SRC:library.base.PwBaseDao');
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>.
 *
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwDesignPermissionsDao.php 17399 2012-09-05 07:12:51Z gao.wanggao $
 */
class PwDesignPermissionsDao extends PwBaseDao
{
    protected $_pk = 'id';
    protected $_table = 'design_permissions';
    protected $_dataStruct = array('id', 'design_type', 'design_id', 'uid', 'permissions');

    public function get($id)
    {
        return $this->_get($id);
    }

    public function search($data)
    {
        list($where, $array) = $this->_buildCondition($data);
        $sql = $this->_bindSql('SELECT * FROM %s %s', $this->getTable(), $where);
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll($array, 'id');
    }

    public function add($data)
    {
        if (!$data = $this->_filterStruct($data)) {
            return false;
        }
        $sql = $this->_bindSql('INSERT INTO %s SET %s', $this->getTable(), $this->sqlSingle($data));

        return $this->getConnection()->execute($sql);
    }

    public function updatePermissions($id, $permissions)
    {
        $sql = $this->_bindTable('UPDATE %s SET `permissions` = ? WHERE `id` = ? ');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->update(array($permissions, $id));
    }

    public function delete($id)
    {
        return $this->_delete($id);
    }

    public function batchDelete($ids)
    {
        return $this->_batchDelete($ids);
    }

    public function deleteByTypeAndDesignId($type, $id)
    {
        $sql = $this->_bindTable('DELETE FROM %s WHERE `design_type` = ? AND `design_id` = ?');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->update(array($type, $id));
    }

    private function _buildCondition($data)
    {
        $where = ' WHERE 1';
        $array = array();
        foreach ($data as $key => $value) {
            switch ($key) {
                case 'design_id':
                    $value = !is_array($value) && $value ? array($value) : $value;
                    $where .= ' AND design_id IN '.$this->sqlImplode($value);
                    break;
                case 'design_type':
                    $where .= ' AND design_type = ?';
                    $array[] = $value;
                    break;
                case 'uid':
                    $where .= ' AND uid = ?';
                    $array[] = $value;
                    break;
            }
        }

        return array($where, $array);
    }
}
