<?php

defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 回复的回复.
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwPostsReply.php 12035 2012-06-15 11:09:30Z jieyin $
 */
class PwPostsReply
{
    public function getPostByPid($pid, $limit = 20, $offset = 0)
    {
        if (empty($pid)) {
            return $pid;
        }

        return $this->_getDao()->getPostByPid($pid, $limit, $offset);
    }

    public function add($pid, $rpid)
    {
        return $this->_getDao()->add(array(
            'pid'  => $pid,
            'rpid' => $rpid,
        ));
    }

    protected function _getDao()
    {
        return Wekit::loadDao('forum.dao.PwPostsReplyDao');
    }
}
