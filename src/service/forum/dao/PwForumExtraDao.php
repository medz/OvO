<?php

defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 版块扩展信息dao服务
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @license http://www.phpwind.com
 * @version $Id: PwForumExtraDao.php 8617 2012-04-21 09:26:52Z jieyin $
 * @package forum
 */

class PwForumExtraDao extends PwBaseDao
{
    protected $_table = 'bbs_forum_extra';
    protected $_pk = 'fid';
    protected $_dataStruct = array('fid', 'seo_title', 'seo_description', 'seo_keywords', 'settings_basic', 'settings_credit');
    protected $_defaultBaseInstance = 'forum.dao.PwForumBaseDao';

    public function getForum($fid)
    {
        if (!$result = $this->getBaseInstance()->getForum($fid)) {
            return $result;
        }
        if ($ret = $this->getForumExtra($fid)) {
            $result = array_merge($result, $ret);
        }

        return $result;
    }

    public function getForumExtra($fid)
    {
        return $this->_get($fid);
    }

    public function fetchForum($fids)
    {
        if (!$result = $this->getBaseInstance()->fetchForum($fids)) {
            return $result;
        }

        return $this->_margeArray($result, $this->_fetch($fids, 'fid'));
    }

    public function getForumList()
    {
        if (!$result = $this->getBaseInstance()->getForumList()) {
            return $result;
        }

        return $this->_margeArray($result, $this->_fetch(array_keys($result), 'fid'));
    }

    public function getCommonForumList()
    {
        if (!$result = $this->getBaseInstance()->getCommonForumList()) {
            return $result;
        }

        return $this->_margeArray($result, $this->_fetch(array_keys($result), 'fid'));
    }

    public function addForum($fields)
    {
        if (!$fid = $this->getBaseInstance()->addForum($fields)) {
            return false;
        }
        $fields['fid'] = $fid;
        $this->_add($fields, false);

        return $fid;
    }

    public function updateForum($fid, $fields, $increaseFields = array())
    {
        $result = $this->getBaseInstance()->updateForum($fid, $fields, $increaseFields);
        $this->_update($fid, $fields, $increaseFields);

        return $result;
    }

    public function batchUpdateForum($fids, $fields, $increaseFields = array())
    {
        $result = $this->getBaseInstance()->batchUpdateForum($fids, $fields, $increaseFields);
        $this->_batchUpdate($fids, $fields, $increaseFields);

        return $result;
    }

    public function deleteForum($fid)
    {
        if (!$this->getBaseInstance()->deleteForum($fid)) {
            return false;
        }

        return $this->_delete($fid);
    }
}
