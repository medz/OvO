<?php

Wind::import('SRC:library.base.PwBaseDao');
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>.
 *
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwDesignPushDao.php 17721 2012-09-08 07:45:19Z gao.wanggao $
 */
class PwDesignPushDao extends PwBaseDao
{
    protected $_pk = 'push_id';
    protected $_table = 'design_push';
    protected $_dataStruct = ['push_id', 'push_from_id', 'push_from_model', 'module_id', 'push_standard', 'push_style', 'push_orderid', 'push_extend', 'created_userid', 'author_uid', 'status', 'neednotice', 'check_uid', 'created_time', 'start_time', 'end_time', 'checked_time'];

    public function getPush($id)
    {
        return $this->_get($id);
    }

    public function fetchPush($ids)
    {
        return $this->_fetch($ids, 'push_id');
    }

    public function searchPush($data, $orderdata, $limit, $offset)
    {
        $sqlLimit = '';
        list($where, $array) = $this->_buildCondition($data);
        $orderby = $this->_buildOrder($orderdata);
        if ($limit > 0) {
            $sqlLimit = $this->sqlLimit($limit, $offset);
        }
        $sql = $this->_bindSql('SELECT * FROM %s %s %s %s ', $this->getTable(), $where, $orderby, $sqlLimit);
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll($array, 'push_id');
    }

    public function countPush($data)
    {
        list($where, $array) = $this->_buildCondition($data);
        $sql = $this->_bindSql('SELECT COUNT(*) FROM %s %s ', $this->getTable(), $where);
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->getValue($array);
    }

    public function addPush($data)
    {
        return $this->_add($data, true);
    }

    public function updatePush($id, $data)
    {
        return $this->_update($id, $data);
    }

    public function updateAutoByModuleAndOrder($moduleid, $order)
    {
        $sql = $this->_bindTable('UPDATE %s SET `push_orderid`= 0 WHERE `module_id` = ? AND `push_orderid` =?');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->update([$moduleid, $order]);
    }

    public function deletePush($id)
    {
        return $this->_delete($id);
    }

    public function deleteByModuleId($moduleid)
    {
        $sql = $this->_bindTable('DELETE FROM %s WHERE `module_id` = ?');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->update([$moduleid]);
    }

    public function batchDelete($ids)
    {
        return $this->_batchDelete($ids);
    }

    private function _buildCondition($data)
    {
        $where = ' WHERE 1';
        $array = [];
        foreach ($data as $key => $value) {
            switch ($key) {
                case 'module_id':
                    $value = !is_array($value) && $value ? [$value] : $value;
                    $where .= ' AND module_id IN '.$this->sqlImplode($value);
                    break;
                case 'created_userid':
                    $where .= ' AND created_userid = ?';
                    $array[] = $value;
                    break;
                case 'lt_end_time':
                    $where .= ' AND (end_time < ? OR end_time != 0)';
                    $array[] = $value;
                    break;
                case 'gt_end_time':
                    $where .= ' AND (end_time >= ? OR  end_time = 0) ';
                    $array[] = $value;
                    break;
                case 'status':
                    $where .= ' AND status = ?';
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
                case 'push_id':
                    $array[] = 'push_id '.($value ? 'ASC' : 'DESC');
                    break;
            }
        }

        return $array ? ' ORDER BY '.implode(',', $array) : '';
    }
}
