<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>.
 *
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwDesignPageDm.php 15075 2012-07-31 03:36:58Z gao.wanggao $
 */
class PwDesignPageDm extends PwBaseDm
{
    public $pageid;

    public function __construct($pageid = null)
    {
        if (isset($pageid)) {
            $this->pageid = (int) $pageid;
        }
    }

    public function setType($type)
    {
        $this->_data['page_type'] = (int) $type;

        return $this;
    }

    public function setName($name)
    {
        $this->_data['page_name'] = $name;

        return $this;
    }

    public function setRouter($router)
    {
        $this->_data['page_router'] = $router;

        return $this;
    }

    public function setUnique($unique)
    {
        $this->_data['page_unique'] = (int) $unique;

        return $this;
    }

    public function setIsUnique($isunique)
    {
        $this->_data['is_unique'] = (int) $isunique;

        return $this;
    }

    public function setModuleIds($array)
    {
        $_string = ',';
        foreach ($array as $v) {
            $_string .= $v.',';  //前后分隔符
        }
        $this->_data['module_ids'] = $_string;

        return $this;
    }

    public function setStrucNames($array)
    {
        $this->_data['struct_names'] = implode(',', $array);

        return $this;
    }

    public function setSegments($array)
    {
        $this->_data['segments'] = implode(',', $array);

        return $this;
    }

    public function setDesignLock($uid, $time)
    {
        $this->_data['design_lock'] = $uid.'|'.$time;

        return $this;
    }

    protected function _beforeAdd()
    {
        if (!$this->_data['page_router']) {
            return new PwError('fail');
        }

        return true;
    }

    protected function _beforeUpdate()
    {
        if ($this->pageid < 1) {
            return new PwError('fail');
        }

        return true;
    }
}
