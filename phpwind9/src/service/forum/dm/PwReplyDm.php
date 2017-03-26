<?php

defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 帖子数据模型(insert, update).
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwReplyDm.php 17954 2012-09-10 10:45:12Z jieyin $
 */
class PwReplyDm extends PwPostDm
{
    public $pid;

    public function __construct($pid = 0, PwForumBo $forum = null, PwUserBo $user = null)
    {
        parent::__construct($forum, $user);
        $this->pid = $pid;
    }

    public function setTid($tid)
    {
        $this->_data['tid'] = $tid;

        return $this;
    }

    public function setReplyPid($pid)
    {
        $this->_data['rpid'] = intval($pid);
    }

    public function setIfshield($ifshield)
    {
        $this->_data['ifshield'] = intval($ifshield);

        return $this;
    }

    public function setTopped($topped)
    {
        $this->_data['topped'] = intval($topped);

        return $this;
    }
}
