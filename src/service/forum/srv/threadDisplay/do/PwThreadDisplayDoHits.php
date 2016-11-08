<?php

defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('SRV:forum.srv.threadDisplay.do.PwThreadDisplayDoBase');

/**
 * 点击率实时更新显示
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwThreadDisplayDoHits.php 23447 2013-01-09 12:01:09Z jieyin $
 * @package poll
 */

class PwThreadDisplayDoHits extends PwThreadDisplayDoBase
{
    public function __construct()
    {
    }

    public function bulidRead($read)
    {
        if ($read['lou'] == 0 && ($result = $this->_getThreadService()->getHit($read['tid']))) {
            $read['hits'] += $result['hits'];
        }

        return $read;
    }

    protected function _getThreadService()
    {
        return Wekit::load('forum.PwThread');
    }
}
