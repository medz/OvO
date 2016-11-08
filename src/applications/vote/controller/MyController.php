<?php

defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('SRV:poll.srv.PwPollDisplay');
Wind::import('SRV:poll.srv.dataSource.PwFetchPollByOrder');
Wind::import('SRV:poll.srv.dataSource.PwFetchPollByUid');

/**
 * 我的投票
 *
 * @author MingXing Sun <mingxing.sun@aliyun-inc.com> 2012-01-12
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: MyController.php 3219 2012-01-12 06:43:45Z mingxing.sun $
 * @package admin
 * @subpackage controller
 */

class MyController extends PwBaseController
{
    public $page = 1;
    public $perpage = 10;

    public function beforeAction($handlerAdapter)
    {
        parent::beforeAction($handlerAdapter);
        if (!$this->loginUser->isExists()) {
            $this->forwardAction('u/login/run', array('backurl' => WindUrlHelper::createUrl('vote/my/run')));
        }
    }

    public function run()
    {
        $page = $this->getInput('page');
        $this->page = $page < 1 ? 1 : intval($page);
        list($start, $limit) = Pw::page2limit($this->page, $this->perpage);

        $total = $this->_getPollVoterDs()->countByUid(Wekit::getLoginUser()->uid);
        $poll = $total ? $this->_getPollVoterDs()->getPollByUid(Wekit::getLoginUser()->uid, $limit, $start) : array();

        $pollInfo = array();

        if ($poll) {
            $pollid = array();
            foreach ($poll as $value) {
                $pollid[] = $value['poll_id'];
            }

            Wind::import('SRV:poll.srv.dataSource.PwFetchPollByPollid');
            $pollDisplay = new PwPollDisplay(new PwFetchPollByPollid($pollid, count($pollid)));
            $pollInfo = $this->_buildPoll($pollDisplay->gather(), 'my');
        }

        $latestPollDisplay = new PwPollDisplay(new PwFetchPollByOrder(10, 0, array('created_time' => '0')));
        $latestPoll = $latestPollDisplay->gather();

        $this->setOutput($total, 'total');
        $this->setOutput($pollInfo, 'pollInfo');
        $this->setOutput($latestPoll, 'latestPoll');
        $this->setOutput($this->page, 'page');
        $this->setOutput($this->perpage, 'perpage');
        $this->setOutput(
            array(
                'allowview' => $this->loginUser->getPermission('allow_view_vote'),
                'allowvote' => $this->loginUser->getPermission('allow_participate_vote'),
            ), 'pollGroup');

        // seo设置
        Wind::import('SRV:seo.bo.PwSeoBo');
        $seoBo = PwSeoBo::getInstance();
        $lang = Wind::getComponent('i18n');
        $seoBo->setCustomSeo($lang->getMessage('SEO:vote.my.run.title'), '', '');
        Wekit::setV('seo', $seoBo);
    }

    public function createAction()
    {
        $page = $this->getInput('page');
        $this->page = $page < 1 ? 1 : intval($page);
        list($start, $limit) = Pw::page2limit($this->page, $this->perpage);

        $total = $this->_getPollDs()->countPollByUid($this->loginUser->uid);

        $pollInfo = array();

        if ($total) {
            $pollDisplay = new PwPollDisplay(new PwFetchPollByUid(Wekit::getLoginUser()->uid, $limit, $start));
            $pollInfo = $this->_buildPoll($pollDisplay->gather());
        }

        $latestPollDisplay = new PwPollDisplay(new PwFetchPollByOrder(10, 0, array('created_time' => '0')));
        $latestPoll = $latestPollDisplay->gather();

        $this->setOutput($total, 'total');
        $this->setOutput($pollInfo, 'pollInfo');
        $this->setOutput($latestPoll, 'latestPoll');
        $this->setOutput($this->page, 'page');
        $this->setOutput($this->perpage, 'perpage');

        $this->setOutput(array(
            'allowview' => $this->loginUser->getPermission('allow_view_vote'),
            'allowvote' => $this->loginUser->getPermission('allow_participate_vote'),
        ), 'pollGroup');

        $this->setOutput(false, 'isPostPollGuide');
    }

    private function isPostPollGuide()
    {
        if (!$this->loginUser->getPermission('allow_add_vote')) {
            return false;
        }
        $forums = Wekit::load('forum.PwForum')->getForumList(PwForum::FETCH_ALL);
        $service = Wekit::load('forum.srv.PwForumService');
        $map = $service->getForumMap();
        $cate = array();
        $forum = array();
        foreach ($map[0] as $key => $value) {
            if (!$value['isshow']) {
                continue;
            }
            $array = $service->findOptionInMap($value['fid'], $map, array());
            $tmp = array();
            foreach ($array as $k => $v) {
                $forumset = $forums[$k]['settings_basic'] ? unserialize($forums[$k]['settings_basic']) : array();
                $isAllowPoll = isset($forumset['allowtype']) && is_array($forumset['allowtype']) && in_array('poll', $forumset['allowtype']);

                if ($forums[$k]['isshow'] && $isAllowPoll && (!$forums[$k]['allow_post'] || $this->loginUser->inGroup(explode(',', $forums[$k]['allow_post'])))) {
                    return true;
                }
            }
        }

        return false;
    }

    private function _buildPoll($data, $action = 'create')
    {
        $reuslt = array();
        switch ($action) {
            case 'create':
                $pollid = $myPollid = array();
                foreach ($data as $value) {
                    $pollid[] = $value['poll_id'];
                }

                $loginUserPollids = $this->_getPollVoterDs()->getPollByUidAndPollid($this->loginUser->uid, $pollid);

                foreach ($data as $value) {
                    $value['isvoted'] = in_array($value['poll_id'], $loginUserPollids) ? true : false;
                    $reuslt[] = $value;
                }

                break;

            case 'my':
                foreach ($data as $value) {
                    $value['isvoted'] = 1;
                    $reuslt[] = $value;
                }
                break;
        }

        return $reuslt;
    }

    /**
     * 获取投票service服务层
     *
     * @return PwPollService
     */
    protected function _getPwPollService()
    {
        return Wekit::load('poll.srv.PwPollService');
    }

    /**
     * get PwPollVoter
     *
     * @return PwPollVoter
     */
    protected function _getPollVoterDs()
    {
        return Wekit::load('poll.PwPollVoter');
    }

    /**
     * get PwPoll
     *
     * @return PwPoll
     */
    protected function _getPollDs()
    {
        return Wekit::load('poll.PwPoll');
    }
}
