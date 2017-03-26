<?php

defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 帖子删除扩展服务接口--删除帖子新鲜事.
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwDeleteTopicDoFreshDelete.php 17980 2012-09-10 12:36:27Z jieyin $
 */
class PwDeleteTopicDoFreshDelete extends iPwGleanDoHookProcess
{
    /*
    protected $recode = array();

    public function gleanData($value) {
        if ($value['fid'] == 0) {
            $this->recode[] = $value['tid'];
        }
    }*/

    public function run($ids)
    {
        Wekit::load('attention.PwFresh')->batchDeleteByType(PwFresh::TYPE_THREAD_TOPIC, $ids);

        //回复与新鲜事的关联
        if ($data = Wekit::load('attention.PwFreshIndex')->fetchByTid($ids)) {
            Wekit::load('attention.PwFresh')->batchDelete(array_keys($data));
        }
    }
}
