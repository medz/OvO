<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>.
 *
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwDesignPermissionsService.php 20523 2012-10-30 10:32:39Z gao.wanggao $
 */
class PwDesignPermissionsService
{
    /**
     * 判断用户的管理组权限.
     *
     * @param int $uid
     */
    public function getPermissionsForUserGroup($uid)
    {
         
        $userBo = new PwUserBo($uid);
        $designPermission = $userBo->getPermission('design_allow_manage.push');

        return $designPermission ? $designPermission : -1;
    }

    /**
     * 判断用户的某个页面权限.
     *
     * @param int $uid
     * @param int $pageid
     */
    public function getPermissionsForPage($uid, $pageid)
    {
        $userGroupPermissions = $this->getPermissionsForUserGroup($uid);
        if ($userGroupPermissions < 0) {
            return -1;
        }
        $ds = Wekit::load('design.PwDesignPermissions');
         
        $vo = new PwDesignPermissionsSo();
        $vo->setDesignType(PwDesignPermissions::TYPE_PAGE)
            ->setDesignId($pageid)
            ->setUid($uid);
        $info = $ds->searchPermissions($vo);
        $info = array_shift($info);
        if (isset($info['permissions'])) {
            return $info['permissions'];
        }

        return $userGroupPermissions;
    }

    /**
     * 判断用户的某个模块权限.
     *
     * @param int $uid
     * @param int $moduleid
     * @param int $pageid
     */
    public function getPermissionsForModule($uid, $moduleid, $pageid = 0)
    {
        $ds = Wekit::load('design.PwDesignPermissions');
         
        $vo = new PwDesignPermissionsSo();
        $vo->setDesignType(PwDesignPermissions::TYPE_MODULE)
            ->setDesignId($moduleid)
            ->setUid($uid);
        $permissions = $ds->searchPermissions($vo);
        if ($permissions) {
            $permissions = array_shift($permissions);
            if (isset($permissions['permissions'])) {
                return $permissions['permissions'];
            }
        }

        if ($pageid) {
            return $this->getPermissionsForPage($uid, $pageid);
        }

        return $this->getPermissionsForUserGroup($uid);
    }

    /**
     * 获取用户有权限的所有页面.
     */
    public function getPermissionsAllPage($uid)
    {
        $pagelist = Wekit::load('design.PwDesignPage')->getPageList(PwDesignPage::PORTAL | PwDesignPage::SYSTEM);
        $userGroupPermissions = $this->getPermissionsForUserGroup($uid);
        if ($userGroupPermissions > 0) {
            return $pagelist;
        }
        /* 
        $vo = new PwDesignPermissionsSo();
        $vo->setDesignType(PwDesignPermissions::TYPE_PAGE)
            ->setUid($uid);
        return $ds->searchPermissions($vo);*/
    }
}
