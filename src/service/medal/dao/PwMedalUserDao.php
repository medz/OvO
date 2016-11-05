<?php

Wind::import('SRC:library.base.PwBaseDao');
 /**
  * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>
  * @author $Author: gao.wanggao $ Foxsee@aliyun.com
  * @copyright ?2003-2103 phpwind.com
  * @license http://www.phpwind.com
  * @version $Id: PwMedalUserDao.php 20389 2012-10-29 03:41:38Z gao.wanggao $
  * @package
  */
 class PwMedalUserDao extends PwBaseDao
 {
     protected $_table = 'medal_user';
     protected $_dataStruct = array('uid', 'medals', 'counts', 'expired_time');

     public function getInfo($uid)
     {
         $sql = $this->_bindTable('SELECT * FROM %s WHERE uid = ? ');
         $smt = $this->getConnection()->createStatement($sql);

         return $smt->getOne(array($uid));
     }

     public function fetchInfo($uids)
     {
         $sql = $this->_bindSql('SELECT * FROM %s WHERE uid IN %s', $this->getTable(), $this->sqlImplode($uids));
         $smt = $this->getConnection()->createStatement($sql);

         return $smt->queryAll(array(), 'uid');
     }

     public function fetchMedalUserOrder($uids, $start, $limit)
     {
         $sql = $this->_bindSql('SELECT * FROM %s WHERE uid IN %s ORDER BY counts DESC %s', $this->getTable(), $this->sqlImplode($uids), $this->sqlLimit($limit, $start));
         $smt = $this->getConnection()->createStatement($sql);

         return $smt->queryAll(array(), 'uid');
     }

     public function getTotalOrder($limit)
     {
         $sql = $this->_bindSql('SELECT * FROM %s  ORDER BY counts DESC %s', $this->getTable(), $this->sqlLimit($limit, 0));
         $smt = $this->getConnection()->createStatement($sql);

         return $smt->queryAll(array(), 'uid');
     }

     public function getExpiredMedalUser($expiredTime, $start, $limit)
     {
         $sql = $this->_bindSql('SELECT * FROM %s WHERE expired_time <= ?  %s', $this->getTable(), $this->sqlLimit($limit, $start));
         $smt = $this->getConnection()->createStatement($sql);

         return $smt->queryAll(array($expiredTime));
     }

     public function countExpiredMedalUser($expiredTime)
     {
         $sql = $this->_bindTable('SELECT COUNT(*) FROM %s WHERE expired_time <= ?');
         $smt = $this->getConnection()->createStatement($sql);

         return $smt->getValue(array($expiredTime));
     }

     public function countMedalUser()
     {
         $sql = $this->_bindTable('SELECT COUNT(*) FROM %s ');
         $smt = $this->getConnection()->createStatement($sql);

         return $smt->getValue(array());
     }

     public function getMedalUserList($start, $perpage)
     {
         $sql = $this->_bindSql('SELECT * FROM %s %s', $this->getTable(), $this->sqlLimit($limit, $start));
         $smt = $this->getConnection()->createStatement($sql);

         return $smt->queryAll(array());
     }

     public function replaceInfo($data)
     {
         if (!$data = $this->_filterStruct($data)) {
             return false;
         }
         if ($data['uid'] < 1) {
             return false;
         }
         $sql = $this->_bindSql('REPLACE INTO %s SET %s', $this->getTable(), $this->sqlSingle($data));
         $this->getConnection()->execute($sql);

         return $this->getConnection()->lastInsertId();
     }


     public function deleteInfo($uid)
     {
         $sql = $this->_bindTable('DELETE FROM %s  WHERE uid = ? ');
         $smt = $this->getConnection()->createStatement($sql);

         return $smt->update(array($uid));
     }

     public function deleteMedalUsersByCount()
     {
         $sql = $this->_bindTable('DELETE FROM %s  WHERE counts = 0 ');
         $smt = $this->getConnection()->createStatement($sql);

         return $smt->update(array());
     }
 }
