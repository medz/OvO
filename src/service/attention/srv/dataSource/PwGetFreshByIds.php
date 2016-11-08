<?php

defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('LIB:dataSource.iPwDataSource');
/**
 * 获取多条新鲜事
 *
 * @author $Author$ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id$
 * @package attention
 */

class PwGetFreshByIds implements iPwDataSource
{
    public $ids = array();

    public function __construct($ids)
    {
        $this->ids = $ids;
    }

    public function getData()
    {
        return Wekit::load('attention.PwFresh')->fetchFresh($this->ids);
    }
}
