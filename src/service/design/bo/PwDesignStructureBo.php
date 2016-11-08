<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwDesignStructureBo.php 11275 2012-06-05 08:25:47Z gao.wanggao $
 * @package
 */
class PwDesignStructureBo
{
    public $name;
    private $_structure = array();

    public function __construct($name)
    {
        $this->name = $name;
        $this->_setStructure();
    }

    public function getStructure()
    {
        return $this->_structure;
    }

    public function getName()
    {
        return isset($this->_structure['struct_name']) ? $this->_structure['struct_name'] : '';
    }

    public function getTitle()
    {
        return empty($this->_structure['struct_title']) ? array() : unserialize($this->_structure['struct_title']);
    }

    public function getStyle()
    {
        return empty($this->_structure['struct_style']) ? array() : unserialize($this->_structure['struct_style']);
    }

    private function _setStructure()
    {
        $this->_structure = Wekit::load('design.PwDesignStructure')->getStruct($this->name);
    }
}
