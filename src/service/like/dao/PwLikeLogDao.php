<?php

Wind::import('SRC:library.base.PwBaseDao');
/**
 * 喜欢记录DAO服务
 *
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwLikeLogDao.php 7776 2012-04-11 12:27:20Z gao.wanggao $
 * @package
 */
class PwLikeLogDao extends PwBaseDao
{
    protected $_table = 'like_log';
    protected $_dataStruct = array('uid', 'likeid', 'tagids', 'created_time');

    public function getInfo($logid)
    {
        $sql = $this->_bindTable('SELECT * FROM %s WHERE logid = ? ');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->getOne(array($logid));
    }

    public function fetchInfo($logids)
    {
        $sql = $this->_bindSql('SELECT * FROM %s WHERE logid IN %s ', $this->getTable(), $this->sqlImplode($logids));
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll(array(), 'logid');
    }

    public function getInfoByUidLikeid($uid, $likeid)
    {
        $sql = $this->_bindTable('SELECT * FROM %s WHERE uid = ? AND likeid = ? ');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->getOne(array($uid, $likeid));
    }

    public function getInfoList($uids, $offset, $limit)
    {
        $sql = $this->_bindSql('SELECT * FROM %s  WHERE uid IN %s  ORDER BY logid DESC %s ', $this->getTable(), $this->sqlImplode($uids), $this->sqlLimit($limit, $offset));
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll(array(), 'logid');
    }

    public function getLikeCount($uid)
    {
        $sql = $this->_bindSql('SELECT COUNT(*) AS count FROM %s WHERE uid = ?', $this->getTable());
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->getValue(array($uid));
    }

    public function getLikeidCount($likeid, $time)
    {
        $sql = $this->_bindSql('SELECT COUNT(*) AS count FROM (SELECT * FROM %s WHERE likeid = %s) AS tmpTable WHERE created_time > %s ', $this->getTable(), $likeid, $time);
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->getValue(array());
    }

    public function addInfo($data)
    {
        if (!$data = $this->_filterStruct($data)) {
            return false;
        }
        $sql = $this->_bindTable('INSERT INTO %s SET ').$this->sqlSingle($data);
        $this->getConnection()->execute($sql);

        return $this->getConnection()->lastInsertId();
    }

    public function updateInfo($logid, $data)
    {
        if (!$data = $this->_filterStruct($data)) {
            return false;
        }
        $sql = $this->_bindSql('UPDATE %s SET %s WHERE logid = ? ', $this->getTable(), $this->sqlSingle($data));
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->update(array($logid));
    }


    public function updateReplyCount($logid)
    {
        $sql = $this->_bindSql('UPDATE %s SET %s %s ', $this->getTable(), $this->sqlSingleIncrease(array('reply_count' => 1)), ' WHERE logid = ? ');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->update(array($logid));
    }

    public function deleteInfo($logid)
    {
        $sql = $this->_bindTable('DELETE FROM %s  WHERE logid = ? ');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->update(array($logid));
    }
}
