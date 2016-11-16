<?php

defined('WEKIT_VERSION') || exit('Forbidden');



/**
 * 微博数据模型
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @license http://www.phpwind.com
 * @version $Id: PwWeiboCommnetDm.php 5758 2012-03-10 07:50:25Z jieyin $
 * @package weibo
 */

class PwWeiboCommnetDm extends PwBaseDm
{
    protected $isTransmit;

    public function setWeiboId($id)
    {
        $this->_data['weibo_id'] = intval($id);

        return $this;
    }

    public function setContent($content)
    {
        $this->_data['content'] = $content;

        return $this;
    }

    public function setCreatedUser($uid, $username)
    {
        $this->_data['created_userid'] = $uid;
        $this->_data['created_username'] = $username;

        return $this;
    }

    public function setCreatedTime($time)
    {
        $this->_data['created_time'] = $time;

        return $this;
    }

    protected function _beforeAdd()
    {
        if (!isset($this->_data['weibo_id'])) {
            return new PwError('WEIBO:id.empty');
        }

        return true;
    }

    protected function _beforeUpdate()
    {
        return true;
    }
}
