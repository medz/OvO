<?php

/**
 * 举报DAO.
 *
 * @author jinlong.panjl <jinlong.panjl@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id$
 */
class PwReportDao extends PwBaseDao
{
    protected $_table = 'report';
    protected $_dataStruct = array('id', 'type', 'type_id', 'content', 'content_url', 'author_userid', 'created_userid', 'created_time', 'reason', 'ifcheck', 'operate_userid', 'operate_time');
    protected $_pk = 'id';

    /**
     * 添加单条消息.
     *
     * @param array $fields
     *
     * @return bool
     */
    public function add($fields)
    {
        return $this->_add($fields);
    }

    /**
     * 删除单条
     *
     * @param int $id
     *
     * @return bool
     */
    public function delete($id)
    {
        return $this->_delete($id);
    }

    /**
     * 批量删除.
     *
     * @param array $ids
     *
     * @return bool
     */
    public function batchDelete($ids)
    {
        return $this->_batchDelete($ids);
    }

    /**
     * 更新单条
     *
     * @param int   $id
     * @param array $fields
     *
     * @return bool
     */
    public function update($id, $fields)
    {
        return $this->_update($id, $fields);
    }

    /**
     * 批量更新.
     *
     * @param array $ids
     * @param array $fields
     *
     * @return bool
     */
    public function batchUpdate($ids, $fields)
    {
        return $this->_batchUpdate($ids, $fields);
    }

    /**
     * 取一条
     *
     * @param int $id
     *
     * @return array
     */
    public function get($id)
    {
        return $this->_get($id);
    }

    /**
     * 批量取.
     *
     * @param array $ids
     *
     * @return array
     */
    public function fetch($ids)
    {
        return $this->_fetch($ids);
    }

    /**
     * 根据举报来源和是否处理统计数量.
     *
     * @param int $type
     * @param int $ifcheck
     *
     * @return array
     */
    public function countByType($ifcheck, $type)
    {
        $where = 'WHERE `ifcheck`=?';
        $parms = array($ifcheck);
        if ($type) {
            $where .= ' AND `type`=?';
            $parms[] = $type;
        }
        $sql = $this->_bindSql('SELECT COUNT(*) FROM %s %s ', $this->getTable(), $where);
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->getValue($parms);
    }

    /**
     * 根据举报来源和是否处理取列表.
     *
     * @param int $type
     * @param int $ifcheck
     *
     * @return array
     */
    public function getListByType($ifcheck, $type, $limit, $start)
    {
        $where = 'WHERE `ifcheck`=?';
        $parms = array($ifcheck);
        if ($type) {
            $where .= ' AND `type`=?';
            $parms[] = $type;
        }
        $sql = $this->_bindSql('SELECT * FROM %s %s ORDER BY `created_time` DESC %s ', $this->getTable(), $where, $this->sqlLimit($limit, $start));
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll($parms);
    }
}
