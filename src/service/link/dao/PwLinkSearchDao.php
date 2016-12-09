<?php

defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 链接搜索DAO.
 *
 * @author jinlong.panjl <jinlong.panjl@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id$
 */
class PwLinkSearchDao extends PwBaseDao
{
    protected $_table = 'link';
    protected $_table_relation = 'link_relations';
    protected $_dataStruct = array();

    public function countSearchLink($field)
    {
        list($where, $arg, $join) = $this->_buildCondition($field);
        $sql = $this->_bindSql('SELECT * FROM %s AS a %s %s', $this->getTable(), $join, $where);
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->getValue($arg);
    }

    public function searchLink($field, $orderby, $limit, $offset)
    {
        list($where, $arg, $join) = $this->_buildCondition($field);
        $order = $this->_buildOrderby($orderby);
        $sql = $this->_bindSql('SELECT * FROM %s AS a %s %s %s %s', $this->getTable(), $join, $where, $order, $this->sqlLimit($limit, $offset));
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll($arg, 'lid');
    }

    protected function _buildCondition($field)
    {
        $join = '';
        $where = 'WHERE 1';
        $arg = array();
        foreach ($field as $key => $value) {
            switch ($key) {
                case 'lid':
                    $where .= ' AND a.lid'.$this->_sqlIn($value, $arg);
                    break;
                case 'name':
                    $where .= ' AND a.name=?';
                    $arg[] = $value;
                    break;
                case 'iflogo':
                    $where .= ' AND a.iflogo=?';
                    $arg[] = $value;
                    break;
                case 'ifcheck':
                    $where .= ' AND a.ifcheck=?';
                    $arg[] = $value;
                    break;
                case 'typeid':
                    $where .= ' AND b.`typeid` =?';
                    $join = sprintf('LEFT JOIN %s AS b USING(`lid`) ', $this->getTable($this->_table_relation));
                    $arg[] = $value;
                    break;
            }
        }

        return array($where, $arg, $join);
    }

    protected function _buildOrderby($orderby)
    {
        $array = array();
        foreach ($orderby as $key => $value) {
            switch ($key) {
                case 'vieworder':
                    $array[] = 'a.vieworder '.($value ? 'ASC' : 'DESC');
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
