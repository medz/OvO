<?php

defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('ADMIN:library.AdminBaseController');

/**
 * 投票系统后台.
 *
 * @author mingxing.sun <mingxing.sun@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 *
 * @version $Id: ManageController.php 4731 2012-02-23 08:36:01Z mingxing.sun $
 */
class ManageController extends AdminBaseController
{
    public function run()
    {
        $map = (array) $this->_getFroumService()->getForumMap();
        $forumList = [];
        foreach ($map[0] as $key => $value) {
            $forumList[$value['fid']] = $this->_getFroumService()->getForumsByLevel($value['fid'], $map);
        }

        $forumIds = [];
        foreach ($forumList as $forum) {
            foreach ($forum as $value) {
                $forumIds[] = $value['fid'];
            }
        }

        $pollOpenForum = $this->_getPollOpenForum($forumIds);

        $cateIds = [];
        foreach ($map[0] as $value) {
            foreach ($forumList[$value['fid']] as $val) {
                if (! in_array($val['fid'], array_keys($pollOpenForum))) {
                    continue;
                }
                $cateIds[] = $value['fid'];
            }
        }
        $cateIds = array_unique($cateIds);

        $this->setOutput($forumList, 'forumList');
        $this->setOutput($map[0], 'cateList');
        $this->setOutput($cateIds, 'cateIds');
        $this->setOutput($pollOpenForum, 'pollOpenForum');
        $this->setOutput($this->_buildGroup($this->_getUserGroupsDs()->getAllGroups()), 'groups');
        $this->setOutput($this->_getUserGroupsDs()->getTypeNames(), 'groupsTypeName');
        $this->setOutput($this->_buildPermission($this->_getUserPermissionDs()->fetchPermissionByRkey(['allow_add_vote', 'allow_participate_vote', 'allow_view_vote'])), 'permission');
    }

    public function dogroupAction()
    {
        $this->getRequest()->isPost() || $this->showError('operate.fail');
        $view = $this->getInput('view', 'post');

        $groups = $this->_getUserGroupsDs()->getAllGroups();

        foreach ($groups as $value) {
            $dm = new PwUserPermissionDm($value['gid']);
            $dm->setPermission('allow_add_vote', in_array($value['gid'], $view['allow_add_vote']) ? 1 : 0);
            $dm->setPermission('allow_participate_vote', in_array($value['gid'], $view['allow_participate_vote']) ? 1 : 0);
            $dm->setPermission('allow_view_vote', in_array($value['gid'], $view['allow_view_vote']) ? 1 : 0);
            $this->_getUserPermissionDs()->setPermission($dm);
        }

        $this->showMessage('ADMIN:success');
    }

    public function editforumAction()
    {
        $map = (array) $this->_getFroumService()->getForumMap();
        $forumList = [];
        foreach ($map[0] as $key => $value) {
            $forumList[$value['fid']] = $this->_getFroumService()->getForumsByLevel($value['fid'], $map);
        }

        $forumIds = [];
        foreach ($forumList as $forum) {
            foreach ($forum as $value) {
                $forumIds[] = $value['fid'];
            }
        }

        $pollOpenForum = $this->_getPollOpenForum($forumIds);

        $this->setOutput($forumList, 'forumList');
        $this->setOutput($map[0], 'cateList');
        $this->setOutput($pollOpenForum, 'pollOpenForum');
    }

    public function doeditforumAction()
    {
        $this->getRequest()->isPost() || $this->showError('operate.fail');
        $forumid = $this->getInput('fid', 'post');
        $forumid = $forumid ? explode(',', $forumid) : [];

        $forum = (array) $this->_getFroumService()->getForumList();

        $openForum = $noOpenForum = [];
        foreach ($forum as $value) {
            if ($value['type'] == 'category') {
                continue;
            }
            if (in_array($value['fid'], $forumid)) {
                $openForum[] = $value['fid'];
            } else {
                $noOpenForum[] = $value['fid'];
            }
        }

        if ($openForum) {
            foreach ($openForum as $value) {
                $_forum = $this->_getForumDs()->getForum($value, 4);
                if (! $_forum) {
                    continue;
                }

                $setting = unserialize($_forum['settings_basic']);
                $allowType = is_array($setting['allowtype']) ? $setting['allowtype'] : [];
                if (in_array('poll', $allowType)) {
                    continue;
                }
                $allowType[] = 'poll';
                ! isset($setting['typeorder']['poll']) && $setting['typeorder']['poll'] = 0;
                $setting['allowtype'] = $allowType;

                $dm = new PwForumDm($value);
                $dm->setBasicSetting($setting);
                $this->_getForumDs()->updateForum($dm, 4);
            }
        }

        if ($noOpenForum) {
            foreach ($noOpenForum as $value) {
                $_forum = $this->_getForumDs()->getForum($value, 4);
                if (! $_forum) {
                    continue;
                }

                $setting = unserialize($_forum['settings_basic']);
                $allowType = is_array($setting['allowtype']) ? $setting['allowtype'] : [];
                if (! in_array('poll', $allowType)) {
                    continue;
                }

                $allowType = array_diff($allowType, ['poll']);
                unset($setting['typeorder']['poll']);
                $setting['allowtype'] = $allowType;

                $dm = new PwForumDm($value);
                $dm->setBasicSetting($setting);
                $this->_getForumDs()->updateForum($dm, 4);
            }
        }

        $this->showMessage('ADMIN:success');
    }

    private function _buildGroup($data)
    {
        if (empty($data) || ! is_array($data)) {
            return [];
        }

        $result = [];
        foreach ($data as $value) {
            if ($value['type'] == 'vip') {
                continue;
            }
            $result[$value['type']][] = $value;
        }

        return $result;
    }

    private function _buildPermission($data)
    {
        if (empty($data) || ! is_array($data)) {
            return [];
        }

        $result = [];
        foreach ($data as $value) {
            if (! $value['rvalue']) {
                continue;
            }
            $result[$value['rkey']][] = $value['gid'];
        }

        return $result;
    }

    private function _getPollOpenForum($forumIds)
    {
        if (empty($forumIds) || ! is_array($forumIds)) {
            return [];
        }

        $forumExtra = $this->_getForumDs()->fetchForum($forumIds, 4);

        $result = [];
        foreach ($forumExtra as $value) {
            $setting = (array) unserialize($value['settings_basic']);
            $allowType = $setting['allowtype'];
            if (is_array($allowType) && in_array('poll', $allowType)) {
                $result[$value['fid']] = $value['fid'];
            }
        }

        return $result;
    }

    /**
     * get PwUserGroups.
     *
     * @return PwUserGroups
     */
    protected function _getUserGroupsDs()
    {
        return Wekit::load('usergroup.PwUserGroups');
    }

    /**
     * get PwForumService.
     *
     * @return PwForumService
     */
    protected function _getFroumService()
    {
        return Wekit::load('forum.srv.PwForumService');
    }

    /**
     * get PwUserPermission.
     *
     * @return PwUserPermission
     */
    protected function _getUserPermissionDs()
    {
        return Wekit::load('usergroup.PwUserPermission');
    }

    /**
     * get PwForum.
     *
     * @return PwForum
     */
    protected function _getForumDs()
    {
        return Wekit::load('forum.PwForum');
    }
}
