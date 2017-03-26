<?php

Wind::import('SRC:library.base.PwBaseDao');
 /**
  * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>.
  *
  * @author $Author: gao.wanggao $ Foxsee@aliyun.com
  * @copyright ?2003-2103 phpwind.com
  * @license http://www.phpwind.com
  *
  * @version $Id: PwMedalLogDao.php 12575 2012-06-23 10:09:56Z gao.wanggao $
  */
 class PwMedalLogDao extends PwBaseDao
 {
     protected $_table = 'medal_log';
     protected $_dataStruct = ['log_id', 'uid', 'medal_id', 'award_status', 'created_time', 'expired_time', 'log_order'];

     public function getInfo($logId)
     {
         $sql = $this->_bindTable('SELECT * FROM %s WHERE log_id = ? ');
         $smt = $this->getConnection()->createStatement($sql);

         return $smt->getOne([$logId]);
     }

     public function getInfoByUidMedalId($uid, $medalId)
     {
         $sql = $this->_bindTable('SELECT * FROM %s WHERE uid = ?  AND medal_id = ?');
         $smt = $this->getConnection()->createStatement($sql);

         return $smt->getOne([$uid, $medalId]);
     }

     public function fetchMedalLog($logIds)
     {
         $sql = $this->_bindSql('SELECT * FROM %s WHERE log_id IN %s ', $this->getTable(), $this->sqlImplode($logIds));
         $smt = $this->getConnection()->createStatement($sql);

         return $smt->queryAll([], 'log_id');
     }

     public function getInfoListByUid($uid)
     {
         $sql = $this->_bindTable('SELECT * FROM %s WHERE uid = ? ORDER BY award_status DESC, log_order ASC ');
         $smt = $this->getConnection()->createStatement($sql);

         return $smt->queryAll([$uid], 'log_id');
     }

     public function getInfoListByUidStatus($uid, $status)
     {
         $sql = $this->_bindTable('SELECT * FROM %s WHERE uid = ? AND award_status = ?  ORDER BY award_status DESC, log_order ASC ');
         $smt = $this->getConnection()->createStatement($sql);

         return $smt->queryAll([$uid, $status], 'log_id');
     }

     public function getInfoList($uid, $status, $medalId, $offset, $limit)
     {
         $where = ' WHERE 1 ';
         $_array = [];

         if ($uid > 0) {
             $where .= ' AND uid = ? ';
             $_array[] = $uid;
         }

         if ($status > 0) {
             $where .= ' AND award_status = ? ';
             $_array[] = $status;
         }

         if ($medalId > 0) {
             $where .= ' AND medal_id = ? ';
             $_array[] = $medalId;
         }
         $sql = $this->_bindSql('SELECT * FROM %s %s ORDER BY award_status DESC, log_order ASC %s', $this->getTable(), $where, $this->sqlLimit($limit, $offset));
         $smt = $this->getConnection()->createStatement($sql);

         return $smt->queryAll($_array, 'log_id');
     }

     public function countInfo($uid, $status, $medalId)
     {
         $where = ' WHERE 1 ';
         $_array = [];

         if ($uid > 0) {
             $where .= ' AND uid = ? ';
             $_array[] = $uid;
         }

         if ($status > 0) {
             $where .= ' AND award_status = ? ';
             $_array[] = $status;
         }

         if ($medalId > 0) {
             $where .= ' AND medal_id = ? ';
             $_array[] = $medalId;
         }

         $sql = $this->_bindSql('SELECT COUNT(*) AS count FROM %s %s', $this->getTable(), $where);
         $smt = $this->getConnection()->createStatement($sql);

         return $smt->getValue($_array);
     }

     public function getMedalLogList($uid, $status, $medalIds, $offset, $limit)
     {
         $where = ' WHERE 1 ';
         $_array = [];

         if ($uid > 0) {
             $where .= ' AND uid = ? ';
             $_array[] = $uid;
         }

         if ($status > 0) {
             $where .= ' AND award_status = ? ';
             $_array[] = $status;
         }

         if (count($medalIds) > 0) {
             $where .= ' AND medal_id IN '.$this->sqlImplode($medalIds);
         }
         $sql = $this->_bindSql('SELECT * FROM %s %s ORDER BY award_status DESC, log_order ASC %s', $this->getTable(), $where, $this->sqlLimit($limit, $offset));
         $smt = $this->getConnection()->createStatement($sql);

         return $smt->queryAll($_array, 'log_id');
     }

     public function countMedalLogList($uid, $status, $medalIds)
     {
         $where = ' WHERE 1 ';
         $_array = [];

         if ($uid > 0) {
             $where .= ' AND uid = ? ';
             $_array[] = $uid;
         }

         if ($status > 0) {
             $where .= ' AND award_status = ? ';
             $_array[] = $status;
         }

         if (count($medalIds) > 0) {
             $where .= ' AND medal_id IN '.$this->sqlImplode($medalIds);
         }
         $sql = $this->_bindSql('SELECT COUNT(*) AS count FROM %s %s', $this->getTable(), $where);
         $smt = $this->getConnection()->createStatement($sql);

         return $smt->getValue($_array);
     }

    /*

    public function addInfo($data) {
        if (!$data = $this->_filterStruct($data)) return false;
        $sql = $this->_bindSql('INSERT INTO %s SET %s',  $this->getTable(), $this->sqlSingle($data));
        $this->getConnection()->execute($sql);
        return $this->getConnection()->lastInsertId();
    }
    */

    public function replace($data)
    {
        if (! $data = $this->_filterStruct($data)) {
            return false;
        }
        if (! $data['uid'] || ! $data['medal_id']) {
            return false;
        }
        $sql = $this->_bindSql('REPLACE INTO %s SET %s', $this->getTable(), $this->sqlSingle($data));
        $this->getConnection()->execute($sql);

        return $this->getConnection()->lastInsertId();
    }

     public function updateInfo($logId, $data)
     {
         if (! $data = $this->_filterStruct($data)) {
             return false;
         }
         $sql = $this->_bindSql('UPDATE %s SET %s WHERE log_id = ? ', $this->getTable(), $this->sqlSingle($data));
         $smt = $this->getConnection()->createStatement($sql);

         return $smt->update([$logId]);
     }

     public function updateExpiredByUidMedalId($uid, $medalId, $time)
     {
         $sql = $this->_bindTable('UPDATE %s SET `expired_time`  = ? WHERE `medal_id` = ?  AND `uid` = ?');
         $smt = $this->getConnection()->createStatement($sql);

         return $smt->update([$time, $medalId, $uid]);
     }

     public function deleteInfo($logId)
     {
         $sql = $this->_bindTable('DELETE FROM %s  WHERE log_id = ? ');
         $smt = $this->getConnection()->createStatement($sql);

         return $smt->update([$logId]);
     }

     public function deleteInfos($expiredTime, $awardStatus)
     {
         $sql = $this->_bindTable('DELETE FROM %s  WHERE expired_time <= ? AND expired_time > 0 AND award_status = ? ');
         $smt = $this->getConnection()->createStatement($sql);

         return $smt->update([$expiredTime, $awardStatus]);
     }

     public function deleteInfosByUidMedalIds($uid, $medalIds)
     {
         $sql = $this->_bindSql('DELETE  FROM %s WHERE uid = ? AND medal_id IN %s ', $this->getTable(), $this->sqlImplode($medalIds));
         $smt = $this->getConnection()->createStatement($sql);

         return $smt->update([$uid]);
     }

     public function deleteInfoByMedalId($medalId)
     {
         $sql = $this->_bindTable('DELETE  FROM %s WHERE medal_id =? ');
         $smt = $this->getConnection()->createStatement($sql);

         return $smt->update([$medalId]);
     }
 }
