<?php

defined('WEKIT_VERSION') || exit('Forbidden');



/**
 * 获取用户(A)关注的指定用户列表的新鲜事
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @license http://www.phpwind.com
 * @version $Id: PwFetchAttentionFreshByUid.php 9241 2012-05-04 03:25:39Z jieyin $
 * @package attention
 */

class PwFetchAttentionFreshByUid implements iPwDataSource
{
    public $uid;
    public $uids;
    public $limit;
    public $offset;

    public function __construct($uid, $uids, $limit, $offset)
    {
        $this->uid = $uid;
        $this->uids = $uids;
        $this->limit = $limit;
        $this->offset = $offset;
    }

    public function getData()
    {
        return Wekit::load('attention.PwFresh')->fetchAttentionFreshByUid($this->uid, $this->uids, $this->limit, $this->offset);
    }
}
