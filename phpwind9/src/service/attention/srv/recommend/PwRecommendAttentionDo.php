<?php

/**
 * Enter description here ...
 *
 * @author jinlong.panjl <jinlong.panjl@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id$
 */
class PwRecommendAttentionDo
{
    /**
     * 删除关注.
     *
     * @param int $uid
     * @param int $touid
     *
     * @return bool
     */
    public function delFollow($uid, $touid)
    {
        Wekit::load('attention.PwAttentionRecommendRecord')->deleteByUidAndSameUid($uid, $touid);

        return true;
    }
}
