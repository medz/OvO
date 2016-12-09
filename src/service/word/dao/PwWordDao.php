<?php

defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('SRC:library.base.PwBaseDao');

/**
 * 词语过滤Dao服务
 *
 * @author Mingqu Luo <luo.mingqu@gmail.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 *
 * @version $Id: PwWordDao.php 17015 2012-08-30 08:32:05Z hejin $
 */
class PwWordDao extends PwBaseDao
{
    protected $_table = 'word';
    protected $_pk = 'word_id';
    protected $_dataStruct = array('word_id', 'word_type', 'word', 'word_replace', 'word_from', 'created_time');

    public function get($wordId)
    {
        return $this->_get($wordId);
    }

    public function getByWord($word)
    {
        $sql = $this->_bindSql('SELECT * FROM %s where word = ?', $this->getTable());
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->getOne(array($word));
    }

    public function getByType($type)
    {
        $sql = $this->_bindSql('SELECT * FROM %s where word_type = ?', $this->getTable());
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll(array($type));
    }

    public function fetch($wordIds)
    {
        return $this->_fetch($wordIds);
    }

    public function fetchByWord($word)
    {
        $sql = $this->_bindSql('SELECT * FROM %s where word IN %s', $this->getTable(), $this->sqlImplode($word));
        $smt = $this->getConnection()->query($sql);

        return $smt->fetchAll();
    }

    public function getWordList($limit, $offset)
    {
        $sql = $this->_bindSql('SELECT * FROM %s ORDER BY created_time DESC %s', $this->getTable(), $this->sqlLimit($limit, $offset));
        $smt = $this->getConnection()->query($sql);

        return $smt->fetchAll();
    }

    public function count()
    {
        $sql = $this->_bindSql('SELECT count(*) FROM %s', $this->getTable());
        $smt = $this->getConnection()->query($sql);

        return $smt->fetchColumn();
    }

    public function countByFrom($from)
    {
        $sql = $this->_bindSql('SELECT count(*) FROM %s where word_from = ?', $this->getTable());
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->getValue(array($from));
    }

    public function add($fieldData)
    {
        return $this->_add($fieldData);
    }

    public function delete($wordId)
    {
        return $this->_delete($wordId);
    }

    public function deleteByType($type)
    {
        $sql = $this->_bindSql('DELETE FROM %s WHERE word_type = ?', $this->getTable());

        return $this->getConnection()->createStatement($sql)->execute(array($type));
    }

    public function deleteByKeyword($keyword)
    {
        $sql = $this->_bindSql('DELETE FROM %s WHERE word LIKE ?', $this->getTable());

        return $this->getConnection()->createStatement($sql)->execute(array("%$keyword%"));
    }

    public function deleteByTypeAndKeyword($type, $keyword)
    {
        $sql = $this->_bindSql('DELETE FROM %s WHERE word_type= ? AND word LIKE ?', $this->getTable());

        return $this->getConnection()->createStatement($sql)->execute(array($type, "%$keyword%"));
    }

    public function update($wordId, $fieldData)
    {
        return $this->_update($wordId, $fieldData);
    }

    public function batchUpdate($wordIds, $fieldData)
    {
        return $this->_batchUpdate($wordIds, $fieldData);
    }

    public function batchDelete($wordIds)
    {
        return $this->_batchDelete($wordIds);
    }

    public function countSearchWord($condition)
    {
        list($where, $params) = $this->_buildCondition($condition);
        $sql = $this->_bindSql('SELECT COUNT(*) AS total FROM %s %s', $this->getTable(), $where);
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->getValue($params);
    }

    public function searchWord($condition, $limit, $offset)
    {
        list($where, $params) = $this->_buildCondition($condition);
        $sql = $this->_bindSql('SELECT * FROM %s %s ORDER BY created_time DESC %s', $this->getTable(), $where, $this->sqlLimit($limit, $offset));
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll($params);
    }

    private function _buildCondition($condition)
    {
        if (!$condition) {
            return array('', array());
        }

        $where = $params = array();
        foreach ($condition as $key => $value) {
            switch ($key) {
                case 'word_type':
                     $where[] = 'word_type = ?';
                     $params[] = $value;
                break;
                case 'word':
                     $where[] = 'word LIKE ?';
                     $params[] = "%$value%";
                break;
            }
        }

        $_whereSql = $where ? $this->_bindSql('WHERE %s', implode(' AND ', $where)) : '';

        return array($_whereSql, $params);
    }

    /**
     * 获得所有敏感词(需谨慎).
     */
    public function fetchAllWord()
    {
        $sql = $this->_bindSql('SELECT * FROM %s FORCE INDEX(PRIMARY) ORDER BY word_id DESC', $this->getTable());
        $smt = $this->getConnection()->query($sql);

        return $smt->fetchAll('word');
    }

    /**
     * 清空数据(需谨慎).
     */
    public function truncate()
    {
        $sql = $this->_bindTable('TRUNCATE TABLE %s ');

        return $this->getConnection()->query($sql);
    }

    /**
     * 更新所有类型(需谨慎，仅后台使用).
     */
    public function updateAll($fieldData)
    {
        if (!($fieldData = $this->_filterStruct($fieldData))) {
            return false;
        }

        $sql = $this->_bindSql('UPDATE %s SET %s', $this->getTable(), $this->sqlSingle($fieldData));

        return $this->getConnection()->query($sql);
    }
}
