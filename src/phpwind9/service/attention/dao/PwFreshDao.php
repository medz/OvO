<?php

/**
 * 新鲜事dao服务
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @license http://www.phpwind.com
 *
 * @version $Id: PwFreshDao.php 16249 2012-08-21 09:01:24Z jieyin $
 */
class PwFreshDao extends PwBaseDao
{
    protected $_table = 'attention_fresh';
    protected $_dataStruct = array('id', 'type', 'src_id', 'created_userid', 'created_time');

    public function getFresh($id)
    {
        return $this->_get($id);
    }

    public function fetchFresh($ids)
    {
        return $this->_fetch($ids, 'id');
    }

    public function countFreshByUid($uid)
    {
        $sql = $this->_bindTable('SELECT COUNT(*) AS sum FROM %s WHERE created_userid=?');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->getValue(array($uid));
    }

    public function getFreshByUid($uid, $limit, $offset)
    {
        $sql = $this->_bindSql('SELECT * FROM %s WHERE created_userid=? ORDER BY created_time DESC %s', $this->getTable(), $this->sqlLimit($limit, $offset));
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll(array($uid), 'id');
    }

    public function getFreshByType($type, $srcId)
    {
        $sql = $this->_bindSql('SELECT * FROM %s WHERE type=? AND src_id IN %s', $this->getTable(), $this->sqlImplode($srcId));
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll(array($type), 'id');
    }

    public function addFresh($fields)
    {
        return $this->_add($fields);
    }

    public function batchDelete($ids)
    {
        return $this->_batchDelete($ids);
    }
}
