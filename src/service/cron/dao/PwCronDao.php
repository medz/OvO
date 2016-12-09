<?php

Wind::import('SRC:library.base.PwBaseDao');
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>.
 *
 * @author $Author$ Foxsee@aliyun.com
 * @copyright  ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id$
 */
class PwCronDao extends PwBaseDao
{
    protected $_pk = 'cron_id';
    protected $_table = 'common_cron';
    protected $_dataStruct = array('cron_id', 'subject', 'loop_type', 'loop_daytime', 'cron_file', 'isopen', 'created_time', 'modified_time', 'next_time');

    public function getCron($cronId)
    {
        return $this->_get($cronId);
    }

    public function getCronByFile($file)
    {
        $sql = $this->_bindTable('SELECT * FROM %s WHERE `cron_file` = ? ');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->getOne(array($file));
    }

    public function fetchCron($cronIds)
    {
        return $this->_fetch($cronIds, 'cron_id');
    }

    public function getFirstCron()
    {
        $sql = $this->_bindTable('SELECT * FROM %s WHERE `isopen` >= 1  ORDER BY `next_time` ASC');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->getOne(array());
    }

    public function getList($isopen = null)
    {
        $array = array();
        $where = '';
        if (isset($isopen)) {
            $array[] = $isopen;
            $where = ' WHERE isopen = ?';
        }
        $sql = $this->_bindSql('SELECT * FROM %s %s  ORDER BY `isopen` DESC , `next_time` ASC', $this->getTable(), $where);
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll($array);
    }

    public function addCron($data)
    {
        return $this->_add($data, false);
    }

    public function updateCron($cronId, $data)
    {
        return $this->_update($cronId, $data);
    }

    public function deleteCron($cronId)
    {
        return $this->_delete($cronId);
    }
}
