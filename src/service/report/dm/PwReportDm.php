<?php

defined('WEKIT_VERSION') || exit('Forbidden');
Wind::import('LIB:base.PwBaseDm');

/**
 * 举报DM
 *
 * @author jinlong.panjl <jinlong.panjl@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id$
 * @package wind
 */
class PwReportDm extends PwBaseDm
{
    public $id;

    public function __construct($id = 0)
    {
        $id = intval($id);
        $id > 0 && $this->id = $id;
    }

    /**
     * 设置类型type
     *
     * @param  int        $type
     * @return PwReportDm
     */
    public function setType($type)
    {
        $this->_data['type'] = intval($type);

        return $this;
    }

    /**
     * 设置类型typeid
     *
     * @param  int        $typeId
     * @return PwReportDm
     */
    public function setTypeId($typeId)
    {
        $this->_data['type_id'] = intval($typeId);

        return $this;
    }

    /**
     * 设置内容content
     *
     * @param  string     $content
     * @return PwReportDm
     */
    public function setContent($content)
    {
        $content = trim($content);
        $this->_data['content'] = $content;

        return $this;
    }

    /**
     * 设置内容链接
     *
     * @param  string     $contentUrl
     * @return PwReportDm
     */
    public function setContentUrl($contentUrl)
    {
        $contentUrl = trim($contentUrl);
        $this->_data['content_url'] = $contentUrl;

        return $this;
    }

    /**
     * 设置作者
     *
     * @param  int        $author_userid
     * @return PwReportDm
     */
    public function setAuthorUserid($authorUserid)
    {
        $this->_data['author_userid'] = intval($authorUserid);

        return $this;
    }

    /**
     * 设置举报人
     *
     * @param  string     $created_userid
     * @return PwReportDm
     */
    public function setCreatedUserid($created_userid)
    {
        $this->_data['created_userid'] = intval($created_userid);

        return $this;
    }

    /**
     * 设置举报事件
     *
     * @param  int        $created_time
     * @return PwReportDm
     */
    public function setCreatedTime($created_time)
    {
        $this->_data['created_time'] = intval($created_time);

        return $this;
    }

    /**
     * 设置举报原因
     *
     * @param  string     $reason
     * @return PwReportDm
     */
    public function setReason($reason)
    {
        $this->_data['reason'] = $reason;

        return $this;
    }

    /**
     * 设置是否处理
     *
     * @param  int        $ifcheck
     * @return PwReportDm
     */
    public function setIfcheck($ifcheck)
    {
        $this->_data['ifcheck'] = intval($ifcheck);

        return $this;
    }

    /**
     * 设置处理人
     *
     * @param  int        $operate_userid
     * @return PwReportDm
     */
    public function setOperateUserid($operate_userid)
    {
        $this->_data['operate_userid'] = intval($operate_userid);

        return $this;
    }

    /**
     * 设置处理时间
     *
     * @param  int        $operate_time
     * @return PwReportDm
     */
    public function setOperateTime($operate_time)
    {
        $this->_data['operate_time'] = intval($operate_time);

        return $this;
    }

    protected function _beforeAdd()
    {
        return true;
    }

    protected function _beforeUpdate()
    {
        return true;
    }
}
