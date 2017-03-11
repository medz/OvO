<?php

Wind::import('SRC:library.base.PwBaseDm');
/**
 * 导航数据模型.
 *
 * @author $Author: gao.wanggao $
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwNavDm.php 24004 2013-01-18 06:18:11Z gao.wanggao $
 */
class PwNavDm extends PwBaseDm
{
    public $navid;

    public function __construct($navid = null)
    {
        if (isset($navid)) {
            $this->navid = (int) $navid;
        }
    }

    public function setParentid($parentid)
    {
        $this->_data['parentid'] = intval($parentid);

        return $this;
    }

    public function setRootid($rootid)
    {
        $this->_data['rootid'] = intval($rootid);

        return $this;
    }

    public function setType($type)
    {
        $this->_data['type'] = $type;

        return $this;
    }

    /**
     * TODO 根据链接来获取参数，需要确定重写规则后修改
     * Enter description here ...
     *
     * @param unknown_type $link
     */
    public function setSign($router)
    {
        $sign = '';
        if (is_array($router)) {
            $allow = array('m', 'c', 'a', 'tid', 'fid', 'id', 'uid', 'username', 'tab');
            foreach ($router as $k => $v) {
                if (!in_array($k, $allow)) {
                    continue;
                }
                if (!$v) {
                    continue;
                }
                $sign .= $v.'|';
            }
        } else {
            $sign = $router;
        }
        $this->_data['sign'] = $sign;

        return $this;
    }

    public function setName($name)
    {
        $this->_data['name'] = $name;

        return $this;
    }

    public function setLink($link)
    {
        $this->_data['link'] = $link;

        return $this;
    }

    public function setStyle($color, $bold, $italic, $underline)
    {
        $this->_data['style'] = $color.'|'.$bold.'|'.$italic.'|'.$underline;

        return $this;
    }

    public function setAlt($alt)
    {
        $this->_data['alt'] = $alt;

        return $this;
    }

    public function setImage($image)
    {
        $this->_data['image'] = $image;

        return $this;
    }

    public function setTarget($target)
    {
        $this->_data['target'] = intval($target);

        return $this;
    }

    public function setIsshow($isshow)
    {
        $this->_data['isshow'] = intval($isshow);

        return $this;
    }

    public function setOrderid($orderid)
    {
        $this->_data['orderid'] = intval($orderid);

        return $this;
    }

    /**
     * 用于缓存批量子父导航新添加关系绑定.
     *
     * @param string $tempid
     */
    public function setTempid($tempid)
    {
        $this->_data['tempid'] = $tempid;

        return $this;
    }

    protected function _beforeUpdate()
    {
        if ($this->navid < 1) {
            return new PwError('ADMIN:nav.add.fail.empty.navid');
        }

        return true;
    }

    protected function _beforeAdd()
    {
        return true;
    }
}
