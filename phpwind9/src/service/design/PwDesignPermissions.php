<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>.
 *
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwDesignPermissions.php 17399 2012-09-05 07:12:51Z gao.wanggao $
 */
class PwDesignPermissions
{
    const TYPE_PAGE = 1;
    const TYPE_MODULE = 2;

    const IS_DESIGN = 4;
    const IS_ADMIN = 3;
    const IS_PUSH = 2;
    const NEED_CHECK = 1;

    public function getInfo($id)
    {
        $id = (int) $id;
        if ($id < 1) {
            return [];
        }

        return $this->_getDao()->get($id);
    }

    public function searchPermissions(PwDesignPermissionsSo $vo)
    {
        return $this->_getDao()->search($vo->getData());
    }

    public function addInfo($designType, $designId, $uid, $permissions = self::IS_ADMIN)
    {
        $designType = (int) $designType;
        $designId = (int) $designId;
        $uid = (int) $uid;
        $permissions = (int) $permissions;
        if ($designType < 1 || $designId < 1 || $uid < 1 || $permissions < 1) {
            return false;
        }
        $data['design_type'] = $designType;
        $data['design_id'] = $designId;
        $data['uid'] = $uid;
        $data['permissions'] = $permissions;

        return $this->_getDao()->add($data);
    }

    public function updatePermissions($id, $permissions = self::IS_ADMIN)
    {
        $id = (int) $id;
        $permissions = (int) $permissions;
        if ($id < 1 || $permissions < 1) {
            return false;
        }

        return $this->_getDao()->updatePermissions($id, $permissions);
    }

    public function deleteInfo($id)
    {
        $id = (int) $id;
        if ($id < 1) {
            return false;
        }

        return $this->_getDao()->delete($id);
    }

    public function batchDelete($ids)
    {
        if (!is_array($ids) || !$ids) {
            return false;
        }

        return $this->_getDao()->batchDelete($ids);
    }

    public function deleteByTypeAndDesignId($type, $id)
    {
        $id = (int) $id;
        if ($id < 1 || $type < 1) {
            return false;
        }

        return $this->_getDao()->deleteByTypeAndDesignId($type, $id);
    }

    private function _getDao()
    {
        return Wekit::loadDao('design.dao.PwDesignPermissionsDao');
    }
}
