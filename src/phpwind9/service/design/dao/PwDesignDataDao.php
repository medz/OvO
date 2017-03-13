<?php

Wind::import('SRC:library.base.PwBaseDao');
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>.
 *
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwDesignDataDao.php 19070 2012-10-10 08:19:50Z gao.wanggao $
 */
class PwDesignDataDao extends PwBaseDao
{
    protected $_pk = 'data_id';
    protected $_table = 'design_data';
    protected $_dataStruct = ['data_id', 'from_type', 'from_app', 'from_id', 'standard', 'module_id', 'style', 'extend_info', 'data_type', 'is_edited', 'is_reservation', 'vieworder', 'start_time', 'end_time'];

    public function getData($id)
    {
        return $this->_get($id);
    }

    public function fetchData($ids)
    {
        return $this->_fetch($ids, 'data_id');
    }

    public function getDataByModuleid($moduleid)
    {
        $sql = $this->_bindTable('SELECT * FROM %s WHERE `module_id` = ?  ORDER BY `vieworder` ASC , `data_id` DESC');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll([$moduleid], 'data_id');
    }

    public function fetchDataByFrom($fromids, $fromtype, $datatype)
    {
        $sql = $this->_bindSql('SELECT * FROM %s  WHERE `from_id` IN %s AND `from_type` = ?  AND `data_type` = ? ', $this->getTable(), $this->sqlImplode($fromids));
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll([$fromtype, $datatype], 'data_id');
    }

    public function fetchDataByModuleid($moduleids)
    {
        $sql = $this->_bindSql('SELECT * FROM %s  WHERE `module_id` IN %s ORDER BY `vieworder` ASC , `data_id` DESC', $this->getTable(), $this->sqlImplode($moduleids));
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll([], 'data_id');
    }

    public function searchData($data, $orderdata, $limit, $offset)
    {
        $sqlLimit = '';
        list($where, $array) = $this->_buildCondition($data);
        $orderby = $this->_buildOrder($orderdata);
        if ($limit > 0) {
            $sqlLimit = $this->sqlLimit($limit, $offset);
        }
        $sql = $this->_bindSql('SELECT * FROM %s %s %s %s ', $this->getTable(), $where, $orderby, $sqlLimit);
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll($array, 'data_id');
    }

    public function countData($data)
    {
        list($where, $array) = $this->_buildCondition($data);
        $sql = $this->_bindSql('SELECT COUNT(*) FROM %s %s ', $this->getTable(), $where);
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->getValue($array);
    }

    public function getMinDataIdByOrder($moduleid, $orderid)
    {
        $sql = $this->_bindTable('SELECT `data_id` FROM %s  WHERE `module_id` = ? AND `is_reservation` = 0  AND `vieworder` = ? ORDER BY `data_id` ASC LIMIT 1');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->getValue([$moduleid, $orderid]);
    }

    public function getMaxOrderDataId($moduleid, $dataType)
    {
        $sql = $this->_bindTable('SELECT `data_id` FROM %s  WHERE `module_id` = ? AND `data_type` = ? ORDER BY `vieworder` DESC , `data_id` ASC LIMIT 1');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->getValue([$moduleid, $dataType]);
    }

    public function getMaxOrder($moduleid)
    {
        $sql = $this->_bindTable('SELECT MAX(vieworder) AS max FROM %s WHERE module_id = ? ');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->getValue([$moduleid]);
    }

    public function addData($data)
    {
        return $this->_add($data, true);
    }

    public function updateData($id, $data)
    {
        return $this->_update($id, $data);
    }

    public function updateFixedToAuto($moduleid, $order)
    {
        $sql = $this->_bindTable('UPDATE %s SET `data_type`= 1 WHERE `module_id` = ? AND `vieworder` =? AND `data_type`= 2');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->update([$moduleid, $order]);
    }

    public function deleteData($id)
    {
        return $this->_delete($id);
    }

    public function batchDelete($ids)
    {
        return $this->_batchDelete($ids);
    }

    public function deleteByModuleId($moduleid)
    {
        $sql = $this->_bindTable('DELETE FROM %s WHERE `module_id` =? ');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->update([$moduleid]);
    }

    private function _buildCondition($data)
    {
        $where = ' WHERE 1';
        $array = [];
        foreach ($data as $key => $value) {
            switch ($key) {
                case 'from_type':
                    $where .= ' AND from_type = ?';
                    $array[] = $value;
                    break;
                case 'from_id':
                    $where .= ' AND from_id = ?';
                    $array[] = $value;
                    break;
                case 'module_id':
                    $value = !is_array($value) && $value ? [$value] : $value;
                    $where .= ' AND module_id IN '.$this->sqlImplode($value);
                    break;
                case 'data_type':
                    $where .= ' AND data_type = ?';
                    $array[] = $value;
                    break;
                case 'end_time':
                    $where .= ' AND end_time < ?';
                    $array[] = $value;
                    break;
                case 'is_reservation':
                    $where .= ' AND is_reservation = ?';
                    $array[] = $value;
                    break;
                case 'vieworder':
                    $where .= ' AND vieworder = ?';
                    $array[] = $value;
                    break;
            }
        }

        return [$where, $array];
    }

    private function _buildOrder($data)
    {
        $array = [];
        foreach ($data as $key => $value) {
            switch ($key) {
                case 'vieworder':
                    $array[] = 'vieworder '.($value ? 'ASC' : 'DESC');
                    break;
                case 'data_id':
                    $array[] = 'data_id '.($value ? 'ASC' : 'DESC');
                    break;
            }
        }

        return $array ? ' ORDER BY '.implode(',', $array) : '';
    }
}
