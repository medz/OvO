<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>.
 *
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwDesignPermissionsSo.php 11914 2012-06-14 08:32:07Z gao.wanggao $
 */
class PwDesignPermissionsSo
{
    protected $_data = [];

    public function getData()
    {
        return $this->_data;
    }

    public function setDesignType($type)
    {
        $this->_data['design_type'] = (int) $type;

        return $this;
    }

    public function setDesignId($ids)
    {
        if (! $ids) {
            return $this;
        }
        $this->_data['design_id'] = $ids;

        return $this;
    }

    public function setUid($uid)
    {
        $this->_data['uid'] = (int) $uid;

        return $this;
    }

    public function setPermissions($permissions)
    {
        $this->_data['permissions'] = (int) $permissions;

        return $this;
    }
}
