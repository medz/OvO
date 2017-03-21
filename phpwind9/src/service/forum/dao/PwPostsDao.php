<?php

/**
 * 帖子基础dao服务
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwPostsDao.php 24251 2013-01-23 09:07:13Z jinlong.panjl $
 */
class PwPostsDao extends PwBaseDao
{
    protected $_table = 'bbs_posts';
    protected $_pk = 'pid';
    protected $_dataStruct = ['pid', 'fid', 'tid', 'disabled', 'ischeck', 'ifshield', 'replies', 'useubb', 'aids', 'rpid', 'subject', 'content', 'like_count', 'sell_count', 'created_time', 'created_username', 'created_userid', 'created_ip', 'reply_notice', 'modified_time', 'modified_username', 'modified_userid', 'modified_ip', 'reminds', 'word_version', 'ipfrom', 'manage_remind', 'topped', 'app_mark'];

    public function getPost($pid)
    {
        return $this->_get($pid);
    }

    public function fetchPost($pids)
    {
        return $this->_fetch($pids, 'pid');
    }

    public function getPostByTid($tid, $limit, $offset, $asc)
    {
        $orderby = $asc ? 'ASC' : 'DESC';
        $sql = $this->_bindSql('SELECT * FROM %s WHERE tid=? AND disabled=0 ORDER BY created_time %s %s', $this->getTable(), $orderby, $this->sqlLimit($limit, $offset));
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll([$tid], 'pid');
    }

    public function countPostByUid($uid)
    {
        $sql = $this->_bindTable('SELECT COUNT(*) FROM %s WHERE created_userid=? AND disabled=0');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->getValue([$uid]);
    }

    public function getPostByUid($uid, $limit, $offset)
    {
        $sql = $this->_bindSql('SELECT * FROM %s WHERE created_userid=? AND disabled=0 ORDER BY created_time DESC %s', $this->getTable(), $this->sqlLimit($limit, $offset));
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll([$uid], 'pid');
    }

    public function countPostByTidAndUid($tid, $uid)
    {
        $sql = $this->_bindTable('SELECT COUNT(*) FROM %s WHERE tid=? AND created_userid=? AND disabled=0');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->getValue([$tid, $uid]);
    }

    public function countPostByTidUnderPid($tid, $pid)
    {
        $sql = $this->_bindTable('SELECT COUNT(*) FROM %s WHERE tid=? AND pid<? AND disabled=0');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->getValue([$tid, $pid]);
    }

    public function getPostByTidAndUid($tid, $uid, $limit, $offset, $asc)
    {
        $orderby = $asc ? 'ASC' : 'DESC';
        $sql = $this->_bindSql('SELECT * FROM %s WHERE tid=? AND disabled=0 AND created_userid=? ORDER BY created_time %s %s', $this->getTable(), $orderby, $this->sqlLimit($limit, $offset));
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll([$tid, $uid], 'pid');
    }

    public function addPost($fields)
    {
        return $this->_add($fields);
    }

    public function updatePost($pid, $fields, $increaseFields = [])
    {
        $this->_update($pid, $fields, $increaseFields);
    }

    public function batchUpdatePost($pids, $fields, $increaseFields = [])
    {
        return $this->_batchUpdate($pids, $fields, $increaseFields);
    }

    public function batchUpdatePostByTid($tids, $fields, $increaseFields = [])
    {
        $fields = $this->_filterStruct($fields);
        $increaseFields = $this->_filterStruct($increaseFields);
        if (! $fields && ! $increaseFields) {
            return false;
        }
        $sql = $this->_bindSql('UPDATE %s SET %s WHERE tid IN %s', $this->getTable(), $this->sqlMerge($fields, $increaseFields), $this->sqlImplode($tids));
        $this->getConnection()->execute($sql);

        return true;
    }

    public function revertPost($tids)
    {
        $sql = $this->_bindSql('UPDATE %s SET disabled=ischeck^1 WHERE tid IN %s', $this->getTable(), $this->sqlImplode($tids));

        return $this->getConnection()->execute($sql);
    }

    public function batchDeletePost($pids)
    {
        return $this->_batchDelete($pids);
    }

    public function batchDeletePostByTid($tids)
    {
        $sql = $this->_bindSql('DELETE FROM %s WHERE tid IN %s', $this->getTable(), $this->sqlImplode($tids));
        $this->getConnection()->execute($sql);

        return true;
    }

    /**************** 以下是搜索 *******************\
    \**************** 以下是搜索 *******************/

    public function countSearchPost($field)
    {
        list($where, $arg) = $this->_buildCondition($field);
        $sql = $this->_bindSql('SELECT COUNT(*) AS sum FROM %s WHERE %s', $this->getTable(), $where);
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->getValue($arg);
    }

    public function searchPost($field, $orderby, $limit, $offset)
    {
        list($where, $arg) = $this->_buildCondition($field);
        $order = $this->_buildOrderby($orderby);
        $sql = $this->_bindSql('SELECT * FROM %s WHERE %s %s %s', $this->getTable(), $where, $order, $this->sqlLimit($limit, $offset));
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll($arg, 'pid');
    }

    protected function _buildCondition($field)
    {
        $where = '1';
        $arg = [];
        foreach ($field as $key => $value) {
            switch ($key) {
                case 'fid':
                    $where .= ' AND fid'.$this->_sqlIn($value, $arg);
                    break;
                case 'tid':
                    $where .= ' AND tid'.$this->_sqlIn($value, $arg);
                    break;
                case 'disabled':
                    $where .= ' AND disabled=?';
                    $arg[] = $value;
                    break;
                case 'created_userid':
                    $where .= ' AND created_userid'.$this->_sqlIn($value, $arg);
                    break;
                case 'title_keyword':
                    $where .= ' AND subject LIKE ?';
                    $arg[] = "%$value%";
                    break;
                case 'content_keyword':
                    $where .= ' AND content LIKE ?';
                    $arg[] = "%$value%";
                    break;
                case 'title_and_content_keyword':
                    $where .= ' AND (subject LIKE ? OR content LIKE ?)';
                    $arg[] = "%$value%";
                    $arg[] = "%$value%";
                    break;
                case 'created_time_start':
                    $where .= ' AND created_time>?';
                    $arg[] = $value;
                    break;
                case 'created_time_end':
                    $where .= ' AND created_time<?';
                    $arg[] = $value;
                    break;
                case 'created_ip':
                    $where .= ' AND a.created_ip LIKE ?';
                    $arg[] = "$value%";
                    break;
            }
        }

        return [$where, $arg];
    }

    protected function _buildOrderby($orderby)
    {
        $array = [];
        foreach ($orderby as $key => $value) {
            switch ($key) {
                case 'created_time':
                    $array[] = 'created_time '.($value ? 'ASC' : 'DESC');
                    break;
            }
        }

        return $array ? ' ORDER BY '.implode(',', $array) : '';
    }

    protected function _sqlIn($value, &$arg)
    {
        if (is_array($value)) {
            return ' IN '.$this->sqlImplode($value);
        }
        $arg[] = $value;

        return '=?';
    }
}
