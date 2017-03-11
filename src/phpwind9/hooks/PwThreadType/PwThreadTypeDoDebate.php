<?php

defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 帖子删除扩展服务接口--虚拟删除到回收站.
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwThreadTypeDoDebate.php 10060 2012-05-16 06:51:05Z jieyin $
 */
class PwThreadTypeDoDebate
{
    public function getTtype($tType)
    {
        $tType['4'] = array('辩论帖', '发起辩论', true);

        return $tType;
    }
}
