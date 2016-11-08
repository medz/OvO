<?php

Wind::import('SRC:library.base.PwBaseDao');
 /**
  * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>
  * @author $Author: gao.wanggao $ Foxsee@aliyun.com
  * @copyright ?2003-2103 phpwind.com
  * @license http://www.phpwind.com
  * @version $Id: PwMedalInfoDao.php 8501 2012-04-19 09:32:42Z gao.wanggao $
  * @package
  */
 class PwMedalInfoDao extends PwBaseDao
 {
     protected $_table = 'medal_info';
     protected $_dataStruct = array('medal_id', 'name', 'path', 'image', 'icon', 'descrip', 'receive_type', 'medal_type', 'medal_gids', 'award_type', 'award_condition', 'expired_days', 'isopen', 'vieworder');

     public function getInfo($medalId)
     {
         $sql = $this->_bindTable('SELECT * FROM %s WHERE medal_id = ? ');
         $smt = $this->getConnection()->createStatement($sql);

         return $smt->getOne(array($medalId));
     }

     public function fetchInfo($medalIds)
     {
         $sql = $this->_bindSql('SELECT * FROM %s WHERE medal_id IN  %s', $this->getTable(), $this->sqlImplode($medalIds));
         $smt = $this->getConnection()->createStatement($sql);

         return $smt->queryAll(array(), 'medal_id');
     }

     public function countInfo($type)
     {
         $where = '';
         $_array = array();
         if ($type > 0) {
             $where = ' WHERE medal_type = ? ' ;
             $_array = array($type);
         }
         $sql = $this->_bindSql('SELECT COUNT(*) AS count FROM %s %s ', $this->getTable(), $where);
         $smt = $this->getConnection()->createStatement($sql);

         return $smt->getValue($_array);
     }

     public function getInfoList($receiveType, $awardType, $offset, $limit, $isopen = null)
     {
         $where = ' WHERE 1 ';
         $_array = array();
         $order = ' ORDER BY receive_type ASC , vieworder ASC ';
         if ($receiveType > 0) {
             $where .= ' AND receive_type = ? ' ;
             $order = ' ORDER BY vieworder ASC ';
             $_array[] = $receiveType;
         }
         if ($awardType > 0) {
             $where .= ' AND award_type = ? ' ;
             $order = ' ORDER BY vieworder ASC ';
             $_array[] = $awardType;
         }
         if (isset($isopen)) {
             $where .= ' AND isopen = ? ' ;
             $order = ' ORDER BY vieworder ASC ';
             $_array[] = $isopen;
         }
         $sql = $this->_bindSql('SELECT * FROM %s %s %s %s ', $this->getTable(), $where, $order, $this->sqlLimit($limit, $offset));
         $smt = $this->getConnection()->createStatement($sql);

         return $smt->queryAll($_array, 'medal_id');
     }

     public function getOpenMedalList($awardType, $receiveType)
     {
         $sql = $this->_bindTable('SELECT * FROM %s WHERE receive_type = ? AND award_type = ? AND isopen = 1  ORDER BY vieworder ASC ');
         $smt = $this->getConnection()->createStatement($sql);

         return $smt->queryAll(array($awardType, $receiveType), 'medal_id');
     }

     public function getInfoListByAwardtype($awardType, $isopen = null)
     {
         $where = ' WHERE award_type = ? ';
         $array = array($awardType);
         if (isset($isopen)) {
             $where .= ' AND isopen = ?';
             $array[] = $isopen;
         }
         $sql = $this->_bindSql('SELECT * FROM %s %s  ORDER BY vieworder ASC ', $this->getTable(), $where);
         $smt = $this->getConnection()->createStatement($sql);

         return $smt->queryAll($array, 'medal_id');
     }

     public function getInfoListByReceiveType($receiveType, $isopen = null)
     {
         $where = ' WHERE receive_type = ? ';
         $array = array($receiveType);
         if (isset($isopen)) {
             $where .= ' AND isopen = ?';
             $array[] = $isopen;
         }
         $sql = $this->_bindSql('SELECT * FROM %s  %s ORDER BY vieworder ASC ', $this->getTable(), $where);
         $smt = $this->getConnection()->createStatement($sql);

         return $smt->queryAll($array, 'medal_id');
     }

     public function getAllOpenMedal()
     {
         $sql = $this->_bindTable('SELECT * FROM %s WHERE isopen = 1 ORDER BY vieworder ASC');
         $smt = $this->getConnection()->createStatement($sql);

         return $smt->queryAll(array(), 'medal_id');
     }

     public function getAllMedal()
     {
         $sql = $this->_bindTable('SELECT * FROM %s ');
         $smt = $this->getConnection()->createStatement($sql);

         return $smt->queryAll(array(), 'medal_id');
     }

     public function addInfo($data)
     {
         if (!$data = $this->_filterStruct($data)) {
             return false;
         }
         $sql = $this->_bindSql('INSERT INTO %s SET %s', $this->getTable(), $this->sqlSingle($data));
         $this->getConnection()->execute($sql);

         return $this->getConnection()->lastInsertId();
     }

     public function updateInfo($medalId, $data)
     {
         if (!$data = $this->_filterStruct($data)) {
             return false;
         }
         $sql = $this->_bindSql('UPDATE %s SET %s WHERE medal_id = ? ', $this->getTable(), $this->sqlSingle($data));
         $smt = $this->getConnection()->createStatement($sql);

         return $smt->update(array($medalId));
     }

     public function deleteInfo($medalId)
     {
         $sql = $this->_bindTable('DELETE FROM %s  WHERE medal_id = ? ');
         $smt = $this->getConnection()->createStatement($sql);

         return $smt->update(array($medalId));
     }
 }
