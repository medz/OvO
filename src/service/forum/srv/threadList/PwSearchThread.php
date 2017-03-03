<?php

defined('WEKIT_VERSION') || exit('Forbidden');

 

/**
 * 帖子列表数据接口 / 特殊列表.
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwSearchThread.php 23245 2013-01-07 07:42:41Z jieyin $
 */
class PwSearchThread extends PwThreadDataSource
{
    protected $so;
    protected $forum;

    public function __construct($forum)
    {
         
        $this->forum = $forum;
        $this->so = new PwThreadSo();
        $this->so->setFid($forum->fid)->setDisabled(0);
    }

    public function setType($type, $subtype = array())
    {
        $this->so->setTopicType($subtype ? array_merge(array($type), $subtype) : $type);
        $this->urlArgs['type'] = $type;
    }

    public function setOrderby($order)
    {
        if ($order == 'postdate') {
            $this->so->orderbyCreatedTime(0);
        } else {
            $this->so->orderbyLastPostTime(0);
        }
    }

    public function getTotal()
    {
        $_tmp = $this->so->getData();
        if (count($_tmp) == 2) {
            return $this->forum->foruminfo['threads'];
        }

        return $this->_getThreadDs()->countSearchThread($this->so);
    }

    public function getData($limit, $offset)
    {
        return $this->_getThreadDs()->searchThread($this->so, $limit, $offset);
    }

    protected function _getThreadDs()
    {
        return Wekit::load('forum.PwThread');
    }
}
