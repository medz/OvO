<?php

defined('WEKIT_VERSION') || exit('Forbidden');



/**
 * 帖子删除扩展服务接口--虚拟删除到回收站
 *
 * @author zhangpeihong@aliyun.com
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwDeleteTopicDoSpecialDelete.php 22514 2012-12-25 06:12:19Z jieyin $
 * @package forum
 */

class PwDeleteTopicDoSpecialDelete extends iPwGleanDoHookProcess
{
    protected $record = array();

    public function run($tids)
    {
        Wekit::load('forum.PwSpecialSort')->batchDeleteSpecialSortByTid($tids);
    }
}
