<?php

defined('WEKIT_VERSION') || exit('Forbidden');

 

/**
 * 点击率实时更新显示.
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwThreadListDoHits.php 23447 2013-01-09 12:01:09Z jieyin $
 */
class PwThreadListDoHits extends PwThreadListDoBase
{
    protected $_data = array();

    public function __construct()
    {
    }

    public function initData($threaddb)
    {
        $tids = Pw::collectByKey($threaddb, 'tid');
        $this->_data = Wekit::load('forum.PwThread')->fetchHit($tids);
    }

    public function bulidThread($thread)
    {
        if (isset($this->_data[$thread['tid']])) {
            $thread['hits'] += $this->_data[$thread['tid']]['hits'];
        }

        return $thread;
    }
}
