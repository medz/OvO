<?php

Wind::import('SRC:library.base.PwBaseDao');
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>.
 *
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwDesignComponentDao.php 21926 2012-12-17 06:08:05Z gao.wanggao $
 */
class PwDesignComponentDao extends PwBaseDao
{
    protected $_pk = 'comp_id';
    protected $_table = 'design_component';
    protected $_dataStruct = array('comp_id', 'model_flag', 'comp_name', 'comp_tpl', 'sys_id');

    public function getComponent($id)
    {
        return $this->_get($id);
    }

    public function getComponentByFlag($flag)
    {
        $sql = $this->_bindTable('SELECT * FROM %s WHERE model_flag = ? ORDER BY comp_id ASC');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll(array($flag));
    }

    public function getMaxSysid()
    {
        $sql = $this->_bindTable('SELECT MAX(sys_id) AS max FROM %s');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->getValue(array());
    }

    public function countComponent($data)
    {
        list($where, $array) = $this->_buildCondition($data);
        $sql = $this->_bindSql('SELECT count(*) FROM %s %s', $this->getTable(), $where);
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->getValue($array);
    }

    public function searchComponent($data, $offset, $limit)
    {
        list($where, $array) = $this->_buildCondition($data);
        $sql = $this->_bindSql('SELECT * FROM %s %s %s ', $this->getTable(), $where, $this->sqlLimit($limit, $offset));
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll($array, 'comp_id');
    }

    public function addComponent($data)
    {
        return $this->_add($data, false);
    }

    public function updateComponent($id, $data)
    {
        return $this->_update($id, $data);
    }

    public function deleteComponent($id)
    {
        return $this->_delete($id);
    }

    private function _buildCondition($data)
    {
        $where = ' WHERE 1';
        $array = array();
        foreach ($data as $key => $value) {
            switch ($key) {
                case 'model_flag':
                    $where .= ' AND model_flag = ?';
                    $array[] = $value;
                    break;
                case 'comp_id':
                    $where .= ' AND comp_id = ?';
                    $array[] = $value;
                    break;
                case 'comp_name':
                    $where .= ' AND comp_name like ?';
                    $array[] = '%'.$value.'%';
                    break;
            }
        }

        return array($where, $array);
    }
}
