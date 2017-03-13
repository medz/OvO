<?php

Wind::import('SRC:library.base.PwBaseDao');

/**
 * 草稿DAO.
 *
 * @author jinlong.panjl <jinlong.panjl@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id$
 */
class PwDraftDao extends PwBaseDao
{
    protected $_table = 'draft';
    protected $_dataStruct = ['id', 'created_userid', 'title', 'content', 'created_time'];

    /**
     * 添加.
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
     * 删除.
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
     * 修改.
     *
     * @param int   $id
     * @param array $data
     *
     * @return bool
     */
    public function update($id, $data)
    {
        return $this->_update($id, $data);
    }

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
     * 根据用户统计草稿箱数量.
     *
     * @param int $uid
     *
     * @return int
     */
    public function countByUid($uid)
    {
        $sql = $this->_bindTable('SELECT COUNT(*) FROM %s WHERE `created_userid`=?');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->getValue([$uid]);
    }

    /**
     * 根据用户获取$num条数据.
     *
     * @param int $uid
     * @param int $num
     *
     * @return array
     */
    public function getByUid($uid, $num)
    {
        $sql = $this->_bindSql('SELECT * FROM %s WHERE `created_userid`=? ORDER BY `id` DESC %s ', $this->getTable(), $this->sqlLimit($num));
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll([$uid]);
    }
}
