<?php

defined('WEKIT_VERSION') || exit('Forbidden');
Wind::import('LIB:dataSource.iPwDataSource');

/**
 * 通过时间获取投票基础信息
 *
 * @author MingXing Sun <mingxing.sun@aliyun-inc.com>
 * @license http://www.phpwind.com
 * @version $Id: PwFetchPollByUid.php 5519 2012-01-12 07:13:36Z mingxing.sun $
 * @package poll
 */

class PwFetchPollByUid implements iPwDataSource
{
    public $uid = 0;
    public $limit = 0;
    public $offset = 0;

    public function __construct($uid, $limit, $offset)
    {
        $this->uid = $uid;
        $this->limit = $limit;
        $this->offset = $offset;
    }

    public function getData()
    {
        return Wekit::load('poll.PwPoll')->getPollByUid($this->uid, $this->limit, $this->offset);
    }
}
