<?php
/**
 * 用户组数据模型
 *
 * @author peihong.zhangph <peihong.zhangph@aliyun-inc.com> Nov 1, 2011
 * @link http://www.phpwind.com
 * @copyright 2011 phpwind.com
 * @license
 * @version $Id: PwUserGroupDm.php 16536 2012-08-24 07:59:16Z peihong.zhangph $
 */



class PwUserGroupDm extends PwBaseDm
{
    private $groupType = 'member';
    private $gid;

    public function __construct($gid = 0)
    {
        $gid = intval($gid);
        if ($gid < 1) {
            return;
        }
        $this->gid = $gid;
    }

    public function setGroupName($groupName)
    {
        $this->_data['name'] = $groupName;
    }

    public function setGroupImage($groupImage)
    {
        $this->_data['image'] = $groupImage;
    }

    public function setGroupPoints($points)
    {
        $points = intval($points);
        $this->_data['points'] = $points;
    }

    public function setGroupType($groupType)
    {
        $ds = $this->loadDataService();
        if (!in_array($groupType, $ds->getGroupTypes())) {
            return false;
        }
        $this->_data['type'] = $groupType;
    }

    public function getGroupId()
    {
        return $this->gid;
    }

    /**
     *
     * 添加用户组校验
     */
    protected function _beforeAdd()
    {
        if (!$this->_data['name']) {
            return new PwError('USER:groups.info.name.empty');
        }

        return true;
    }

    protected function _beforeUpdate()
    {
        if ($this->gid < 1) {
            return new PwError('USER:groups.info.gid.error');
        } elseif (!$this->_data['name']) {
            return new PwError('USER:groups.info.name.empty');
        }

        return true;
    }

    protected function loadDataService()
    {
        return Wekit::load('usergroup.PwUserGroups');
    }
}
