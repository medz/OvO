<?php

Wind::import('SRC:library.base.PwBaseDao');
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>.
 *
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwLikeRelationsDao.php 5754 2012-03-10 07:01:17Z gao.wanggao $
 */
class PwLikeRelationsDao extends PwBaseDao
{
    protected $_table = 'like_tag_relations';
    protected $_dataStruct = array('logid', 'tagid');

    public function getInfo($tagid)
    {
        $sql = $this->_bindTable('SELECT * FROM %s WHERE tagid = ? ');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll(array($tagid));
    }

    public function getInfoList($tagid, $offset, $limit)
    {
        $where = ' WHERE tagid = ? ';
        $sql = $this->_bindSql('SELECT * FROM %s %s  %s ', $this->getTable(), $where, $this->sqlLimit($limit, $offset));
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll(array($tagid));
    }

    public function addInfo($data)
    {
        if (!$data = $this->_filterStruct($data)) {
            return false;
        }
        $sql = $this->_bindTable('INSERT INTO %s SET ').$this->sqlSingle($data);

        return $this->getConnection()->execute($sql);
    }

    public function deleteInfo($logid, $tagid)
    {
        $sql = $this->_bindTable('DELETE FROM %s  WHERE logid = ? AND tagid = ? ');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->update(array($logid, $tagid));
    }

    public function deleteInfos($tagid)
    {
        $sql = $this->_bindTable('DELETE FROM %s  WHERE tagid = ? ');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->update(array($tagid));
    }

    public function deleteInfosBylogid($logid)
    {
        $sql = $this->_bindTable('DELETE FROM %s  WHERE logid = ? ');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->update(array($logid));
    }
}
