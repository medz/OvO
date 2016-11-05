<?php

defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('SRV:forum.srv.post.do.PwPostDoBase');
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author $Author$ Foxsee@aliyun.com
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id$
 * @package
 */


class PwLikeDoReply extends PwPostDoBase
{
    public $likeid;

    public function __construct($likeid)
    {
        $this->likeid = $likeid;
    }

    public function addPost($pid, $tid)
    {
        if ($pid < 1 && $tid < 1) {
            return false;
        }

        return  Wekit::load('like.PwLikeContent')->updateLastPid($this->likeid, $pid);
    }
}
