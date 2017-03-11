<?php

/**
 * @提醒Controller
 *
 * @author jinlong.panjl <jinlong.panjl@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id$
 */
class RemindController extends PwBaseController
{
    public function beforeAction($handlerAdapter)
    {
        parent::beforeAction($handlerAdapter);
        if ($this->loginUser->uid < 1) {
            $this->showError('login.not');
        }
        if ($this->loginUser->getPermission('remind_open') < 1) {
            $this->showError('bbs:remind.remind_open.error');
        }
    }

    /**
     * @下拉获取用户数据
     */
    public function run()
    {
        $username = $this->getInput('username');
        if (!$username) {
            $remindData = $this->_getRemindDs()->getByUid($this->loginUser->uid);
            $remindData && $reminds = unserialize($remindData['touid']);
            $count = count($reminds);
            $count < 10 && $num = 10 - $count;
        }
        if ($username || $num) {
            $count = $this->loginUser->info['follows'];
            if ($count) {
                $num = $num ? $num : 2000;
                $follows = $this->_getAttentionDs()->getFollows($this->loginUser->uid, $num);
                $follows = array_keys($follows);
            }
        }
        $uids = array_unique(array_merge((array) $reminds, (array) $follows));
        Pw::echoJson(array('state' => 'success', 'data' => $this->_buildRemindUsers($uids)));
        exit;
    }

    /**
     * @提醒获取好友弹窗
     */
    public function friendAction()
    {
        $remindData = $this->_getRemindDs()->getByUid($this->loginUser->uid);
        $remindData && $uids = unserialize($remindData['touid']);
        $reminds = $this->_buildRemindUsers($uids);
        $typeArr = $this->_getAttentionService()->getAllType($this->loginUser->uid);
        $todayNum = $this->_getRemindToday();

        $this->setOutput($todayNum, 'todayNum');
        $this->setOutput($reminds, 'reminds');
        $this->setOutput($typeArr, 'typeArr');
    }

    /**
     * 获取用户关注数据，ajax输出.
     */
    public function getfollowAction()
    {
        list($type, $page, $perpage) = $this->getInput(array('type', 'page', 'perpage'));
        $page = $page ? $page : 1;
        $perpage = $perpage ? $perpage : $this->perpage;
        list($start, $limit) = Pw::page2limit($page, $perpage);
        $typeCounts = $this->_getAttentionTypeDs()->countUserType($this->loginUser->uid);

        if ($type) {
            $tmp = $this->_getAttentionTypeDs()->getUserByType($this->loginUser->uid, $type, $limit, $start);
            $follows = $this->_getAttentionDs()->fetchFollows($this->loginUser->uid, array_keys($tmp));
            $count = $typeCounts[$type] ? $typeCounts[$type]['count'] : 0;
        } else {
            $follows = $this->_getAttentionDs()->getFollows($this->loginUser->uid, $limit, $start);
            $count = $this->loginUser->info['follows'];
        }
        $uids = array_keys($follows);
        Pw::echoJson(array('state' => 'success', 'data' => $this->_buildRemindUsers($uids), 'page' => $page));
        exit;
    }

    /**
     * 组装用户.
     */
    private function _buildRemindUsers($uids)
    {
        $userList = $this->_getUserDs()->fetchUserByUid($uids, PwUser::FETCH_MAIN);
        $users = array();
        foreach ($uids as $v) {
            if (!isset($userList[$v]['username'])) {
                continue;
            }
            $users[$v] = $userList[$v]['username'];
        }

        return $users;
    }

    private function _getRemindToday()
    {
        $maxNum = $this->loginUser->getPermission('remind_max_num');
        if ($maxNum < 1) {
            return '';
        }
        $behavior = $this->_getUserBehaviorDs()->getBehavior($this->loginUser->uid, 'remind_today');
        $todayNum = $maxNum - $behavior['number'];

        return $todayNum > 0 ? $todayNum : 0;
    }

    /**
     * PwAttentionService.
     *
     * @return PwAttentionService
     */
    private function _getAttentionService()
    {
        return Wekit::load('attention.srv.PwAttentionService');
    }

    /**
     * @return PwAttentionType
     */
    private function _getAttentionTypeDs()
    {
        return Wekit::load('attention.PwAttentionType');
    }

    /**
     * @return PwRemind
     */
    private function _getRemindDs()
    {
        return Wekit::load('remind.PwRemind');
    }

    /**
     * PwUserBehavior.
     *
     * @return PwUserBehavior
     */
    private function _getUserBehaviorDs()
    {
        return Wekit::load('user.PwUserBehavior');
    }

    /**
     * PwAttention.
     *
     * @return PwAttention
     */
    private function _getAttentionDs()
    {
        return Wekit::load('attention.PwAttention');
    }

    /**
     * @return PwUser
     */
    protected function _getUserDs()
    {
        return Wekit::load('user.PwUser');
    }
}
