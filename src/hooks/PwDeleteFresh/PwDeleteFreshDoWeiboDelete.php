<?php

defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 新鲜事删除扩展服务接口--删除微博源内容.
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwDeleteFreshDoWeiboDelete.php 21189 2012-11-30 04:00:34Z xiaoxia.xuxx $
 */
class PwDeleteFreshDoWeiboDelete extends iPwGleanDoHookProcess
{
    public $record = array();

    public function gleanData($value)
    {
        if ($value['type'] == PwFresh::TYPE_WEIBO) {
            $this->record[] = $value['src_id'];
        }
    }

    public function run($ids)
    {
        if ($this->record) {
             
             
            $srv = new PwDeleteWeibo(new PwFetchWeiboById($this->record), $this->srv->user);
            $srv->execute();

            //微博产生的新鲜事删除，日志记录为“删除新鲜事”类型的日志
            Wekit::load('log.srv.PwLogService')->addDeleteFreshLog($this->srv->user, $srv->data, '');
        }
    }
}
