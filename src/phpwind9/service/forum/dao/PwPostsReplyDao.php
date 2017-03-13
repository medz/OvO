<?php

/**
 * 帖子索引dao服务
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwPostsReplyDao.php 13302 2012-07-05 03:45:43Z jieyin $
 */
class PwPostsReplyDao extends PwBaseDao
{
    protected $_table = 'bbs_posts_reply';
    protected $_mergeTable = 'bbs_posts';
    protected $_pk = 'pid';
    protected $_dataStruct = ['pid', 'rpid'];

    public function getPostByPid($pid, $limit, $offset)
    {
        $sql = $this->_bindSql('SELECT b.* FROM %s a LEFT JOIN %s b ON a.pid=b.pid WHERE a.rpid=? AND b.disabled=0 ORDER BY a.pid DESC %s', $this->getTable(), $this->getTable($this->_mergeTable), $this->sqlLimit($limit, $offset));
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll([$pid], 'pid');
    }

    public function add($fields)
    {
        if (!$fields = $this->_filterStruct($fields)) {
            return false;
        }
        $sql = $this->_bindSql('REPLACE INTO %s SET %s', $this->getTable(), $this->sqlSingle($fields));

        return $this->getConnection()->execute($sql);
    }
}
