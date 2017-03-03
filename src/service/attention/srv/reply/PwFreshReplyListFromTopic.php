<?php

defined('WEKIT_VERSION') || exit('Forbidden');

 
 

/**
 * 新鲜事回复.
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwFreshReplyListFromTopic.php 13121 2012-07-02 03:47:00Z jieyin $
 */
class PwFreshReplyListFromTopic
{
    protected $tid;

    public function __construct($fresh)
    {
        $this->tid = $fresh['src_id'];
    }

    public function getReplies($limit, $offset)
    {
        return $this->_getThread()->getPostByTid($this->tid, $limit, $offset, false);
    }

    protected function _getThread()
    {
        return Wekit::load('forum.PwThread');
    }
}
