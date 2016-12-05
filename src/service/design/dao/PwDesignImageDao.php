<?php

Wind::import('SRC:library.base.PwBaseDao');
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>.
 *
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwDesignImageDao.php 22292 2012-12-21 05:09:20Z gao.wanggao $
 */
class PwDesignImageDao extends PwBaseDao
{
    protected $_pk = 'id';
    protected $_table = 'design_image';
    protected $_dataStruct = array('path', 'thumb', 'width', 'height', 'moduleid', 'data_id', 'sign', 'status');

    public function get($id)
    {
        return $this->_get($id);
    }

    public function fetch($ids)
    {
        return $this->_fetch($ids, 'id');
    }

    public function add($fields)
    {
        return $this->_add($fields, true);
    }

    public function update($id, $fields)
    {
        return $this->_update($id, $fields);
    }

    public function delete($id)
    {
        return $this->_delete($id);
    }

    public function batchDelete($ids)
    {
        return $this->_batchDelete($ids);
    }
}
