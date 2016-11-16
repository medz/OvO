<?php

defined('WEKIT_VERSION') || exit('Forbidden');



/**
 * 获取用户的新鲜事
 *
 * @author $Author: jieyin $ Foxsee@aliyun.com
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwFetchMyFresh.php 14777 2012-07-26 10:26:51Z jieyin $
 * @package attention
 */
class PwFetchMyFresh implements iPwDataSource
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
        return Wekit::load('attention.PwFresh')->getFreshByUid($this->uid, $this->limit, $this->offset);
    }
}
