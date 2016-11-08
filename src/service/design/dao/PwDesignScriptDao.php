<?php

Wind::import('SRC:library.base.PwBaseDao');
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwDesignScriptDao.php 16031 2012-08-17 08:18:58Z gao.wanggao $
 * @package
 */

class PwDesignScriptDao extends PwBaseDao
{
    protected $_pk = 'module_id';
    protected $_table = 'design_script';
    protected $_dataStruct = array('module_id', 'token', 'view_times');

    public function get($id)
    {
        return $this->_get($id);
    }

    public function add($data)
    {
        return $this->_add($data);
    }

    public function delete($id)
    {
        return $this->_delete($id);
    }
}
