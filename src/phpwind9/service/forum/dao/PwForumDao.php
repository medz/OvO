<?php

defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 版块dao服务
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @license http://www.phpwind.com
 *
 * @version $Id: PwForumDao.php 18802 2012-09-27 10:17:30Z jieyin $
 */
class PwForumDao extends PwBaseDao
{
    protected $_table = 'bbs_forum';
    protected $_pk = 'fid';
    protected $_dataStruct = ['fid', 'parentid', 'type', 'issub', 'hassub', 'name', 'descrip', 'vieworder', 'manager', 'uppermanager', 'icon', 'logo', 'fup', 'fupname', 'isshow', 'across', 'isshowsub', 'newtime', 'password', 'allow_visit', 'allow_read', 'allow_post', 'allow_reply', 'allow_upload', 'allow_download', 'created_time', 'created_userid', 'created_username', 'created_ip', 'style'];

    public function getForum($fid)
    {
        return $this->_get($fid);
    }

    public function fetchForum($fids)
    {
        return $this->_fetch($fids, 'fid');
    }

    public function searchForum($keyword)
    {
        $sql = $this->_bindTable('SELECT fid,name FROM %s WHERE name LIKE ?');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll(["$keyword%"]);
    }

    public function getForumList()
    {
        $sql = $this->_bindTable('SELECT * FROM %s ORDER BY issub ASC,vieworder ASC');
        $smt = $this->getConnection()->query($sql);

        return $smt->fetchAll('fid');
    }

    public function getCommonForumList()
    {
        $sql = $this->_bindTable('SELECT * FROM %s WHERE issub=0 ORDER BY vieworder ASC');
        $smt = $this->getConnection()->query($sql);

        return $smt->fetchAll('fid');
    }

    public function getSubForums($fid)
    {
        $sql = $this->_bindTable('SELECT * FROM %s WHERE parentid=? ORDER BY vieworder ASC');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll([$fid], 'fid');
    }

    public function getForumOrderByType($asc)
    {
        $sql = $this->_bindSql('SELECT * FROM %s ORDER BY type %s', $this->getTable(), $asc ? 'ASC' : 'DESC');
        $smt = $this->getConnection()->query($sql);

        return $smt->fetchAll('fid');
    }

    public function addForum($fields)
    {
        return $this->_add($fields);
    }

    public function updateForum($fid, $fields, $increaseFields = [])
    {
        return $this->_update($fid, $fields);
    }

    public function batchUpdateForum($fids, $fields, $increaseFields = [])
    {
        return $this->_batchUpdate($fids, $fields);
    }

    public function deleteForum($fid)
    {
        return $this->_delete($fid);
    }
}
