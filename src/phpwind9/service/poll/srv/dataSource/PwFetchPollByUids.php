<?php

defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 通过时间获取投票基础信息.
 *
 * @author MingXing Sun <mingxing.sun@aliyun-inc.com>
 * @license http://www.phpwind.com
 *
 * @version $Id: PwFetchPollByUids.php 5519 2012-01-12 07:13:36Z mingxing.sun $
 */
class PwFetchPollByUids implements iPwDataSource
{
    public $uids = [];
    public $limit = 0;
    public $offset = 0;

    public function __construct($uids, $limit, $offset)
    {
        $this->uids = $uids;
        $this->limit = $limit;
        $this->offset = $offset;
    }

    public function getData()
    {
        return Wekit::load('poll.PwPoll')->fetchPollByUid($this->uids, $this->limit, $this->offset);
    }
}
