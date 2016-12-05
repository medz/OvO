<?php

Wind::import('SRC:library.base.PwBaseDao');
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>.
 *
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwDesignModuleDao.php 22555 2012-12-25 08:37:31Z gao.wanggao $
 */
class PwDesignModuleDao extends PwBaseDao
{
    protected $_pk = 'module_id';
    protected $_table = 'design_module';
    protected $_dataStruct = array('module_id', 'page_id', 'segment', 'module_struct', 'model_flag', 'module_name', 'module_property', 'module_title', 'module_style', 'module_compid', 'module_tpl', 'module_cache', 'isused',  'module_type');

    public function getModule($id)
    {
        return $this->_get($id);
    }

    public function fetchModule($ids)
    {
        return $this->_fetch($ids, 'module_id');
    }

    public function getByPageid($pageid)
    {
        $sql = $this->_bindTable('SELECT * FROM %s WHERE `page_id` = ? AND `isused` = 1');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll(array($pageid), 'module_id');
    }

    public function countModule($data)
    {
        list($where, $array) = $this->_buildCondition($data);
        $sql = $this->_bindSql('SELECT count(*) FROM %s %s', $this->getTable(), $where);
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->getValue($array);
    }

    public function searchModule($data, $orderdata, $offset, $limit)
    {
        list($where, $array) = $this->_buildCondition($data);
        $orderby = $this->_buildOrder($orderdata);
        $_limit = $limit ? $this->sqlLimit($limit, $offset) : '';
        $sql = $this->_bindSql('SELECT * FROM %s %s %s %s ', $this->getTable(), $where, $orderby, $_limit);
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll($array, 'module_id');
    }

    public function addModule($data)
    {
        return $this->_add($data, true);
    }

    public function updateModule($id, $data)
    {
        return $this->_update($id, $data);
    }

    public function batchUpdateIsUsed($ids)
    {
        $sql = $this->_bindSql('UPDATE %s SET `isused`=1 WHERE `module_id` IN %s', $this->getTable(), $this->sqlImplode($ids));

        return $this->getConnection()->execute($sql);
    }

    public function deleteModule($id)
    {
        return $this->_delete($id);
    }

    public function deleteByPageId($pageid)
    {
        $sql = $this->_bindTable('DELETE FROM %s WHERE `page_id` =? ');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->update(array($pageid));
    }

    private function _buildCondition($data)
    {
        $where = ' WHERE 1';
        $array = array();
        foreach ($data as $key => $value) {
            switch ($key) {
                case 'model_flag':
                    $where .= ' AND `model_flag` = ?';
                    $array[] = $value;
                    break;
                case 'module_id':
                    $where .= ' AND `module_id` = ?';
                    $array[] = $value;
                    break;
                case 'module_name':
                    $where .= ' AND `module_name` like ?';
                    $array[] = '%'.$value.'%';
                    break;
                case 'isused':
                    $where .= ' AND `isused` = ?';
                    $array[] = $value;
                    break;
                case 'module_type':
                    $where .= ' AND `module_type` & ?';
                    $array[] = $value;
                    break;
                case 'page_id':
                    $where .= ' AND `page_id` = ?';
                    $array[] = $value;
                    break;
            }
        }

        return array($where, $array);
    }

    private function _buildOrder($data)
    {
        $array = array();
        foreach ($data as $key => $value) {
            switch ($key) {
                case 'module_id':
                    $array[] = 'module_id '.($value ? 'ASC' : 'DESC');
                    break;
            }
        }

        return $array ? ' ORDER BY '.implode(',', $array) : '';
    }
}
