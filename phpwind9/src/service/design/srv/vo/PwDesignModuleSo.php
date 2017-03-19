<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>.
 *
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwDesignModuleSo.php 20969 2012-11-22 09:37:48Z gao.wanggao $
 */
class PwDesignModuleSo
{
    protected $_data = [];
    protected $_order = [];

    public function getData()
    {
        return $this->_data;
    }

    public function getOrder()
    {
        return $this->_order;
    }

    public function setModuleId($id)
    {
        $this->_data['module_id'] = $id;
    }

    public function setModelFlag($flag)
    {
        $this->_data['model_flag'] = $flag;
    }

    public function setModuleName($name)
    {
        $this->_data['module_name'] = $name;
    }

    public function setPageId($pageid)
    {
        $this->_data['page_id'] = $pageid;
    }

    public function setIsUse($isuse)
    {
        $this->_data['isused'] = (int) $isuse;
    }

    public function setModuleType($type)
    {
        $this->_data['module_type'] = $type;
    }

    public function orderbyModuleId($asc)
    {
        $this->_order['module_id'] = (bool) $asc;
    }
}
