<?php

defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 话题搜索DAO.
 *
 * @author jinlong.panjl <jinlong.panjl@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id$
 */
class PwTagSearchDao extends PwBaseDao
{
    protected $_table = 'tag';
    protected $_table_relation = 'tag_category_relation';
    protected $_dataStruct = [];

    public function countSearchTag($field)
    {
        list($where, $arg, $join) = $this->_buildCondition($field);
        $sql = $this->_bindSql('SELECT * FROM %s AS a %s %s', $this->getTable(), $join, $where);
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->getValue($arg);
    }

    public function searchTag($field, $orderby, $limit, $offset)
    {
        list($where, $arg, $join) = $this->_buildCondition($field);
        $order = $this->_buildOrderby($orderby);
        $sql = $this->_bindSql('SELECT * FROM %s AS a %s %s %s %s', $this->getTable(), $join, $where, $order, $this->sqlLimit($limit, $offset));
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll($arg, 'tag_id');
    }

    protected function _buildCondition($field)
    {
        $join = '';
        $where = 'WHERE 1';
        $arg = [];
        foreach ($field as $key => $value) {
            switch ($key) {
                case 'tag_id':
                    $where .= ' AND a.tag_id'.$this->_sqlIn($value, $arg);
                    break;
                case 'category_id':
                    $where .= ' AND b.category_id=?';
                    $join = sprintf('LEFT JOIN %s AS b USING(`tag_id`) ', $this->getTable($this->_table_relation));
                    $arg[] = $value;
                    break;
                case 'iflogo':
                    $where .= ' AND a.iflogo=?';
                    $arg[] = $value;
                    break;
                case 'ifhot':
                    $where .= ' AND a.`ifhot` =?';
                    $arg[] = $value;
                    break;
            }
        }

        return [$where, $arg, $join];
    }

    protected function _buildOrderby($orderby)
    {
        $array = [];
        foreach ($orderby as $key => $value) {
            switch ($key) {
                case 'attention_count':
                    $array[] = 'a.attention_count '.($value ? 'ASC' : 'DESC');
                    break;
                case 'content_count':
                    $array[] = 'a.content_count '.($value ? 'ASC' : 'DESC');
                    break;
                case 'created_time':
                    $array[] = 'a.created_time '.($value ? 'ASC' : 'DESC');
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
