<?php

Wind::import('ADMIN:library.AdminBaseController');

/**
 * 举报管理.
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
    private $_maxUids = 30;

    /**
     * 举报管理.
     */
    public function run()
    {
        list($page, $perpage, $ifcheck, $type) = $this->getInput(['page', 'perpage', 'ifcheck', 'type']);
        $page = $page ? $page : 1;
        $perpage = $perpage ? $perpage : $this->perpage;
        list($start, $limit) = Pw::page2limit($page, $perpage);

        $count = $this->_getReportDs()->countByType($ifcheck, $type);
        if ($count) {
            $reports = $this->_getReportService()->getReceiverList($ifcheck, $type, $limit, $start);
        }
        $reportTypes = $this->_getReportService()->getTypeName();
        $this->setOutput($reportTypes, 'reportTypes');
        $this->setOutput($page, 'page');
        $this->setOutput($perpage, 'perpage');
        $this->setOutput($count, 'count');
        $this->setOutput($reports, 'reports');
        $this->setOutput(['ifcheck' => $ifcheck, 'type' => $type], 'args');
    }

    /**
     * 忽略.
     */
    public function deleteAction()
    {
        $id = $this->getInput('id', 'post');
        if (! $id) {
            $this->showError('operate.select');
        }
        ! is_array($id) && $id = [$id];
        $this->_sendDealNotice($id, '忽略');
        $this->_getReportDs()->batchDeleteReport($id);
        $this->showMessage('success');
    }

    private function _buildNoticeTitle($username, $action)
    {
        return '您举报的内容已被 <a href="'.WindUrlHelper::createUrl('space/index/run', ['username' => $username], '', 'pw').'">'.$username.'</a> '.$action.'，感谢您能一起协助我们管理站点。';
    }

    /**
     * 标记处理.
     */
    public function dealCheckAction()
    {
        $id = $this->getInput('id', 'post');
        if (! $id) {
            $this->showError('operate.select');
        }
        ! is_array($id) && $id = [$id];
        $dm = new PwReportDm();
        $dm->setOperateUserid($this->loginUser->uid)
            ->setOperateTime(Pw::getTime())
            ->setIfcheck(1);
        $this->_getReportDs()->batchUpdateReport($id, $dm);
        $this->_sendDealNotice($id, '处理');
        $this->showMessage('success');
    }

    private function _sendDealNotice($ids, $action)
    {
        $reports = $this->_getReportDs()->fetchReport($ids);
        $notice = Wekit::load('message.srv.PwNoticeService');
        $extendParams = [
            'operateUserId'   => $this->loginUser->uid,
            'operateUsername' => $this->loginUser->username,
            'operateTime'     => Pw::getTime(),
            'operateType'     => $action,
        ];
        foreach ($reports as $v) {
            $this->_getReportService()->sendNotice($v, $extendParams);
            $content = $this->_buildNoticeTitle($this->loginUser->username, $action);
            $action == '处理' && $this->_getPwNoticeService()->sendDefaultNotice($v['created_userid'], $content, $content);
        }

        return true;
    }

    /**
     * 接收提醒用户列表.
     */
    public function receiverListAction()
    {
        $uids = $this->_getReportDs()->getNoticeReceiver();
        $receivers = $this->getUsersWithGroup($uids);
        $this->setOutPut($receivers, 'receivers');
    }

    /**
     * 添加接收人.
     */
    public function addReceiverAction()
    {
        $username = $this->getInput('username', 'post');
        ! $username && $this->showError('Report:user.empty');
        $user = $this->_getPwUserDs()->getUserByName($username);
        if (! $user) {
            $this->showError('Report:user.not.presence');
        }
        $uids = $this->_getReportDs()->getNoticeReceiver();
        if (count($uids) >= $this->_maxUids) {
            $this->showError('REPORT:receiver.num.error');
        }
        ! in_array($user['uid'], $uids) && $uids[] = $user['uid'];
        $config = new PwConfigSet('report');
        $config->set('noticeReceiver', $uids)
                ->flush();
        $this->showMessage('success');
    }

    /**
     * do删除.
     */
    public function deleteReceiverAction()
    {
        $uid = (int) $this->getInput('uid', 'post');
        if (! $uid) {
            $this->showError('operate.fail');
        }

        $uids = $this->_getReportDs()->getNoticeReceiver();
        $uids = array_flip($uids);
        unset($uids[$uid]);
        $config = new PwConfigSet('report');
        $config->set('noticeReceiver', array_keys($uids))
                ->flush();
        $this->showMessage('success');
    }

    /**
     * 根据用户uids批量获取用户带身份.
     *
     * @param array $uids
     *
     * @return array
     */
    private function getUsersWithGroup($uids)
    {
        if (! is_array($uids) || ! count($uids)) {
            return [];
        }
        $users = $this->_getPwUserDs()->fetchUserByUid($uids, PwUser::FETCH_MAIN);
        $gids = $receivers = [];
        foreach ($users as $v) {
            $gids[$v['uid']] = ($v['groupid'] == 0) ? $v['memberid'] : $v['groupid'];
        }
        $groupDs = Wekit::load('usergroup.PwUserGroups');
        $groups = $groupDs->fetchGroup($gids);
        foreach ($users as $k => $v) {
            $gid = ($v['groupid'] == 0) ? $v['memberid'] : $v['groupid'];
            $user['username'] = $v['username'];
            $user['uid'] = $v['uid'];
            $user['group'] = $groups[$gid]['name'];
            $receivers[] = $user;
        }

        return $receivers;
    }

    /**
     * @return PwNoticeService
     */
    protected function _getPwNoticeService()
    {
        return Wekit::load('message.srv.PwNoticeService');
    }

    /**
     * @return PwReportReceiverDs
     */
    protected function _getReportReceiverDs()
    {
        return Wekit::load('report.PwReportReceiver');
    }

    /**
     * @return PwReportDs
     */
    protected function _getReportDs()
    {
        return Wekit::load('report.PwReport');
    }

    /**
     * @return PwReportService
     */
    protected function _getReportService()
    {
        return Wekit::load('report.srv.PwReportService');
    }

    /**
     * @return PwUserDs
     */
    protected function _getPwUserDs()
    {
        return Wekit::load('user.PwUser');
    }
}
