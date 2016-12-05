<?php

/**
 * 新鲜事与帖子关系索引 dao服务
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @license http://www.phpwind.com
 *
 * @version $Id: PwFreshIndexDao.php 16249 2012-08-21 09:01:24Z jieyin $
 */
class PwFreshIndexDao extends PwBaseDao
{
    protected $_table = 'attention_fresh_index';
    protected $_dataStruct = array('fresh_id', 'tid');

    public function getByTid($tid)
    {
        $sql = $this->_bindTable('SELECT * FROM %s WHERE tid=?');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll(array($tid), 'fresh_id');
    }

    public function fetchByTid($tids)
    {
        $sql = $this->_bindSql('SELECT * FROM %s WHERE tid IN %s', $this->getTable(), $this->sqlImplode($tids));
        $rst = $this->getConnection()->query($sql);

        return $rst->fetchAll('fresh_id');
    }

    public function add($fields)
    {
        return $this->_add($fields, false);
    }
}
