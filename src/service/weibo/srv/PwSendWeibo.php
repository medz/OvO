<?php

defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('SRV:weibo.PwWeibo');

/**
 * 微博发布服务
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwSendWeibo.php 5519 2012-03-06 07:13:36Z jieyin $
 */
class PwSendWeibo
{
    public $user;

    public function __construct(PwUserBo $user)
    {
        $this->user = $user;
    }

    public function check()
    {
        return true;
    }

    /**
     * 发布一条微博.
     *
     * @param object $dm PwWeiboDm
     *
     * @return bool|PwError
     */
    public function send(PwWeiboDm $dm)
    {
        if (($result = $this->check()) instanceof PwError) {
            return $result;
        }
        $dm->setCreatedUser($this->user->uid, $this->user->username);
        $dm->setCreatedTime(Pw::getTime());

        $weibo_id = $this->_getDs()->addWeibo($dm);
        $this->_getFresh()->send($this->user->uid, PwFresh::TYPE_WEIBO, $weibo_id);

        return $weibo_id;
    }

    protected function _getFresh()
    {
        return Wekit::load('attention.PwFresh');
    }

    protected function _getDs()
    {
        return Wekit::load('weibo.PwWeibo');
    }
}
