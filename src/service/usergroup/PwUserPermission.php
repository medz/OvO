<?php

defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 用户权限基础服务
 *
 * @author peihong.zhangph <peihong.zhangph@aliyun-inc.com> Nov 1, 2011
 * @link http://www.phpwind.com
 * @copyright 2011 phpwind.com
 * @license
 * @version $Id: PwUserPermission.php 24736 2013-02-19 09:24:40Z jieyin $
 */

class PwUserPermission
{
    /**
     * 设置用户组权限
     *
     * @param  PwUserPermissionDm $dm
     * @return bool
     */
    public function setPermission(PwUserPermissionDm $dm)
    {
        if (!$data = $dm->getData()) {
            return false;
        }
        $result = $this->_getGroupPermissionDao()->setGroupPermission($data);
        PwSimpleHook::getInstance('PwUserGroupPermission_update')->runDo($dm);

        return $result;
    }

    /**
     * 获取某会员组的权限
     *
     * @param  string $gid
     * @param  array  $keys
     * @return array
     */
    public function getPermissions($gid, $keys = array())
    {
        $gid = intval($gid);
        if (!is_array($keys) || !$keys || $gid < 1) {
            return array();
        }

        return $this->_getGroupPermissionDao()->getPermissions($gid, $keys);
    }

    /**
     * 获取某个rkey的权限
     *
     * @param  string $rkey
     * @return array
     */
    public function getPermissionByRkey($rkey)
    {
        return $this->_getGroupPermissionDao()->getPermissionByRkey($rkey);
    }

    /**
     * 获取指定用户组某个rkey的权限
     *
     * @param  string $rkey
     * @param  array  $gids
     * @return array
     */
    public function getPermissionByRkeyAndGids($rkey, $gids)
    {
        if (empty($gids) || !is_array($gids)) {
            return array();
        }

        return $this->_getGroupPermissionDao()->getPermissionByRkeyAndGids($rkey, $gids);
    }

    /**
     * 获取某类rkey的权限
     *
     * @param  array $rkeys
     * @return array
     */
    public function fetchPermissionByRkey($rkeys = array())
    {
        if (!is_array($rkeys) || !$rkeys) {
            return array();
        }

        return $this->_getGroupPermissionDao()->fetchPermissionByRkey($rkeys);
    }

    /**
     * 根据gids获取用户组权限
     *
     * @param  array $gids
     * @return array
     */
    public function fetchPermissionByGid($gids)
    {
        if (!is_array($gids) || !$gids) {
            return array();
        }

        return $this->_getGroupPermissionDao()->fetchPermissionByGid($gids);
    }

    /**
     * 根据用户组删除权限点
     *
     * @param  unknown_type $gid
     * @return bool
     */
    public function deletePermissionsByGid($gid)
    {
        return $this->_getGroupPermissionDao()->deletePermissionsByGid($gid);
    }

    /**
     * 批量删除用户组权限点
     *
     * @param  int   $gid
     * @param  array $keys
     * @return bool
     */
    public function batchDeletePermissionByGidAndKeys($gid, $keys)
    {
        $gid = intval($gid);
        if (!is_array($keys) || !$keys || $gid < 1) {
            return false;
        }

        return $this->_getGroupPermissionDao()->batchDeletePermissionByGidAndKeys($gid, $keys);
    }

    /**
     * Enter description here ...
     *
     * @return PwUserPermissionGroupsDao
     */
    protected function _getGroupPermissionDao()
    {
        return Wekit::loadDao('usergroup.dao.PwUserPermissionGroupsDao');
    }
}
