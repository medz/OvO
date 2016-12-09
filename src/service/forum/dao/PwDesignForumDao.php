<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: jinlong.panjl $>.
 *
 * @author $Author: jinlong.panjl $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwDesignForumDao.php 20783 2012-11-08 06:52:27Z jinlong.panjl $
 */
class PwDesignForumDao extends PwBaseDao
{
    protected $_table = 'bbs_forum';
    protected $_mergeTable = 'bbs_forum_statistics';
    protected $_dataStruct = array();

    public function countSearchForum($data)
    {
        list($where, $arg) = $this->_buildCondition($data);
        $_mergeTable = $this->_bindTable(' LEFT JOIN %s AS s ON f.fid=s.fid', $this->getTable($this->_mergeTable));
        $sql = $this->_bindSql('SELECT COUNT(*) FROM %s AS f %s %s', $this->getTable(), $_mergeTable, $where);
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->getValue($arg);
    }

    public function searchForum($data, $order, $limit, $offset)
    {
        list($where, $arg) = $this->_buildCondition($data);
        $order = $this->_buildOrderby($order);
        $_mergeTable = $this->_bindTable(' LEFT JOIN %s AS s ON f.fid=s.fid', $this->getTable($this->_mergeTable));
        $sql = $this->_bindSql('SELECT * FROM %s AS f %s %s %s %s', $this->getTable(), $_mergeTable, $where, $order, $this->sqlLimit($limit, $offset));
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll($arg, 'fid');
    }

    protected function _buildCondition($field)
    {
        $where = ' WHERE 1';
        $arg = array();
        foreach ($field as $key => $value) {
            switch ($key) {
                case 'fid':
                    $where .= ' AND f.fid '.$this->_sqlIn($value, $arg);
                    break;
                case 'name':
                    $where .= ' AND f.name LIKE ?';
                    $arg[] = "%$value%";
                    break;
            }
        }

        return array($where, $arg);
    }

    protected function _buildOrderby($orderby)
    {
        $array = array();
        foreach ($orderby as $key => $value) {
            switch ($key) {
                case 'article':
                    $array[] = 's.article '.($value ? 'ASC' : 'DESC');
                    break;
                case 'posts':
                    $array[] = 's.posts '.($value ? 'ASC' : 'DESC');
                    break;
                case 'threads':
                    $array[] = 's.threads '.($value ? 'ASC' : 'DESC');
                    break;
                case 'lastpost_time':
                    $array[] = 's.lastpost_time '.($value ? 'ASC' : 'DESC');
                    break;
                case 'todaythreads':
                    $array[] = 's.todaythreads '.($value ? 'ASC' : 'DESC');
                    break;
                case 'todayposts':
                    $array[] = 's.todayposts '.($value ? 'ASC' : 'DESC');
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
