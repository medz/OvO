<?php

defined('WEKIT_VERSION') || exit('Forbidden');

/**
 *
 * 投票选项DAO
 *
 * @author Mingqu Luo<luo.mingqu@gmail.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id$
 * @package wind
 */

class PwPollOptionDao extends PwBaseDao
{
    protected $_table = 'app_poll_option';
    protected $_pk = 'option_id';
    protected $_dataStruct = array('option_id', 'poll_id', 'voted_num', 'content', 'image');

    public function get($id)
    {
        return $this->_get($id);
    }

    public function fetch($ids)
    {
        return $this->_fetch($ids);
    }

    public function getByPollid($pollid)
    {
        $sql = $this->_bindSql('SELECT * FROM %s where poll_id = ? ORDER BY option_id ASC ', $this->getTable());
        $smt = $this->connection->createStatement($sql);

        return $smt->queryAll(array($pollid), $this->_pk);
    }

    public function fetchByPollid($pollids)
    {
        $sql = $this->_bindSql('SELECT * FROM %s where poll_id IN %s ORDER BY option_id ASC', $this->getTable(), $this->sqlImplode($pollids));
        $smt = $this->getConnection()->query($sql);

        return $smt->fetchAll();
    }

    public function countByPollid($pollid)
    {
        $sql = $this->_bindSql('SELECT COUNT(*) FROM %s where poll_id= ?', $this->getTable());
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->getValue(array($pollid));
    }

    public function add($fieldData)
    {
        return $this->_add($fieldData);
    }

    public function delete($id)
    {
        return $this->_delete($id);
    }

    public function deleteByPollid($pollid)
    {
        $sql = $this->_bindSql('DELETE FROM %s WHERE poll_id=?', $this->getTable());
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->update(array($pollid));
    }

    public function update($id, $fieldData, $increaseFields)
    {
        return $this->_update($id, $fieldData, $increaseFields);
    }

    public function batchDelete($optionIds)
    {
        return $this->_batchDelete($optionIds);
    }

    public function batchUpdate($ids, $fieldData)
    {
        return $this->_batchUpdate($ids, $fieldData);
    }
}
