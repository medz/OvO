<?php

defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 订单dao服务
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @license http://www.phpwind.com
 * @version $Id: PwOrderDao.php 7491 2012-04-06 10:14:44Z jieyin $
 * @package forum
 */

class PwOrderDao extends PwBaseDao
{
    protected $_table = 'pay_order';
    protected $_dataStruct = array('id', 'order_no', 'price', 'number', 'state', 'payemail', 'paymethod', 'paytype', 'buy', 'created_userid', 'created_time', 'extra_1', 'extra_2');

    /*
    public function getForum($fid) {
        $sql = $this->_bindTable('SELECT * FROM %s WHERE fid=?');
        $smt = $this->getConnection()->createStatement($sql);
        return $smt->getOne(array($fid));
    }

    public function searchForum($keyword){
        $sql = $this->_bindTable('SELECT fid,name FROM %s WHERE name LIKE ?');
        $smt = $this->getConnection()->createStatement($sql);
        return $smt->queryAll(array("$keyword%"));
    }

    public function getForumList() {
        $sql = $this->_bindTable('SELECT * FROM %s ORDER BY issub ASC,vieworder ASC');
        $smt = $this->getConnection()->query($sql);
        return $smt->fetchAll('fid');
    }

    public function getCommonForumList() {
        $sql = $this->_bindTable('SELECT * FROM %s WHERE issub=0 ORDER BY vieworder ASC');
        $smt = $this->getConnection()->query($sql);
        return $smt->fetchAll('fid');
    }

    public function getForumByFids($fids) {
        $sql = $this->_bindSql('SELECT * FROM %s WHERE fid IN %s', $this->getTable(), $this->sqlImplode($fids));
        $smt = $this->getConnection()->query($sql);
        return $smt->fetchAll('fid');
    }

    public function getSubForums($fid) {
        $sql = $this->_bindTable('SELECT * FROM %s WHERE parentid=?');
        $smt = $this->getConnection()->createStatement($sql);
        return $smt->queryAll(array($fid), 'fid');
    }*/

    public function getOrder($id)
    {
        return $this->_get($id);
    }

    public function getOrderByOrderNo($orderno)
    {
        $sql = $this->_bindTable('SELECT * FROM %s WHERE order_no=?');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->getOne(array($orderno));
    }

    public function countByUidAndType($uid, $type)
    {
        $sql = $this->_bindTable('SELECT COUNT(*) AS sum FROM %s WHERE created_userid=? AND paytype=?');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->getValue(array($uid, $type));
    }

    public function getOrderByUidAndType($uid, $type, $limit, $offset)
    {
        $sql = $this->_bindSql('SELECT * FROM %s WHERE created_userid=? AND paytype=? ORDER BY id DESC %s', $this->getTable(), $this->sqlLimit($limit, $offset));
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll(array($uid, $type), 'id');
    }

    public function addOrder($fields)
    {
        return $this->_add($fields);
    }

    public function updateOrder($id, $fields)
    {
        return $this->_update($id, $fields);
    }
    /*
    public function updateForum($fid, $fields, $increaseFields = array()) {
        if (!$fields = $this->_filterStruct($fields)) {
            return false;
        }
        $sql = $this->_bindSql('UPDATE %s SET %s WHERE fid=?', $this->getTable(), $this->sqlSingle($fields));
        $smt = $this->getConnection()->createStatement($sql);
        return $smt->update(array($fid));
    }

    public function batchUpdateForum($fids, $fields, $increaseFields = array()) {
        if (!$fields = $this->_filterStruct($fields)) {
            return false;
        }
        $sql = $this->_bindSql('UPDATE %s SET %s WHERE fid IN %s', $this->getTable(), $this->sqlSingle($fields), $this->sqlImplode($fids));
        $this->getConnection()->execute($sql);
        return true;
    }

    public function deleteForum($fid) {
        $sql = $this->_bindTable('DELETE FROM %s WHERE fid=?');
        $smt = $this->getConnection()->createStatement($sql);
        return $smt->update(array($fid));
    }*/
}
