<?php

Wind::import('ADMIN:library.AdminBaseController');
/**
 * 后台消息管理Controller.
 *
 * @author jinlong.panjl <jinlong.panjl@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id$
 */
class ManageController extends AdminBaseController
{
    private $perpage = 20;
    private $perstep = 10;

    public function run()
    {
        list($page, $perpage, $username, $starttime, $endtime, $keyword) = $this->getInput(array('page', 'perpage', 'username', 'starttime', 'endtime', 'keyword'));
        $starttime && $pwStartTime = Pw::str2time($starttime);
        $endtime && $pwEndTime = Pw::str2time($endtime);
        $page = $page ? $page : 1;
        $perpage = $perpage ? $perpage : $this->perpage;
        list($start, $limit) = Pw::page2limit($page, $perpage);
        if ($username) {
            $userinfo = $this->_getUserDs()->getUserByName($username);
            $fromUid = $userinfo['uid'] ? $userinfo['uid'] : 0;
        }
        list($count, $messages) = $this->_getMessageService()->getMessagesByUid($start, $limit, $fromUid, $pwStartTime, $pwEndTime, $keyword);
        $this->setOutput($count, 'count');
        $this->setOutput($page, 'page');
        $this->setOutput($perpage, 'perpage');
        $this->setOutput(array('keyword' => $keyword, 'username' => $username, 'starttime' => $starttime, 'endtime' => $endtime), 'args');
        $this->setOutput($messages, 'messages');
    }

    /**
     * 删除消息.
     */
    public function deleteMessagesAction()
    {
        $ids = $this->getInput('ids');
        if (!$ids) {
            $this->showError('Message:message.id.empty');
        }
        $this->_getMessageService()->deleteMessageByMessageIds($ids);
        $this->showMessage('ADMIN:success');
    }

    /**
     * 发消息.
     */
    public function addAction()
    {
        $userGroupService = Wekit::load('usergroup.PwUserGroups');
        $userGroups = $userGroupService->getClassifiedGroups();
    }

    /**
     * 群发消息.
     */
    public function sendAction()
    {
        // 用户组
        $userGroup = Wekit::load('usergroup.PwUserGroups');
        $groups = $userGroup->getAllGroups();
        $groupTypes = $userGroup->getTypeNames();
        $memberGroupTypes = $groupGroupTypes = array();
        foreach ($groups as $key => $group) {
            if ($group['type'] == 'member') {
                $group['grouptype'] = 'memberid';
                $members[$key] = $group;
                $memberGroupTypes[$group['type']] = $groupTypes[$group['type']];
            } else {
                $group['grouptype'] = 'groupid';
                $othergroup[$key] = $group;
                $groupGroupTypes = array_diff_key($groupTypes, $memberGroupTypes);
            }
        }
        $this->setOutput($members, 'members');
        $this->setOutput($othergroup, 'othergroup');
        $this->setOutput($memberGroupTypes, 'memberGroupTypes');
        $this->setOutput($groupGroupTypes, 'groupGroupTypes');
    }

    /**
     * do群发消息.
     */
    public function doSendAction()
    {
        list($type, $content, $title, $step, $countStep) = $this->getInput(array('type', 'content', 'title', 'step', 'countStep'));
        !$content && $this->showError('Message:content.empty');
        if ($step > $countStep) {
            $this->showMessage('ADMIN:success');
        }
        $step = $step ? $step : 1;
        switch ($type) {
            case 1:  // 根据用户组
                list($user_groups, $grouptype) = $this->getInput(array('user_groups', 'grouptype'));
                Wind::import('SRV:user.vo.PwUserSo');
                $vo = new PwUserSo();
                $searchDs = Wekit::load('SRV:user.PwUserSearch');
                if (!$user_groups) {
                    $this->showError('Message:user.groups.empty');
                }
                if ($grouptype == 'memberid') {
                    $vo->setMemberid($user_groups);
                } else {
                    $vo->setGid($user_groups);
                }
                $count = $searchDs->countSearchUser($vo);
                $countStep = ceil($count / $this->perstep);
                if ($step <= $countStep) {
                    list($start, $limit) = Pw::page2limit($step, $this->perstep);
                    $userInfos = $searchDs->searchUser($vo, $limit, $start);
                }
                break;
            case 2:  // 根据用户名
                $touser = $this->getInput('touser');
                !$touser && $this->showError('Message:receive.user.empty');
                $touser = explode(' ', $touser);
                $count = count($touser);
                $countStep = ceil($count / $this->perstep);
                if ($step <= $countStep) {
                    $userDs = Wekit::load('user.PwUser');
                    list($start, $limit) = Pw::page2limit($step, $this->perstep);
                    $userInfos = $userDs->fetchUserByName(array_slice($touser, $start, $limit));
                }
                break;
            case 3:  // 根据在线用户(精确在线)
                $onlineService = Wekit::load('online.srv.PwOnlineCountService');
                list($count, $userInfos) = $onlineService->getVisitorList('', $step, $this->perstep, true);
                $countStep = ceil($count / $this->perstep);
                break;
        }
        $result = $this->sendNoticeByUsers((array) $userInfos, $content, strip_tags($title));
        if ($result instanceof PwError) {
            $this->showError($result->getError());
        }
        $haveBuild = $step * $this->perstep;
        $haveBuild = ($haveBuild > $count) ? $count : $haveBuild;
        $step++;
        usleep(500);
        $data = array('step'    => $step,
                    'countStep' => $countStep,
                    'count'     => $count,
                    'haveBuild' => $haveBuild,
                );
        Pw::echoJson(array('data' => $data));
        exit;
    }

    private function sendNoticeByUsers($userInfos, $content, $title)
    {
        if (!$userInfos) {
            return new PwError('Message:user.notfound');
        }
        $notice = Wekit::load('message.srv.PwNoticeService');
        foreach ($userInfos as $userInfo) {
            $extendParams = array(
                'username' => $userInfo['username'],
                'title'    => $title,
                'content'  => $content,
            );
            $notice->sendNotice($userInfo['uid'], 'massmessage', '', $extendParams);
        }

        return true;
    }

    /**
     * Enter description here ...
     *
     * @return PwMessageService
     */
    private function _getMessageService()
    {
        return Wekit::load('message.srv.PwMessageService');
    }

    /**
     * Enter description here ...
     *
     * @return PwUser
     */
    private function _getUserDs()
    {
        return Wekit::load('user.PwUser');
    }
}
