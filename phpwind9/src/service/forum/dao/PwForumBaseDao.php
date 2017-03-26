<?php

defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 版块基础dao服务
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwForumBaseDao.php 10801 2012-05-30 06:18:39Z jieyin $
 */
class PwForumBaseDao extends PwBaseDao
{
    public function getForum($fid)
    {
        return ['fid' => $fid];
    }

    public function fetchForum($fids)
    {
        $data = [];
        foreach ($fids as $value) {
            $data[$value] = [];
        }

        return $data;
    }

    public function getForumList()
    {
        return [];
    }

    public function getCommonForumList()
    {
        return [];
    }

    public function addForum($fields)
    {
        return false;
    }

    public function updateForum($fid, $fields, $increaseFields = [])
    {
        return true;
    }

    public function batchUpdateForum($fids, $fields, $increaseFields = [])
    {
        return true;
    }

    public function deleteForum($fid)
    {
        return false;
    }
}
