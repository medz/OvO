<?php

defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 用户投票处理层
 *
 * @author MingXing Sun <mingxing.sun@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: VoteController.php 24134 2013-01-22 06:19:24Z xiaoxia.xuxx $
 */
class VoteController extends PwBaseController
{
    public function beforeAction($handlerAdapter)
    {
        parent::beforeAction($handlerAdapter);
        if (!$this->loginUser->isExists()) {
            $this->showError('VOTE:user.not.login');
        }
    }

    public function run()
    {
        if (!$this->loginUser->getPermission('allow_participate_vote')) {
            $this->showError('VOTE:group.not.allow.participate');
        }

        list($appType, $typeid, $optionid) = $this->getInput(['apptype', 'typeid', 'optionid']);
        if (empty($optionid) || !is_array($optionid)) {
            $this->showError('VOTE:not.select.option');
        }

        $poll = $this->_serviceFactory($appType, $typeid);

        if (($result = $poll->check()) !== true) {
            $this->showError($result->getError());
        }

        if (!$poll->isInit()) {
            $this->showError('VOTE:thread.not.exist');
        }
        if ($poll->isExpired()) {
            $this->showError('VOTE:vote.activity.end');
        }
        $regtimeLimit = $poll->getRegtimeLimit();
        if ($regtimeLimit && $this->loginUser->info['regdate'] > $regtimeLimit) {
            $this->showError(['VOTE:vote.regtime.limit', ['{regtimelimit}' => pw::time2str($regtimeLimit, 'Y-m-d')]]);
        }

        if (($result = $this->_getPollService()->doVote($this->loginUser->uid, $poll->info['poll_id'], $optionid)) !== true) {
            $this->showError($result->getError());
        }

        $this->showMessage('VOTE:vote.success');
    }

    public function forumlistAction()
    {
        $forums = Wekit::load('forum.PwForum')->getForumList(PwForum::FETCH_ALL);
        $service = Wekit::load('forum.srv.PwForumService');
        $map = $service->getForumMap();
        $cate = [];
        $forum = [];
        foreach ($map[0] as $key => $value) {
            if (!$value['isshow']) {
                continue;
            }
            $array = $service->findOptionInMap($value['fid'], $map, ['sub' => '--', 'sub2' => '----']);
            $tmp = [];

            foreach ($array as $k => $v) {
                $forumset = $forums[$k]['settings_basic'] ? unserialize($forums[$k]['settings_basic']) : [];
                $isAllowPoll = isset($forumset['allowtype']) && is_array($forumset['allowtype']) && in_array('poll', $forumset['allowtype']);

                if ($forums[$k]['isshow'] && $isAllowPoll && (!$forums[$k]['allow_post'] || $this->loginUser->inGroup(explode(',', $forums[$k]['allow_post'])))) {
                    $tmp[$k] = strip_tags($v);
                }
            }

            if ($tmp) {
                $cate[$value['fid']] = $value['name'];
                $forum[$value['fid']] = $tmp;
            }
        }

        $response = [
            'cate'  => $cate,
            'forum' => $forum,
        ];

        $this->setOutput(Pw::jsonEncode($response), 'data');
        $this->showMessage('success');
    }

    private function _serviceFactory($appType, $typeid)
    {
        switch ($appType) {
            case '0':

                $bo = new PwThreadPollBo($typeid);
                break;

            default:

                $bo = new PwThreadPollBo($typeid);
                break;
        }

        return $bo;
    }

    /**
     * get PwPollService.
     *
     * @return PwPollService
     */
    private function _getPollService()
    {
        return Wekit::load('poll.srv.PwPollService');
    }
}
