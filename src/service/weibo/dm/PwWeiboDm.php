<?php

defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 微博数据模型.
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @license http://www.phpwind.com
 *
 * @version $Id: PwWeiboDm.php 8487 2012-04-19 08:09:57Z gao.wanggao $
 */
class PwWeiboDm extends PwBaseDm
{
    public $weibo_id;

    public function __construct($weibo_id = 0)
    {
        $this->weibo_id = $weibo_id;
    }

    public function setContent($content)
    {
        $this->_data['content'] = $content;

        return $this;
    }

    public function setSrcId($src_id)
    {
        $this->_data['src_id'] = $src_id;

        return $this;
    }

    public function setLikeCount($count)
    {
        $this->_data['like_count'] = intval($count);

        return $this;
    }

    /**
     * 设置微博来源类型.
     *
     * @param int $type 必为 PwWeibo::TYPE_* 中的一种
     *
     * @return object $this;
     */
    public function setType($type)
    {
        $this->_data['type'] = intval($type);

        return $this;
    }

    public function addComments($num)
    {
        $this->_increaseData['comments'] = intval($num);

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
        return true;
    }

    protected function _beforeUpdate()
    {
        return true;
    }
}
