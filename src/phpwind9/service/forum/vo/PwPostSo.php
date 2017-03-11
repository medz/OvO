<?php

defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 回复搜索条件.
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwPostSo.php 13278 2012-07-05 02:08:39Z jieyin $
 */
class PwPostSo
{
    protected $_data = array();
    protected $_orderby = array();

    public function getData()
    {
        return $this->_data;
    }

    public function getOrderby()
    {
        return $this->_orderby;
    }

    /**
     * 搜索回复标题.
     */
    public function setKeywordOfTitle($keyword)
    {
        $this->_data['title_keyword'] = $keyword;

        return $this;
    }

    /**
     * 搜索回复内容.
     */
    public function setKeywordOfContent($keyword)
    {
        $this->_data['content_keyword'] = $keyword;

        return $this;
    }

    /**
     * 搜索回复标题或内容.
     */
    public function setKeywordOfTitleOrContent($keyword)
    {
        $this->_data['title_and_content_keyword'] = $keyword;

        return $this;
    }

    /**
     * 帖子是否可用.
     */
    public function setDisabled($disabled)
    {
        $this->_data['disabled'] = $disabled;

        return $this;
    }

    /**
     * 搜索版块.
     *
     * @param mixed $fid int|array
     */
    public function setFid($fid)
    {
        $this->_data['fid'] = $fid;

        return $this;
    }

    /**
     * 搜索版块.
     *
     * @param mixed $tid int|array
     */
    public function setTid($tid)
    {
        $this->_data['tid'] = $tid;

        return $this;
    }

    /**
     * 搜索作者.
     */
    public function setAuthor($author)
    {
        $user = Wekit::load('user.PwUser')->getUserByName($author);
        $this->setAuthorId($user ? $user['uid'] : 0);

        return $this;
    }

    /**
     * 搜索作者.
     *
     * @param mixed $authorid int|array
     */
    public function setAuthorId($authorid)
    {
        $this->_data['created_userid'] = $authorid;

        return $this;
    }

    /**
     * 发帖时间区间，起始.
     */
    public function setCreateTimeStart($time)
    {
        $this->_data['created_time_start'] = $time;

        return $this;
    }

    /**
     * 发帖时间区间，结束
     */
    public function setCreateTimeEnd($time)
    {
        $this->_data['created_time_end'] = $time + 86400;

        return $this;
    }

    public function setCreatedIp($ip)
    {
        $this->_data['created_ip'] = $ip;

        return $this;
    }

    public function orderbyCreatedTime($asc)
    {
        $this->_orderby['created_time'] = (bool) $asc;

        return $this;
    }
}
