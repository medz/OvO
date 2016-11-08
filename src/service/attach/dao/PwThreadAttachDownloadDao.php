<?php

/**
 * 帖子附件购买记录 / dao服务
 *
 * @author jinlong.panjl <jinlong.panjl@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id$
 * @package wind
 */
class PwThreadAttachDownloadDao extends PwBaseDao
{
    protected $_table = 'attachs_thread_download';
    protected $_dataStruct = array('id', 'aid', 'created_userid', 'created_time', 'ctype', 'cost');

    public function sumCost($aid)
    {
        $sql = $this->_bindTable('SELECT SUM(cost) AS sum FROM %s WHERE aid=?');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->getValue(array($aid));
    }

    public function get($id)
    {
        return $this->_get($id);
    }

    public function countByAid($aid)
    {
        $sql = $this->_bindTable('SELECT COUNT(*) FROM %s WHERE aid=?');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->getValue(array($aid));
    }

    public function getByAid($aid, $limit, $offset)
    {
        $sql = $this->_bindSql('SELECT * FROM %s WHERE aid=? ORDER BY created_time DESC %s', $this->getTable(), $this->sqlLimit($limit, $offset));
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll(array($aid), 'created_userid');
    }

    public function getByAidAndUid($aid, $uid)
    {
        $sql = $this->_bindTable('SELECT * FROM %s WHERE aid=? AND created_userid=?');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->getOne(array($aid, $uid));
    }

    public function add($fields)
    {
        return $this->_add($fields, false);
    }
}
