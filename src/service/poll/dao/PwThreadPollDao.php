<?php

defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 帖子投票关系dao服务
 *
 * @author MingXing Sun <mingxing.sun@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwThreadPollDao.php 9191 2012-05-03 11:04:28Z hejin $
 * @package poll
 */

class PwThreadPollDao extends PwBaseDao
{
    protected $_table = 'app_poll_thread';
    protected $_pk = 'tid';
    protected $_dataStruct = array('tid', 'poll_id', 'created_userid');

    public function getPoll($tid)
    {
        return $this->_get($tid);
    }

    public function fetchPoll($tids)
    {
        return $this->_fetch($tids);
    }

    public function getPollByPollid($pollid)
    {
        $sql = $this->_bindSql('SELECT * FROM %s WHERE poll_id = ?', $this->getTable());
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->getOne(array($pollid));
    }

    public function fetchByPollid($pollids)
    {
        $sql = $this->_bindSql('SELECT * FROM %s WHERE poll_id IN %s', $this->getTable(), $this->sqlImplode($pollids));
        $smt = $this->getConnection()->query($sql);

        return $smt->fetchAll();
    }

    public function addPoll($fieldData)
    {
        return $this->_add($fieldData);
    }

    public function deletePoll($tid)
    {
        return $this->_delete($tid);
    }

    public function batchDeletePoll($tids)
    {
        return $this->_batchDelete($tids);
    }

    public function deleteByPollid($pollid)
    {
        $sql = $this->_bindSql('DELETE FROM %s WHERE poll_id = ?', $this->getTable());
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->update(array($pollid));
    }
}
