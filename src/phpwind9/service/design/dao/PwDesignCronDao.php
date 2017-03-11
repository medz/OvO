<?php

Wind::import('SRC:library.base.PwBaseDao');
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>.
 *
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwDesignCronDao.php 10783 2012-05-30 05:25:30Z gao.wanggao $
 */
class PwDesignCronDao extends PwBaseDao
{
    protected $_pk = 'module_id';
    protected $_table = 'design_cron';
    protected $_dataStruct = array('module_id', 'created_time');

    public function get($id)
    {
        return $this->_get($id);
    }

    public function fetch($ids)
    {
        return $this->_fetch($ids, 'module_id');
    }

    public function add($data)
    {
        return $this->_add($data);
    }

    public function batchAdd($data)
    {
        foreach ($data as $v) {
            $_data[] = array($v['module_id'], $v['created_time']);
        }
        $sql = $this->_bindSql('INSERT INTO %s (`module_id`, `created_time`) VALUES %s ', $this->getTable(), $this->sqlMulti($_data));

        return $this->getConnection()->execute($sql);
    }

    public function getAllCron()
    {
        $sql = $this->_bindTable('SELECT * FROM %s');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll(array());
    }

    public function delete($id)
    {
        return $this->_delete($id);
    }
}
