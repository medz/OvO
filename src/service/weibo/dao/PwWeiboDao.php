<?php

/**
 * 新鲜事dao服务
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @license http://www.phpwind.com
 * @version $Id: PwWeiboDao.php 8959 2012-04-28 09:06:05Z jieyin $
 * @package forum
 */

class PwWeiboDao extends PwBaseDao
{
    protected $_table = 'weibo';
    protected $_pk = 'weibo_id';
    protected $_dataStruct = array('weibo_id', 'src_id', 'content', 'type', 'comments', 'extra', 'like_count', 'created_userid', 'created_username', 'created_time');

    public function getWeibo($weiboId)
    {
        return $this->_get($weiboId);
    }

    public function fetchWeibo($weiboIds)
    {
        return $this->_fetch($weiboIds, 'weibo_id');
    }

    public function addWeibo($fields)
    {
        return $this->_add($fields);
    }

    public function updateWeibo($weiboId, $fields, $increaseFields = array())
    {
        return $this->_update($weiboId, $fields, $increaseFields);
    }

    public function deleteWeibo($weiboId)
    {
        return $this->_delete($weiboId);
    }

    public function batchDeleteWeibo($weiboIds)
    {
        return $this->_batchDelete($weiboIds);
    }
}
