<?php

defined('WEKIT_VERSION') || exit('Forbidden');

 

/**
 * 微博公共服务
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwWeiboService.php 17151 2012-08-31 08:21:36Z jieyin $
 */
class PwWeiboService
{
    /**
     * 添加一条微博评论.
     *
     * @param object $dm PwWeiboCommnetDm
     *
     * @return bool|PwError
     */
    public function addComment(PwWeiboCommnetDm $dm, PwUserBo $user)
    {
        if (($result = $this->_getDs()->addComment($dm)) instanceof PwError) {
            return $result;
        }
         
        $weibo_id = $dm->getField('weibo_id');
        $dm1 = new PwWeiboDm($weibo_id);
        $dm1->addComments(1);
        $this->_getDs()->updateWeibo($dm1);

        PwSimpleHook::getInstance('weibo_addComment')->runDo($result, $dm, $user);

        return $result;
    }

    protected function _getDs()
    {
        return Wekit::load('weibo.PwWeibo');
    }
}
