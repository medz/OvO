<?php

defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 帖子内容页回复列表数据接口.
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwReadDataSource.php 21659 2012-12-12 07:00:13Z xiaoxia.xuxx $
 */
abstract class PwReadDataSource
{
    public $page = 1;
    public $perpage = 20;
    public $maxpage = 1;
    public $total = 1;
    public $firstFloor;
    public $asc = true;

    protected $data = [];
    protected $urlArgs = [];
    protected $_uids = [];
    protected $_aids = [];

    public function setPage($page)
    {
        $this->page = $page;

        return $this;
    }

    public function setPerpage($perpage)
    {
        $perpage = intval($perpage);
        $perpage > 0 && $this->perpage = $perpage;

        return $this;
    }

    public function setDesc($desc)
    {
        $this->asc = !$desc;
        $desc && $this->urlArgs['desc'] = $desc;

        return $this;
    }

    abstract public function execute();

    public function &getData()
    {
        return $this->data;
    }

    public function getUser()
    {
        return $this->_uids;
    }

    public function getAttach()
    {
        return $this->_aids;
    }

    /**
     * 设置url参数.
     *
     * @param string $key
     * @param string $value
     */
    public function setUrlArg($key, $value)
    {
        $this->urlArgs[$key] = $value;
    }

    /**
     * 获取当前链接模式.
     *
     * @param string $except
     *
     * @return string
     */
    public function getUrlArgs($except = '')
    {
        $args = $this->urlArgs;
        if ($except && isset($args[$except])) {
            unset($args[$except]);
        }

        return $args;
// 		return $args ? '&' . http_build_query($args) : '';
    }
}
