<?php

Wind::import('SRC:library.base.PwBaseDao');

/**
 * 搜索记录.
 *
 * @author jinlong.panjl <jinlong.panjl@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id$
 */
class App_SearchRecordDao extends PwBaseDao
{
    protected $_table = 'app_search_record';
    protected $_dataStruct = ['id', 'created_userid', 'created_time', 'search_type', 'keywords'];

    /**
     * 获取一条信息.
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
     * 单条添加.
     *
     * @param array $data
     *
     * @return bool
     */
    public function add($data)
    {
        return $this->_add($data);
    }

    /**
     * 单条添加.
     *
     * @param array $data
     *
     * @return bool
     */
    public function replace($data)
    {
        $sql = $this->_bindSql('REPLACE INTO %s SET %s', $this->getTable(), $this->sqlSingle($data));

        return $this->getConnection()->execute($sql);
    }

    /**
     * 单条删除.
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
     * 根据用户和类型删除.
     *
     * @param int $uid
     * @param int $type
     *
     * @return bool
     */
    public function deleteByUidAndType($uid, $type)
    {
        $sql = $this->_bindTable('DELETE FROM %s WHERE `created_userid`=? AND `search_type`=?');
        $smt = $this->getConnection()->createStatement($sql);
        $result = $smt->update([$uid, $type]);
    }

    /**
     * 根据时间删除.
     *
     * @param int $id
     *
     * @return bool
     */
    public function deleteByTime()
    {
        $sql = $this->_bindTable('DELETE FROM %s ORDER BY `created_time` ASC LIMIT 1');
        $smt = $this->getConnection()->createStatement($sql);
        $result = $smt->update([]);
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
     * 单条修改.
     *
     * @param int   $id
     * @param array $data
     *
     * @return bool
     */
    public function update($id, $fields, $increaseFields = [], $bitFields = [])
    {
        return $this->_update($id, $fields, $increaseFields, $bitFields);
    }

    /**
     * 根据用户统计数量.
     *
     * @param int $uid
     *
     * @return int
     */
    public function countByUidAndType($uid, $type)
    {
        $sql = $this->_bindTable('SELECT COUNT(*) FROM %s WHERE `created_userid`=? AND `search_type`=?');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->getValue([$uid, $type]);
    }

    /**
     * 根据用户获取数据.
     *
     * @param int $uid
     * @param int $type
     *
     * @return array
     */
    public function getByUidAndType($uid, $type)
    {
        $sql = $this->_bindTable('SELECT * FROM %s WHERE `created_userid`=? AND `search_type`=? ');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll([$uid, $type]);
    }

    public function alterAddLastSearch()
    {
        $sql = $this->_bindSql('ALTER TABLE %s ADD `last_search_time` INT(10) UNSIGNED NOT NULL DEFAULT 0', $this->getTable('user_data'));

        return $this->getConnection()->execute($sql);
    }

    public function alterDeleteLastSearch()
    {
        $sql = $this->_bindSql("DELETE FROM %s WHERE `rkey` LIKE '%s';", $this->getTable('user_permission_groups'), 'app_search%');
        $this->getConnection()->execute($sql);
        $sql = $this->_bindSql('ALTER TABLE %s DROP `last_search_time`', $this->getTable('user_data'));

        return $this->getConnection()->execute($sql);
    }
}
