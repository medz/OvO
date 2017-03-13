<?php

defined('WEKIT_VERSION') || exit('Forbidden');
/**
 * 帖子dao服务
 */
class PwThreadMergeDao extends PwBaseDao
{
    protected $_table = 'bbs_threads';
    protected $_mergeTable = 'bbs_threads_content';
    protected $_dataStruct = [];

    public function countSearchThread($field)
    {
        list($where, $arg, $merge) = $this->_buildCondition($field);
        $_mergeTable = $merge ? $this->_bindTable(' LEFT JOIN %s b ON a.tid=b.tid', $this->getTable($this->_mergeTable)) : '';
        $sql = $this->_bindSql('SELECT COUNT(*) AS sum FROM %s a %s WHERE %s', $this->getTable(), $_mergeTable, $where);
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->getValue($arg);
    }

    public function searchThread($fetch, $field, $orderby, $limit, $offset)
    {
        list($where, $arg, $merge) = $this->_buildCondition($field);
        $order = $this->_buildOrderby($orderby);
        list($select, $merge) = $this->_buildFetch($fetch, $merge);
        $_mergeTable = $merge ? $this->_bindTable(' LEFT JOIN %s b ON a.tid=b.tid', $this->getTable($this->_mergeTable)) : '';
        $sql = $this->_bindSql('SELECT %s FROM %s a %s WHERE %s %s %s', $select, $this->getTable(), $_mergeTable, $where, $order, $this->sqlLimit($limit, $offset));
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll($arg, 'tid');
    }

    protected function _buildFetch($fetch, $merge)
    {
        $select = 'a.*';
        if (($fetch & 3) == 3) {
            $select = '*';
            $merge = 1;
        } elseif (($fetch & 3) == 2) {
            $select = 'b.*';
            $merge = 1;
        }

        return [$select, $merge];
    }

    protected function _buildCondition($field)
    {
        $merge = 0;
        $where = '1';
        $arg = [];
        foreach ($field as $key => $value) {
            switch ($key) {
                case 'tid':
                    $where .= ' AND tid '.$this->_sqlIn($value, $arg);
                    break;
                case 'fid':
                    $where .= ' AND a.fid '.$this->_sqlIn($value, $arg);
                    break;
                case 'nofid':
                    $where .= ' AND a.fid  '.$this->_sqlNoIn($value, $arg);
                    break;
                case 'topic_type':
                    $where .= ' AND a.topic_type'.$this->_sqlIn($value, $arg);
                    break;
                case 'disabled':
                    $where .= ' AND a.disabled=?';
                    $arg[] = $value;
                    break;
                case 'created_userid':
                    $where .= ' AND a.created_userid '.$this->_sqlIn($value, $arg);
                    break;
                case 'title_keyword':
                    $where .= ' AND a.subject LIKE ?';
                    $arg[] = "%$value%";
                    break;
                case 'content_keyword':
                    $where .= ' AND b.content LIKE ?';
                    $arg[] = "%$value%";
                    $merge = 1;
                    break;
                case 'title_and_content_keyword':
                    $where .= ' AND (a.subject LIKE ? OR b.content LIKE ?)';
                    $arg[] = "%$value%";
                    $arg[] = "%$value%";
                    $merge = 1;
                    break;
                case 'created_time_start':
                    $where .= ' AND a.created_time>?';
                    $arg[] = $value;
                    break;
                case 'created_time_end':
                    $where .= ' AND a.created_time<?';
                    $arg[] = $value;
                    break;
                case 'lastpost_time_start':
                    $where .= ' AND lastpost_time > ?';
                    $arg[] = $value;
                    break;
                case 'lastpost_time_end':
                    $where .= ' AND lastpost_time < ?';
                    $arg[] = $value;
                    break;
                case 'digest':
                    $where .= ' AND a.digest=?';
                    $arg[] = $value;
                    break;
                case 'hasimage':
                    $where .= ' AND a.ifupload&1='.intval($value);
                    break;
                case 'special':
                    $where .= ' AND special '.$this->_sqlIn($value, $arg);
                    break;
                case 'topped':
                    $where .= ' AND topped '.$this->_sqlIn($value, $arg);
                    break;
                case 'hits_start':
                    $where .= ' AND a.hits>?';
                    $arg[] = $value;
                    break;
                case 'hits_end':
                    $where .= ' AND a.hits<?';
                    $arg[] = $value;
                    break;
                case 'replies_start':
                    $where .= ' AND a.replies>?';
                    $arg[] = $value;
                    break;
                case 'replies_end':
                    $where .= ' AND a.replies<?';
                    $arg[] = $value;
                    break;
                case 'created_ip':
                    $where .= ' AND a.created_ip LIKE ?';
                    $arg[] = "$value%";
                    break;
            }
        }

        return [$where, $arg, $merge];
    }

    protected function _buildOrderby($orderby)
    {
        $array = [];
        foreach ($orderby as $key => $value) {
            switch ($key) {
                case 'created_time':
                    $array[] = 'a.created_time '.($value ? 'ASC' : 'DESC');
                    break;
                case 'lastpost_time':
                    $array[] = 'lastpost_time '.($value ? 'ASC' : 'DESC');
                    break;
                case 'replies':
                    $array[] = 'replies '.($value ? 'ASC' : 'DESC');
                    break;
                case 'hits':
                    $array[] = 'hits '.($value ? 'ASC' : 'DESC');
                    break;
                case 'like':
                    $array[] = 'like_count '.($value ? 'ASC' : 'DESC');
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

    protected function _sqlNoIn($value, &$arg)
    {
        if (is_array($value)) {
            return ' NOT IN '.$this->sqlImplode($value);
        }
        $arg[] = $value;

        return ' !=? ';
    }
}
