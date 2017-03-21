<?php

/**
 * 通知基础表.
 *
 * @author peihong.zhang
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwMessageNoticesDao.php 3575 2012-01-11 11:32:47Z zhangph $
 */
class PwMessageNoticesDao extends PwBaseDao
{
    protected $_table = 'message_notices';
    protected $_pk = 'id';
    protected $_dataStruct = ['id', 'uid', 'title', 'typeid', 'param', 'extend_params', 'is_read', 'is_ignore', 'modified_time', 'created_time'];

    public function getNotice($id)
    {
        $sql = $this->_bindTable('SELECT * FROM %s WHERE id=?');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->getOne([$id]);
    }

    public function getPrevNotice($uid, $id)
    {
        $sql = $this->_bindTable('SELECT * FROM %s WHERE uid=? AND id<? ORDER BY `id` DESC LIMIT 1');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->getOne([$uid, $id]);
    }

    public function getNextNotice($uid, $id)
    {
        $sql = $this->_bindTable('SELECT * FROM %s WHERE uid=? AND id>? ORDER BY `id` ASC LIMIT 1');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->getOne([$uid, $id]);
    }

    public function getNoticesOrderByRead($uid, $num)
    {
        $num = intval($num);
        $sql = $this->_bindTable('SELECT * FROM %s WHERE uid=? ORDER BY is_read ASC,modified_time DESC LIMIT ?');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll([$uid, $num]);
    }

    public function getNotices($uid, $typeid, $offset = 0, $num = 20)
    {
        $offset = intval($offset);
        $num = intval($num);
        $typeid = intval($typeid);
        $params = [$uid];
        $sql = 'SELECT * FROM %s WHERE uid=?';
        if ($typeid > 1) {
            $params[] = $typeid;
            $sql .= ' AND typeid=?';
        } else {
            $sql .= ' AND typeid>1';
        }
        $sql .= ' ORDER BY modified_time DESC'.$this->sqlLimit($num, $offset);
        $sql = $this->_bindTable($sql);
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll($params);
    }

    /**
     * 获取未读通知数.
     *
     * @param int $uid
     *
     * @return int
     */
    public function getUnreadNoticeCount($uid)
    {
        $sql = $this->_bindTable('SELECT COUNT(*) FROM %s WHERE uid=? AND `is_read`=0');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->getValue([$uid]);
    }

    public function addNotice($fields)
    {
        if (! $fields = $this->_filterStruct($fields)) {
            return false;
        }
        $sql = $this->_bindTable('INSERT INTO %s SET ').$this->sqlSingle($fields);
        $this->getConnection()->execute($sql);

        return $this->getConnection()->lastInsertId();
    }

    /**
     * 获取用户通知(按类型).
     *
     * @param int $uid
     * @param int $type
     * @param int $param
     */
    public function getNoticeByUid($uid, $type, $param)
    {
        $sql = $this->_bindTable('SELECT * FROM %s WHERE uid=? AND `typeid`=? AND `param`=?');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->getOne([$uid, $type, $param]);
    }

    /**
     * 按类型统计用户通知数.
     *
     * @param int $uid
     */
    public function countNoticesByType($uid)
    {
        $sql = $this->_bindTable('SELECT COUNT(*) AS num,typeid FROM %s WHERE uid=? AND typeid>1 GROUP BY typeid');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll([$uid]);
    }

    public function updateNotice($id, $fields, $increaseFields = [])
    {
        return $this->_update($id, $fields, $increaseFields);
    }

    public function batchUpdateNotice($ids, $fields, $increaseFields = [])
    {
        return $this->_batchUpdate($ids, $fields, $increaseFields);
    }

    public function batchUpdateNoticeByUidAndType($uid, $type, $fields)
    {
        $sql = $this->_bindSql('UPDATE %s SET %s WHERE uid=? AND typeid=? ', $this->getTable(), $this->sqlSingle($fields));
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->update([$uid, $type]);
    }

    /**
     * 删除一条通知.
     *
     * @param int $id
     */
    public function deleteNotice($id)
    {
        $sql = $this->_bindTable('DELETE FROM %s WHERE id=?');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->update([$id]);
    }

    /**
     * 批量删除通知.
     *
     * @param int $id
     */
    public function deleteNoticeByIds($ids)
    {
        $sql = $this->_bindTable('DELETE FROM %s WHERE `id` IN '.$this->sqlImplode($ids));
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->update();
    }

    public function deleteNoticeByIdsAndUid($uid, $ids)
    {
        $sql = $this->_bindTable('DELETE FROM %s WHERE `id` IN '.$this->sqlImplode($ids).' AND uid=?');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->update([$uid], true);
    }

    /**
     * 根据类型删除通知.
     *
     * @param int $uid
     * @param int $type
     * @param int $param
     * @param bool
     */
    public function deleteNoticeByType($uid, $type, $param)
    {
        $sql = $this->_bindTable('DELETE FROM %s WHERE `uid`=? AND `typeid`=? AND `param`=?');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->update([$uid, $type, $param]);
    }

    /**
     * 根据uid删除通知.
     *
     * @param int $uid
     * @param bool
     */
    public function deleteNoticeByUid($uid)
    {
        $sql = $this->_bindTable('DELETE FROM %s WHERE `uid`=?');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->update([$uid]);
    }

    /**
     * 根据类型批量删除通知.
     *
     * @param int   $uid
     * @param int   $type
     * @param array $params
     * @param bool
     */
    public function betchDeleteNoticeByType($uid, $type, $params)
    {
        $sql = $this->_bindTable('DELETE FROM %s WHERE `uid`=? AND `typeid`=? AND `param` IN '.$this->sqlImplode($params));
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->update([$uid, $type]);
    }
}
