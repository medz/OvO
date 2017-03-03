<?php


 

/**
 * 每日打卡
 *
 * @author jinlong.panjl <jinlong.panjl@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id$
 */
class PunchController extends PwBaseController
{
    protected $config = array();
    protected $perpage = 20;
    protected $_creditBo;

    public function beforeAction($handlerAdapter)
    {
        parent::beforeAction($handlerAdapter);
        if ($this->loginUser->uid < 1) {
            $this->showError('USER:user.not.login');
        }
        $this->config = Wekit::C()->getValues('site');
        $this->_creditBo = PwCreditBo::getInstance();

        // 是否开启
        if (!$this->config['punch.open']) {
            $this->showError('SPCAE:punch.not.open');
        }
    }

    /**
     * 自己打卡
     */
    public function punchAction()
    {
        $userInfo = $this->loginUser->info;
        // 是否自己打过了
        if ($userInfo['punch']) {
            $punchData = unserialize($userInfo['punch']);
            $havePunch = $this->_getPunchService()->isPunch($punchData);
            if ($havePunch) {
                ($punchData['username'] == $userInfo['username']) && $this->showError('SPACE:punch.today.punch');
                $helpPunch = 1;
            }
        }
        // 奖励积分数
        $reward = $this->config['punch.reward'];
        $behavior = $this->_getUserBehaviorDs()->getBehavior($userInfo['uid'], 'punch_day');
        $steps = $behavior['number'] > 0 ? $behavior['number'] : 0;
        $helpPunch && $steps = $steps - 1 > 0 ? $steps - 1 : 0;
        $awardNum = $reward['min'];
        $steps && $awardNum = ($reward['min'] + $steps * $reward['step'] > $reward['max']) ? $reward['max'] : $reward['min'] + $steps * $reward['step'];
        if ($havePunch) {
            $reduce = $awardNum - $this->config['punch.friend.reward']['rewardMeNum'];
            $awardNum = $reduce > 0 ? $reduce : 0;
        }
        $behaviorNum = $havePunch ? $behavior['number'] : $behavior['number'] + 1;
        // 更新用户数据，记录行为
        $result = $this->_punchBehavior($userInfo, $awardNum, $behaviorNum);
        if ($result instanceof PwError) {
            $this->showError($result->getError());
        }
        // 奖励积分
        if ($awardNum) {
            $this->_creditBo->addLog('punch', array($reward['type'] => $awardNum), $this->loginUser, array(
                'cname'  => $this->_creditBo->cUnit[$reward['type']],
                'affect' => $awardNum, )
            );
            $this->_creditBo->set($userInfo['uid'], $reward['type'], $awardNum);
        }
        $result = array(
            'behaviornum' => $havePunch ? $behavior['number'] : $behavior['number'] + 1,
            'reward'      => $awardNum.$this->_creditBo->cUnit[$this->config['punch.reward']['type']].$this->_creditBo->cType[$this->config['punch.reward']['type']],
        );
        Pw::echoJson(array('state' => 'success', 'data' => $result));
        exit;
    }

    /**
     * 帮好友打卡弹窗.
     */
    public function friendAction()
    {
        // 今日帮打了几个好友
        $result = $this->_checkPunchNum();
        if ($result instanceof PwError) {
            $this->showError($result->getError());
        }

        list($page, $perpage) = $this->getInput(array('page', 'perpage'));
        $page = $page ? $page : 1;
        $perpage = $perpage ? $perpage : $this->perpage;
        list($start, $limit) = Pw::page2limit($page, $perpage);

        $count = $this->loginUser->info['follows'];
        if ($count) {
            $follows = $this->_getAttentionDs()->getFollows($this->loginUser->uid, $limit, $start);
            $userFollors = $this->_fetchFollowUsers(array_keys($follows));
        }
        $typeArr = $this->_getAttentionService()->getAllType($this->loginUser->uid);

        $reward = $this->config['punch.reward'];
        $punchFriend = $this->config['punch.friend.reward'];
        $friendReward = array(
            'cUnit' => $this->_creditBo->cUnit[$reward['type']],
            'cType' => $this->_creditBo->cType[$reward['type']],
            'cNum'  => $punchFriend['rewardNum'],
        );

        $this->setOutput($result, 'friendNum');
        $this->setOutput($friendReward, 'reward');
        $this->setOutput($userFollors, 'follows');
        $this->setOutput($typeArr, 'typeArr');
    }

    /**
     * 获取用户关注数据，滚动ajax输出.
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
        Pw::echoJson(array('state' => 'success', 'data' => $this->_fetchFollowUsers($uids), 'page' => $page));
        exit;
    }

    private function _fetchFollowUsers($uids)
    {
        if (!$uids) {
            return '';
        }
        $userList = $this->_getUserDs()->fetchUserByUid($uids, PwUser::FETCH_MAIN | PwUser::FETCH_DATA);
        $userFollors = array();
        foreach ($userList as $k => $v) {
            $tmpUser['disable'] = '';
            if ($v['punch']) {
                $punchData = unserialize($v['punch']);
                $havePunch = $this->_getPunchService()->isPunch($punchData);
                $tmpUser['disable'] = $havePunch ? 'disabled' : '';
            }
            $tmpUser['username'] = $v['username'];
            $userFollors[$k] = $tmpUser;
        }

        return $userFollors;
    }

    /**
     * do帮别人打卡
     */
    public function dofriendAction()
    {
        $friends = $this->getInput('friend');
        !is_array($friends) && $friends = array($friends);
        if (count($friends) < 0) {
            $this->showError('SPACE:punch.data.error');
        }
        // 是否关注的人
        $follows = Wekit::load('attention.PwAttention')->fetchFollows($this->loginUser->uid, $friends);
        $followUids = array_keys($follows);

        // 今日帮打了几个好友
        $countNum = count($followUids);

        $allowNum = $this->_checkPunchNum();
        if ($allowNum instanceof PwError) {
            $this->showError($allowNum->getError());
        }
        $allowNum < $countNum && $followUids = array_slice($followUids, 0, $allowNum);
        $awardNum = $this->config['punch.friend.reward']['rewardNum'];
        $behaviors = $this->fetchBehaviors($followUids);
        foreach ($followUids as $uid) {
            $userBo = new PwUserBo($uid);
            $v = $userBo->info;
            if ($v['punch']) {
                $punchData = unserialize($v['punch']);
                $havePunch = $this->_getPunchService()->isPunch($punchData);
                if ($havePunch) {
                    continue;
                }
            }
            $behaviorNum = (int) $behaviors[$uid] + 1;
            $this->_punchBehavior($v, $this->config['punch.friend.reward']['rewardMeNum'], $behaviorNum);
            $creditUids = array(
                $this->loginUser->uid => array($this->config['punch.reward']['type'] => $awardNum),
                $v['uid']             => array($this->config['punch.reward']['type'] => $this->config['punch.friend.reward']['rewardMeNum']),
            );
            // 奖励积分
            $this->_creditBo->addLog('punch', array($this->config['punch.reward']['type'] => $awardNum), $this->loginUser, array(
                'cname'  => $this->_creditBo->cType[$this->config['punch.reward']['type']],
                'affect' => $awardNum, )
            );

            $this->_creditBo->addLog('punch', array($this->config['punch.reward']['type'] => $this->config['punch.friend.reward']['rewardMeNum']), $userBo, array(
                'cname'  => $this->_creditBo->cType[$this->config['punch.reward']['type']],
                'affect' => $this->config['punch.friend.reward']['rewardMeNum'], )
            );
            $this->_creditBo->execute($creditUids);
            $this->_getUserBehaviorDs()->replaceDayBehavior($this->loginUser->uid, 'punch_num', Pw::getTime());
            $punchUsers[] = $userBo->username;
        }
        if ($punchUsers) {
            $awardNums = $awardNum * count($punchUsers);
            $result = array(
                'usernames' => implode(',', $punchUsers),
                'reward'    => $awardNums.$this->_creditBo->cUnit[$this->config['punch.reward']['type']].$this->_creditBo->cType[$this->config['punch.reward']['type']],
            );
        }
        Pw::echoJson(array('state' => 'success', 'data' => $result));
        exit;
    }

    protected function fetchBehaviors($uids, $behavior = 'punch_day')
    {
        $array = array();
        $behaviors = $this->_getUserBehaviorDs()->fetchBehavior($uids);
        if (!$behaviors) {
            return $array;
        }
        $time = Pw::getTime();
        foreach ($behaviors as $value) {
            if ($value['behavior'] != $behavior) {
                continue;
            }
            if ($value['expired_time'] > 0 && $value['expired_time'] < $time) {
                $value['number'] = 0;
            }
            $array[$value['uid']] = $value['number'];
        }

        return $array;
    }

    /**
     * 请求获取tip.
     */
    public function punchtipAction()
    {
        $punchData = $this->loginUser->info['punch'];
        $punchData = $punchData ? unserialize($punchData) : array();
        $reward = $this->config['punch.reward'];
        if (!$punchData) {
            $data = array(
                'cUnit'        => $this->_creditBo->cUnit[$reward['type']],
                'cType'        => $this->_creditBo->cType[$reward['type']],
                'todaycNum'    => $reward['min'],
                'tomorrowcNum' => $reward['min'] + $reward['step'],
                'step'         => $reward['step'],
                'max'          => $reward['max'],
            );
            Pw::echoJson(array('state' => 'success', 'data' => $data));
            exit;
        }
        $havePunch = $this->_getPunchService()->isPunch($punchData);
        if ($punchData['username'] == $this->loginUser->username && $havePunch) {
            Pw::echoJson(array('state' => 'fail'));
            exit;
        }
        $behavior = $this->_getUserBehaviorDs()->getBehavior($this->loginUser->uid, 'punch_day');
        $steps = $behavior['number'] > 0 ? $behavior['number'] : 0;
        $awardNum = ($reward['min'] + $steps * $reward['step'] > $reward['max']) ? $reward['max'] : $reward['min'] + $steps * $reward['step'];
        $tomorrowcNum = $awardNum + $reward['step'];
        $data = array(
            'cUnit'        => $this->_creditBo->cUnit[$reward['type']],
            'cType'        => $this->_creditBo->cType[$reward['type']],
            'todaycNum'    => $awardNum,
            'tomorrowcNum' => $tomorrowcNum > $reward['max'] ? $reward['max'] : $tomorrowcNum,
            'step'         => $reward['step'],
            'max'          => $reward['max'],
        );
        Pw::echoJson(array('state' => 'success', 'data' => $data));
        exit;
    }

    /**
     * 打卡 - 更新用户数据.
     *
     * @param int $uid
     *
     * @return bool
     */
    private function _punchBehavior($userInfo, $awardNum, $behaviorNum = '')
    {
        $reward = $this->config['punch.reward'];
        $punchData = array(
            'username' => $this->loginUser->username,
            'time'     => Pw::getTime(),
            'cNum'     => $awardNum,
            'cUnit'    => $this->_creditBo->cUnit[$reward['type']],
            'cType'    => $this->_creditBo->cType[$reward['type']],
            'days'     => $behaviorNum,
        );

        // 更新用户data表信息
         
        $dm = new PwUserInfoDm($userInfo['uid']);
        $dm->setPunch($punchData);
        $this->_getUserDs()->editUser($dm, PwUser::FETCH_DATA);

        //埋点[s_punch]
        PwSimpleHook::getInstance('punch')->runDo($dm);

        //记录行为
        return $this->_getUserBehaviorDs()->replaceBehavior($userInfo['uid'], 'punch_day', $punchData['time']);
    }

    /**
     * 帮别人打了几次
     *
     * @return int
     */
    private function _checkPunchNum()
    {
        $behavior = $this->_getUserBehaviorDs()->getBehavior($this->loginUser->uid, 'punch_num');

        $allowNum = $this->config['punch.friend.reward']['friendNum'] - $behavior['number'];
        if ($allowNum < 1) {
            return new PwError('SPACE:punch.friend.num.error', array('{num}' => $this->config['punch.friend.reward']['friendNum']));
        }

        return $allowNum;
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

    protected function _getAttentionService()
    {
        return Wekit::load('attention.srv.PwAttentionService');
    }

    protected function _getAttentionTypeDs()
    {
        return Wekit::load('attention.PwAttentionType');
    }

    /**
     * PwPunchService.
     *
     * @return PwPunchService
     */
    private function _getPunchService()
    {
        return Wekit::load('space.srv.PwPunchService');
    }

    /**
     * PwUser.
     *
     * @return PwUser
     */
    private function _getUserDs()
    {
        return Wekit::load('user.PwUser');
    }
}
