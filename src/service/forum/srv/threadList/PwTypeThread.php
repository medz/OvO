<?php

defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 帖子列表数据接口 / 主题分类.
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwTypeThread.php 16394 2012-08-23 06:28:06Z long.shi $
 */
class PwTypeThread extends PwThreadDataSource
{
    public $fid;
    public $type;

    public function __construct($fid, $type)
    {
        $this->fid = $fid;
        $this->type = $type;
        $this->urlArgs['type'] = $type;
    }

    public function getTotal()
    {
        return $this->_getThreadDs()->countThreadByFidAndType($this->fid, $this->type);
    }

    public function getData($limit, $offset)
    {
        return $this->_getThreadDs()->getThreadByFidAndType($this->fid, $this->type, $limit, $offset);
    }

    protected function _getThreadDs()
    {
        return Wekit::load('forum.PwThread');
    }
}
