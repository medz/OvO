<?php

Wind::import('ADMIN:library.AdminBaseController');
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>.
 *
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PermissionsController.php 28818 2013-05-24 10:10:46Z gao.wanggao $
 */
class PermissionsController extends AdminBaseController
{
    public function run()
    {
        $username = $this->getInput('username', 'post');
        $ds = $this->_getPermissionsDs();
        if ($username) {
            $user = Wekit::load('user.PwUser')->getUserByName($username);
            $uid = isset($user['uid']) ? $user['uid'] : 0;
            if ($uid < 1) {
                $this->showError('permission.design.uid.empty');
            }
        }
        Wind::import('SRV:design.srv.vo.PwDesignPermissionsSo');
        $vo = new PwDesignPermissionsSo();
        if ($uid) {
            $vo->setUid($uid);
        }
        $_tmp = $ds->searchPermissions($vo);
        $_gids = $_uids = array();
        foreach ($_tmp as $v) {
            $_uids[] = $v['uid'];
        }
        array_unique($_uids);
        $users = Wekit::load('user.PwUser')->fetchUserByUid($_uids, PwUser::FETCH_MAIN);
        foreach ($users as &$user) {
            $user['gid'] = ($user['groupid'] == 0) ? $user['memberid'] : $user['groupid'];
            $_gids[] = $user['gid'];
        }
        array_unique($_gids);
        $groups = Wekit::load('usergroup.PwUserGroups')->fetchGroup($_gids);
        $this->setOutput($users, 'users');
        $this->setOutput($groups, 'groups');
    }

    public function viewAction()
    {
        $uid = (int) $this->getInput('uid', 'get');
        if ($uid < 1) {
            $this->showError('permission.design.uid.empty');
        }
        Wind::import('SRV:design.srv.vo.PwDesignPermissionsSo');
        $vo = new PwDesignPermissionsSo();
        $vo->setUid($uid);
        $list = $this->_getPermissionsDs()->searchPermissions($vo);
        $_ids = array();
        foreach ($list as $v) {
            $_ids[$v['design_type']][$v['id']] = $v['design_id'];
        }
        foreach ($_ids as $k => $ids) {
            if ($k == PwDesignPermissions::TYPE_PAGE) {
                $info = $this->_getPageDs()->fetchPage($ids);
                foreach ($ids as $_k => $id) {
                    $list[$_k]['type'] = '页面';
                    $list[$_k]['name'] = $info[$id]['page_name'];
                    $list[$_k]['url'] = WindUrlHelper::createUrl('design/permissions/page', array('id' => $info[$id]['page_id']));
                }
            }
            if ($k == PwDesignPermissions::TYPE_MODULE) {
                $info = $this->_getModuleDs()->fetchModule($ids);
                foreach ($ids as $_k => $id) {
                    $list[$_k]['type'] = '模块';
                    $list[$_k]['name'] = $info[$id]['module_name'];
                    $list[$_k]['url'] = WindUrlHelper::createUrl('design/permissions/module', array('moduleid' => $info[$id]['module_id']));
                }
            }
            /*
            if ($k == PwDesignPermissions::TYPE_PORTAL) {
                $info = $this->_getPageDs()->fetchPage($ids);
                foreach ($ids AS $_k=>$id) {
                    $list[$_k]['type'] = '页面';
                    $list[$_k]['name'] = $info[$id]['page_name'];
                    $list[$_k]['url'] = WindUrlHelper::createUrl('design/permissions/page', array('id' => $info[$id]['page_id']));
                }
            }*/
        }
        $user = Wekit::load('user.PwUser')->getUserByUid($uid);
        $user['gid'] = ($user['groupid'] == 0) ? $user['memberid'] : $user['groupid'];
        $group = Wekit::load('usergroup.PwUserGroups')->getGroupByGid($user['gid']);
        $user['groupname'] = $group['name'];
        $this->setOutput($list, 'list');
        $this->setOutput($user, 'user');
    }

    public function pageAction()
    {
        $uids = array();
        $designId = (int) $this->getInput('id', 'get');
        $pageInfo = $this->_getPageDs()->getPage($designId);
        if (!$pageInfo) {
            $this->showError('operate.fail');
        }
        $ds = $this->_getPermissionsDs();
        $type = ($pageInfo['page_type'] == PwDesignPage::PORTAL) ? 0 : 1;
        Wind::import('SRV:design.srv.vo.PwDesignPermissionsSo');
        $vo = new PwDesignPermissionsSo();
        $vo->setDesignType(PwDesignPermissions::TYPE_PAGE)
           ->setDesignId($designId);
        $list = $ds->searchPermissions($vo);
        foreach ($list as $v) {
            $uids[] = $v['uid'];
        }
        $users = Wekit::load('user.PwUser')->fetchUserByUid($uids, PwUser::FETCH_MAIN);
        $this->setOutput($list, 'list');
        $this->setOutput($users, 'users');
        $this->setOutput($designId, 'designId');
        $this->setOutput($type, 'type');
        $this->setOutput(PwDesignPermissions::TYPE_PAGE, 'pType');

        $this->setOutput($this->_getPageDs()->getPage($designId), 'info');
    }

    public function moduleAction()
    {
        $uids = array();
        $designId = (int) $this->getInput('moduleid', 'get');
        if ($designId < 1) {
            $this->showError('operate.fail');
        }
        $ds = $this->_getPermissionsDs();
        Wind::import('SRV:design.srv.vo.PwDesignPermissionsSo');
        $vo = new PwDesignPermissionsSo();
        $vo->setDesignType(PwDesignPermissions::TYPE_MODULE)
           ->setDesignId($designId);
        $list = $ds->searchPermissions($vo);
        foreach ($list as $v) {
            $uids[] = $v['uid'];
        }
        $users = Wekit::load('user.PwUser')->fetchUserByUid($uids, PwUser::FETCH_MAIN);
        $moduleinfo = $this->_getModuleDs()->getModule($designId);
        $this->setOutput($list, 'list');
        $this->setOutput($users, 'users');
        $this->setOutput($designId, 'designId');
        $this->setOutput(PwDesignPermissions::TYPE_MODULE, 'pType');
        $this->setOutput($moduleinfo, 'info');
    }

    public function doeditAction()
    {
        $designId = (int) $this->getInput('design_id', 'post');
        $designType = (int) $this->getInput('design_type', 'post');
        $new_permissions = $this->getInput('new_permissions', 'post');
        $new_username = $this->getInput('new_username', 'post');
        $ids = $this->getInput('ids', 'post');
        $permissions = $this->getInput('permissions', 'post');
        $fail = 0;
        $ds = $this->_getPermissionsDs();
        //添加新用户  前端已修改为单用户提交
        if ($new_username) {
            Wind::import('SRV:design.srv.vo.PwDesignPermissionsSo');
            Wind::import('SRV:user.bo.PwUserBo');
            $service = $this->_getPermissionsService();
            foreach ($new_username as $k => $name) {
                if (!$name) {
                    continue;
                }
                $user = Wekit::load('user.PwUser')->getUserByName($name);
                $new_uid = isset($user['uid']) ? $user['uid'] : 0;
                if ($new_uid < 1) {
                    $this->showError('DESIGN:user.name.error');
                }
                $vo = new PwDesignPermissionsSo();
                $vo->setDesignId($designId)
                    ->setDesignType($designType)
                    ->setUid($new_uid);
                $list = $ds->searchPermissions($vo);
                if ($list) {
                    $this->showError('DESIGN:user.already.permissions');
                }
                if ($service->getPermissionsForUserGroup($new_uid) < 0) {
                    $this->showError('DESIGN:user.group.error');
                }
                $userBo = new PwUserBo($new_uid);
                $designPermission = $userBo->getPermission('design_allow_manage.push');
                if ($designPermission < 1) {
                    $this->showError('DESIGN:user.group.error');
                }
                $resource = $ds->addInfo($designType, $designId, $new_uid, $new_permissions[$k]);
                if (!$resource) {
                    $fail++;
                }
            }
        }
        foreach ($ids as $k => $id) {
            $resource = $ds->updatePermissions($id, $permissions[$k]);
            if (!$resource) {
                $fail++;
            }
        }
        $this->showMessage('operate.success');
    }

    public function deleteAction()
    {
        $id = (int) $this->getInput('id', 'post');
        $ds = $this->_getPermissionsDs();
        $info = $ds->getInfo($id);
        if (!$info) {
            $this->showError('operate.fail');
        }
        $ds->deleteInfo($id);
        $this->showMessage('operate.success');
    }

    public function batchdeleteAction()
    {
        $deleteIds = $this->getInput('del_ids', 'post');
        $resource = $this->_getPermissionsDs()->batchDelete($deleteIds);
        $this->showMessage('operate.success');
    }

    private function _getPermissionsService()
    {
        return Wekit::load('design.srv.PwDesignPermissionsService');
    }

    private function _getPermissionsDs()
    {
        return Wekit::load('design.PwDesignPermissions');
    }

    private function _getPageDs()
    {
        return Wekit::load('design.PwDesignPage');
    }

    private function _getPortalDs()
    {
        return Wekit::load('design.PwDesignPortal');
    }

    private function _getModuleDs()
    {
        return Wekit::load('design.PwDesignModule');
    }
}
