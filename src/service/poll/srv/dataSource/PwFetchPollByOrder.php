<?php

defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 不同排序的投票.
 *
 * @author Mingqu Luo<luo.mingqu@gmail.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 *
 * @version $Id$
 */
class PwFetchPollByOrder implements iPwDataSource
{
    public $limit = 0;
    public $offset = 0;
    public $orderby = array();

    public function __construct($limit, $offset, $orderby)
    {
        $this->limit = $limit;
        $this->offset = $offset;
        $this->orderby = $orderby;
    }

    public function getData()
    {
        return Wekit::load('poll.PwPoll')->getPollList($this->limit, $this->offset, $this->orderby);
    }
}
