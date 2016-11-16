<?php

defined('WEKIT_VERSION') || exit('Forbidden');



/**
 * 通过时间获取投票基础信息
 *
 * @author MingXing Sun <mingxing.sun@aliyun-inc.com>
 * @license http://www.phpwind.com
 * @version $Id: PwFetchPollByTime.php 5519 2012-01-12 07:13:36Z mingxing.sun $
 * @package poll
 */

class PwFetchPollByTime implements iPwDataSource
{
    public $time = 0;
    public $limit = 0;
    public $offset = 0;

    public function __construct($startTime, $endTime, $limit, $offset, $orderby)
    {
        $this->startTime = $startTime;
        $this->endTime = $endTime;

        $this->limit = $limit;
        $this->offset = $offset;
        $this->orderby = $orderby;
    }

    public function getData()
    {
        return Wekit::load('poll.PwPoll')->getPollByTime($this->startTime, $this->endTime, $this->limit, $this->offset, $this->orderby);
    }
}
