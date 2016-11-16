<?php

defined('WEKIT_VERSION') || exit('Forbidden');



/**
 * 获取我关注的新鲜事
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @license http://www.phpwind.com
 * @version $Id: PwFetchAttentionFresh.php 14776 2012-07-26 10:25:06Z jieyin $
 * @package attention
 */

class PwFetchAttentionFresh implements iPwDataSource
{
    public $uid;
    public $limit;
    public $offset;

    public function __construct($uid, $limit, $offset)
    {
        $this->uid = $uid;
        $this->limit = $limit;
        $this->offset = $offset;
    }

    public function getData()
    {
        return Wekit::load('attention.PwFresh')->getAttentionFresh($this->uid, $this->limit, $this->offset);
    }
}
