<?php

defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 用户组基础服务
 *
 * @author peihong.zhangph <peihong.zhangph@aliyun-inc.com> Oct 31, 2011
 * @link http://www.phpwind.com
 * @copyright 2011 phpwind.com
 * @license
 * @version $Id: PwUserGroups.php 18966 2012-10-09 08:15:59Z xiaoxia.xuxx $
 */

class PwUserGroups
{
    protected $_allowGroupTypes = array('member', 'system', 'special', 'default');

    /**
     * 按会员组类型获取组列表
     *
     * @return array
     */
    public function getGroupTypes()
    {
        return $this->_allowGroupTypes;
    }

    /**
     * 获取用户组类型名
     */
    public function getTypeNames()
    {
        return array_combine($this->_allowGroupTypes, array('会员组', '管理组', '特殊组', '默认组'));
    }

    /**
     * 获取一个会员组详细信息
     *
     * @param  int   $gid
     * @return array
     */
    public function getGroupByGid($gid)
    {
        if (empty($gid)) {
            return array();
        }

        return $this->_getDao()->getGroupByGid($gid);
    }

    /**
     * 根据一组gid获取用户组
     *
     * @param  array $gids
     * @return array
     */
    public function fetchGroup($gids)
    {
        if (empty($gids) || !is_array($gids)) {
            return array();
        }

        return $this->_getDao()->fetchGroup($gids);
    }

    /**
     * 按会员组类型获取组列表
     *
     * @param  string $groupType
     * @return array
     */
    public function getGroupsByType($groupType)
    {
        if (!$this->checkGroupType($groupType)) {
            return array();
        }

        return $this->_getDao()->getGroupsByType($groupType);
    }

    /**
     * 获取几个类型的所有用户组
     *
     * @param  array $groupTypes
     * @return array
     */
    public function getGroupsByTypes($groupTypes)
    {
        if (count($groupTypes) == 1) {
            $groupType = array_pop($groupTypes);

            return $this->getGroupsByType($groupType);
        }
        $groups = array();
        $allGroups = $this->getAllGroups();
        foreach ($allGroups as $k => $v) {
            if (!in_array($v['type'], $groupTypes)) {
                continue;
            }
            $groups[$k] = $v;
        }

        return $groups;
    }

    public function getGroupsByTypeInUpgradeOrder($groupType)
    {
        if (!$this->checkGroupType($groupType)) {
            return array();
        }

        return $this->_getDao()->getGroupsByTypeInUpgradeOrder($groupType);
    }

    /**
     * 获取所有系统和特殊用户组
     */
    public function getSystemAndSpecialGroups()
    {
        return $this->getGroupsByTypes(array('system', 'special'));
    }

    /**
     * 获取所有不可升级的用户组
     */
    public function getNonUpgradeGroups()
    {
        return $this->getGroupsByTypes(array('system', 'special', 'default'));
    }

    /**
     * 检查会员组类型
     *
     * @param  string $groupType
     * @return bool
     */
    public function checkGroupType($groupType)
    {
        return in_array($groupType, $this->_allowGroupTypes);
    }

    /**
     * 获取所有用户组信息
     *
     * @return array
     */
    public function getAllGroups()
    {
        return $this->_getDao()->getAllGroups();
    }

    /**
     * 获取所有分好类的组
     *
     * @return array
     */
    public function getClassifiedGroups()
    {
        if (!$groups = $this->getAllGroups()) {
            return array();
        }
        $data = array();
        foreach ($groups as $key => $v) {
            $data[$v['type']][$key] = $v;
        }

        return $data;
    }

    /**
     * 添加用户组
     *
     * @param  PwUserGroupDm $dm
     * @return bool
     */
    public function addGroup($dm)
    {
        if (($result = $dm->beforeAdd()) !== true) {
            return $result;
        }
        $gid = $this->_getDao()->addGroup($dm->getData());
        PwSimpleHook::getInstance('PwUserGroups_update')->runDo(array($gid));

        return $gid;
    }

    /**
     * 更新用户组信息
     *
     * @param  PwUserGroupDm $dm
     * @return bool
     */
    public function updateGroup($dm)
    {
        if (($result = $dm->beforeUpdate()) !== true) {
            return $result;
        }
        $gid = $dm->getGroupId();
        $result = $this->_getDao()->updateGroup($gid, $dm->getData());
        PwSimpleHook::getInstance('PwUserGroups_update')->runDo(array($gid));

        return $result;
    }

    /**
     * 批量更新用户组基本信息
     *
     * @param  array (PwUserGroupDm) $groups
     * @return bool
     */
    public function updateGroups($groups)
    {
        $gids = array();
        $dao = $this->_getDao();
        foreach ($groups as $v) {
            if ($v instanceof PwUserGroupDm && $v->beforeUpdate()) {
                $gid = $v->getGroupId();
                $dao->updateGroup($gid, $v->getData());
                $gids[] = $gid;
            }
        }
        PwSimpleHook::getInstance('PwUserGroups_update')->runDo($gids);

        return true;
    }

    /**
     * 删除用户组
     *
     * @param int $gid
     */
    public function deleteGroup($gid)
    {
        return $this->_getDao()->deleteGroup($gid);
    }

    /**
     * Enter description here ...
     * @return PwUserGroupsDao
     */
    protected function _getDao()
    {
        return Wekit::loadDao('usergroup.dao.PwUserGroupsDao');
    }
}
