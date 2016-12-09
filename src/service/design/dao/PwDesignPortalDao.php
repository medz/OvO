<?php

Wind::import('SRC:library.base.PwBaseDao');
/**
 * the last known user to change this file in the repository  <$LastChangedBy: long.shi $>.
 *
 * @author $Author: long.shi $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwDesignPortalDao.php 18647 2012-09-25 07:36:25Z long.shi $
 */
class PwDesignPortalDao extends PwBaseDao
{
    protected $_pk = 'id';
    protected $_table = 'design_portal';
    protected $_dataStruct = array('id', 'pagename', 'title', 'keywords', 'description', 'domain', 'cover', 'isopen', 'header', 'navigate', 'footer', 'template', 'style', 'created_uid', 'created_time');

    public function get($id)
    {
        return $this->_get($id);
    }

    public function getByDomain($domain)
    {
        $sql = $this->_bindTable('SELECT * FROM %s WHERE domain = ? LIMIT 1');

        return $this->getConnection()->createStatement($sql)->getOne(array($domain));
    }

    public function countPortalByPagename($pagename)
    {
        $sql = $this->_bindTable('SELECT  count(*) FROM %s WHERE `pagename` = ? ');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->getValue(array($pagename));
    }

    public function fetch($ids)
    {
        return $this->_fetch($ids, 'id');
    }

    public function searchPortal($data, $offset, $limit)
    {
        list($where, $array) = $this->_buildCondition($data);
        $sql = $this->_bindSql('SELECT * FROM %s %s %s ', $this->getTable(), $where, $this->sqlLimit($limit, $offset));
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll($array, 'id');
    }

    public function countPartal($data)
    {
        list($where, $array) = $this->_buildCondition($data);
        $sql = $this->_bindSql('SELECT  count(*) FROM %s %s ', $this->getTable(), $where);
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->getValue(array($array));
    }

    public function add($data)
    {
        return $this->_add($data, true);
    }

    public function update($id, $data)
    {
        return $this->_update($id, $data);
    }

    public function batchOpen($ids, $isopen)
    {
        $sql = $this->_bindSql('UPDATE %s SET `isopen`= ? WHERE `id` IN %s', $this->getTable(), $this->sqlImplode($ids));
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->update(array($isopen));
    }

    public function delete($id)
    {
        return $this->_delete($id);
    }

    public function batchDelete($ids)
    {
        return $this->_batchDelete($ids);
    }

    private function _buildCondition($data)
    {
        $where = ' WHERE 1';
        $array = array();
        foreach ($data as $key => $value) {
            switch ($key) {
                case 'isopen':
                    $where .= ' AND `isopen` = ?';
                    $array[] = $value;
                    break;
                case 'created_uid':
                    $where .= ' AND `created_uid` = ?';
                    $array[] = $value;
                    break;
            }
        }

        return array($where, $array);
    }
}
