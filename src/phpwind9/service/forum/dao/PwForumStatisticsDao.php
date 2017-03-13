<?php

defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 版块统计dao服务
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwForumStatisticsDao.php 13278 2012-07-05 02:08:39Z jieyin $
 */
class PwForumStatisticsDao extends PwBaseDao
{
    protected $_table = 'bbs_forum_statistics';
    protected $_pk = 'fid';
    protected $_dataStruct = ['fid', 'todayposts', 'todaythreads', 'article', 'posts', 'threads', 'subthreads', 'lastpost_info', 'lastpost_time', 'lastpost_username', 'lastpost_tid'];
    protected $_defaultBaseInstance = 'forum.dao.PwForumBaseDao';

    public function getForum($fid)
    {
        if (!$result = $this->getBaseInstance()->getForum($fid)) {
            return $result;
        }
        if ($ret = $this->getForumStatistics($fid)) {
            $result = array_merge($result, $ret);
        }

        return $result;
    }

    public function getForumStatistics($fid)
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

    public function updateForum($fid, $fields, $increaseFields = [])
    {
        $result = $this->getBaseInstance()->updateForum($fid, $fields, $increaseFields);
        $this->_update($fid, $fields, $increaseFields);

        return $result;
    }

    public function batchUpdateForum($fids, $fields, $increaseFields = [])
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

    public function updateForumStatistics($fid, $subFids)
    {
        if ($subFids) {
            $sql = $this->_bindSql('UPDATE %s a LEFT JOIN (SELECT %s as fid,sum(threads+subthreads) as subthreads,sum(article) as subarticle FROM %s WHERE fid IN %s) b on a.fid=b.fid SET a.article=a.threads+a.posts+b.subarticle,a.subthreads=b.subthreads WHERE a.fid=?', $this->getTable(), $fid, $this->getTable(), $this->sqlImplode($subFids));
        } else {
            $sql = $this->_bindTable('UPDATE %s SET article=threads+posts,subthreads=0 WHERE fid=?');
        }
        $smt = $this->getConnection()->createStatement($sql);
        $smt->update([$fid]);

        return true;
    }
}
