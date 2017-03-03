<?php

defined('WEKIT_VERSION') || exit('Forbidden');
 

/**
 * 帖子投票关系数据服务层
 *
 * @author MingXing Sun <mingxing.sun@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwThreadPollDm.php 9051 2012-05-03 01:57:24Z hejin $
 */
class PwThreadPollDm extends PwBaseDm
{
    /**
     * 设置帖子id.
     *
     * @param int $tid
     *
     * @return object
     */
    public function setTid($tid)
    {
        $this->_data['tid'] = intval($tid);

        return $this;
    }

    /**
     * 设置投票ID.
     *
     * @param int $pollid
     *
     * @return object
     */
    public function setPollid($pollid)
    {
        $this->_data['poll_id'] = intval($pollid);

        return $this;
    }

    /**
     * 设置投票的用户ID.
     *
     * @param int $userid
     *
     * @return object
     */
    public function setCreatedUserid($userid)
    {
        $this->_data['created_userid'] = intval($userid);

        return $this;
    }

    public function _beforeAdd()
    {
        return true;
    }

    public function _beforeUpdate()
    {
        return true;
    }
}
