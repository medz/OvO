<?php



/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: IndexController.php 24214 2013-01-23 03:18:19Z gao.wanggao $
 * @package
 */

class IndexController extends PwBaseController
{
    /* (non-PHPdoc)
     * @see PwBaseController::beforeAction()
     */
    public function beforeAction($handlerAdapter)
    {
        parent::beforeAction($handlerAdapter);
        $config = Wekit::C('site');
        if (!$config['medal.isopen']) {
            $this->showError('MEDAL:medal.is.close');
        }
        if (!$this->loginUser->isExists()) {
            $this->forwardAction('u/login/run', array('backurl' => WindUrlHelper::createUrl('medal/index/run')));
        }
    }

    /* (non-PHPdoc)
     * @see WindController::run()
     */
    public function run()
    {
        $myList_w = $myList_y = array();
        Wind::import('SRV:medal.bo.PwUserMedalBo');
        $medalBo = new PwUserMedalBo($this->loginUser->uid);
        $myRelationList = $medalBo->getMyAndAutoMedal();
        foreach ($myRelationList as $key => $medal) {
            $gids = $medal['medal_gids'] ? explode(',', $medal['medal_gids']) : array();

            //已领取和可领取的不过滤
            if ($medal['award_status'] < 3) {
                if ($gids && !$this->loginUser->inGroup($gids) && !in_array($this->loginUser->info['memberid'], $gids)) {
                    unset($myRelationList[$key]);
                    continue;
                }
            }
            if ($medal['award_status'] == 4) {
                $myList_y[$key] = $medal;
            } else {
                $myList_w[$key] = $medal;
            }

            $medalJson[] = array(
                'id' => $medal['medal_id'],
                'status' => $medal['award_status'],
                'name' => $medal['name'],
                'type' => $medal['receive_type'],
                'description' => $medal['descrip'],
                'time' => $medal['expired_days'] ? $medal['expired_days'] : 0,
                'small' => $medal['icon'],
                'big' => $medal['image'],
                'condition' => $medal['award_condition'],
                //'behavior'=>isset($behaviors[$awardTypes[$medal['award_type']]]) ? $behaviors[$awardTypes[$medal['award_type']]] : 0,
            );
        }
        /*$std = new stdClass();
        $std->data = $medalJson;*/
        $alreadyAll = false;
        if (count($myList_w) < 1 && count($myList_y) > 0) {
            $openMedals = $this->_getMedalDs()->getAllOpenMedal();
            foreach ($openMedals as $key => $medal) {
                $gids = $medal['medal_gids'] ? explode(',', $medal['medal_gids']) : array();
                if ($gids && !$this->loginUser->inGroup($gids) && !in_array($this->loginUser->info['memberid'], $gids)) {
                    unset($openMedals[$key]);
                    continue;
                }
            }
            $intersect = array_intersect(array_keys($openMedals), array_keys($myList_y));
            $alreadyAll = $intersect == array_keys($openMedals) ? true : false;
        }
        $count = count($myList_w);
        $myList_w = array_slice($myList_w, 0, 4, true);
        $this->setOutput($myList_y, 'myList_y');
        $this->setOutput($myList_w, 'myList_w');
        $this->setOutput($count, 'count');
        $this->setOutput($medalJson, 'medalJson');
        $this->setOutput($alreadyAll, 'alreadyAll');

        // seo设置
        Wind::import('SRV:seo.bo.PwSeoBo');
        $seoBo = PwSeoBo::getInstance();
        $lang = Wind::getComponent('i18n');
        $seoBo->setCustomSeo($lang->getMessage('SEO:medal.index.run.title'), '', '');
        Wekit::setV('seo', $seoBo);
    }

    public function showAction()
    {
        $medalId = (int) $this->getInput('medalid', 'get');
        $pop = $this->getInput('pop', 'get');
        $medal = $this->_getMedalDs()->getMedalInfo($medalId);
        if (!$medal) {
            $this->showError('MEDAL:fail');
        }
        $isAward = true;
        $awardTypes = $this->_getMedalService()->awardTypes();
        $behaviors = $this->_getMedalService()->getUserBehavior($this->loginUser->uid);
        $log = $this->_getMedalLogDs()->getInfoByUidMedalId($this->loginUser->uid, $medalId);
        $userMedal = $this->_getMedalUserDs()->getMedalUser($this->loginUser->uid);
        $count = isset($userMedal['counts']) ? (int) $userMedal['counts'] + 1 : 1;
        $userdata = Wekit::load('user.PwUser')->getUserByUid($this->loginUser->uid, PwUser::FETCH_DATA);
        $behaviors['follow_number'] = $userdata['follows'];
        $behaviors['fan_number'] = $userdata['fans'];
        $behaviors['like_count'] = $userdata['likes'];
        $medal['behavior'] = isset($behaviors[$awardTypes[$medal['award_type']]]) ? $behaviors[$awardTypes[$medal['award_type']]] : 0;
        $medal['image'] = $this->_getMedalService()->getMedalImage($medal['path'], $medal['image']);
        if ($medal['receive_type'] == 1) {
            $ext = '天不进行相同行为';
        } else {
            $ext = '天';
        }
        $medal['expired'] = $medal['expired_days'] ? $medal['expired_days'].$ext : '长期有效';
        $gids = $medal['medal_gids'] ? explode(',', $medal['medal_gids']) : array();
        $userGids = array_merge($this->loginUser->groups, array($this->loginUser->info['memberid']));
        if (!$this->_getMedalService()->allowAwardMedal($userGids, $medal['medal_gids'])) {
            $isAward = false;
        }

        $groups = Wekit::load('usergroup.PwUserGroups')->fetchGroup($gids);
        $groupName = '';
        foreach ($groups as $group) {
            $groupName .= $groupName ? ', '.$group['name'] : $group['name'];
        }
        $data = array('isAward' => $isAward, 'groups' => $groupName);
        $this->setOutput($isAward, 'isAward');
        $this->setOutput($groupName, 'groupName');
        $this->setOutput($medal, 'medal');
        $this->setOutput($log, 'log');
        $this->setOutput($pop, 'pop');
        $this->setOutput($count, 'count');
    }

    public function centerAction()
    {
        $myLog = $myMedal = array();
        $count = 0;
        $medals = $this->_getMedalDs()->getAllOpenMedal();
        $myList = $this->_getMedalLogDs()->getInfoListByUid($this->loginUser->uid);
        foreach ($myList as $my) {
            $myStatus[$my['medal_id']] = $my['award_status'];
            $myLog[$my['medal_id']] = $my['log_id'];
            if ($my['award_status'] == 4) {
                $count++;
            }
        }
        $medalJson = array();
        $sevice = $this->_getMedalService();

        foreach ($medals as $key => $value) {
            $status = isset($myStatus[$key]) ? $myStatus[$key] : 0;
            $logid = isset($myLog[$key]) ? $myLog[$key] : 0;
            $medals[$key]['icon'] = $value['icon'] = $sevice->getMedalImage($value['path'], $value['icon']);
            $medals[$key]['image'] = $value['image'] = $sevice->getMedalImage($value['path'], $value['image']);
        }
        $this->setOutput($medals, 'medals');
        $this->setOutput($count, 'count');
        $this->setOutput($myStatus, 'myStatus');

        // seo设置
        Wind::import('SRV:seo.bo.PwSeoBo');
        $seoBo = PwSeoBo::getInstance();
        $lang = Wind::getComponent('i18n');
        $seoBo->setCustomSeo($lang->getMessage('SEO:medal.index.center.title'), '', '');
        Wekit::setV('seo', $seoBo);
    }

    public function orderAction()
    {
        $info = $this->_getMedalUserDs()->getMedalUser($this->loginUser->uid);
        $attentionDs = Wekit::load('SRV:attention.PwAttention');
        $toUids = $attentionDs->getFollows($this->loginUser->uid, 100); //100个关注的人中取
        $toUids = array_keys($toUids);
        $toUids[] = $this->loginUser->uid;
        $attentionMedals = $toUids ? $this->_getMedalUserDs()->fetchMedalUserOrder($toUids, 0, 10) : array();
        $attentionUids = array();
        foreach ($attentionMedals as $v) {
            $v['counts'] > 0 && $attentionUids[] = $v['uid'];
        }
        $info['sort'] = 0;
        if (in_array($this->loginUser->uid, $attentionUids)) {
            foreach ($attentionUids as $attention) {
                $info['sort']++;
                if ($this->loginUser->uid == $attention) {
                    break;
                }
            }
        }
        $totalMedals = $this->_getMedalUserDs()->getTotalOrder(10);
        $totalUids = array_keys($totalMedals);
        $totalOrder = array_search($this->loginUser->uid, $totalUids);
        $totalOrder = $totalOrder === false ? false : $totalOrder + 1;

        $uids = array_merge($attentionUids, $totalUids);
        $userInfos = $uids ? Wekit::load('SRV:user.PwUser')->fetchUserByUid($uids) : array();
        $this->setOutput($attentionMedals, 'attentionMedals');
        $this->setOutput($totalMedals, 'totalMedals');
        $this->setOutput($userInfos, 'userInfos');
        $this->setOutput($totalOrder, 'totalOrder');
        $this->setOutput($info, 'info');

        // seo设置
        Wind::import('SRV:seo.bo.PwSeoBo');
        $seoBo = PwSeoBo::getInstance();
        $lang = Wind::getComponent('i18n');
        $seoBo->setCustomSeo($lang->getMessage('SEO:medal.index.order.title'), '', '');
        Wekit::setV('seo', $seoBo);
    }

    /**
     * 我的勋章排序
     *
     */
    public function doOrderAction()
    {
        $medalIds = array();
        $logIds = $this->getInput('id', 'post');
        $orders = $this->getInput('order', 'post');
        $logs = $this->_getMedalLogDs()->getInfoListByUidStatus($this->loginUser->uid, 4);
        if (count($logs) < 1 || !is_array($logIds)) {
            $this->showError('MEDAL:fail');
        }
        $_logIds = array_keys($logs);
        $logIds = array_intersect($logIds, $_logIds);
        Wind::import('SRV:medal.dm.PwMedalLogDm');
        foreach ($logIds as $key => $logid) {
            $dm = new PwMedalLogDm($logid);
            $dm->setLogOrder($orders[$key]);
            $this->_getMedalLogDs()->updateInfo($dm);
        }
        $this->_getMedalService()->updateMedalUser($this->loginUser->uid);
        $this->showMessage('MEDAL:success');
    }

    /**
     * 领取勋章
     *
     */
    public function doAwardAction()
    {
        $logId = (int) $this->getInput('logid', 'post');
        $isfresh = (int) $this->getInput('isfresh', 'post');
        $content = $this->getInput('content', 'post');
        if ($logId < 1) {
            $this->showError('MEDAL:fail');
        }
        $resource = $this->_getMedalService()->awardMedal($logId, $this->loginUser->uid);
        if ($resource instanceof PwError) {
            $this->showError($resource->getError());
        }
        if ($isfresh) {
            Wind::import('SRV:weibo.dm.PwWeiboDm');
            Wind::import('SRV:weibo.srv.PwSendWeibo');
            Wind::import('SRV:weibo.PwWeibo');
            $dm = new PwWeiboDm();
            $dm->setContent($content)
                  ->setType(PwWeibo::TYPE_MEDAL);
            $sendweibo = new PwSendWeibo($this->loginUser);
            $sendweibo->send($dm);
        }
        $this->showMessage('MEDAL:award.success');
    }

    public function doApplyAction()
    {
        $medalId = (int) $this->getInput('medalid', 'post');
        $medal = $this->_getMedalDs()->getMedalInfo($medalId);
        if (!$medal || $medal['receive_type'] == 1) {
            $this->showError('MEDAL:fail');
        }
        $userGids = array_merge($this->loginUser->groups, array($this->loginUser->info['memberid']));
        if (!$this->_getMedalService()->allowAwardMedal($userGids, $medal['medal_gids'])) {
            $this->showError('MEDAL:not.user.group');
        }
        $log = $this->_getMedalLogDs()->getInfoByUidMedalId($this->loginUser->uid, $medalId);
        if ($log) {
            $this->showError('MEDAL:already.apply');
        }
        Wind::import('SRV:medal.dm.PwMedalLogDm');
        $time = Pw::getTime();
        $dm = new PwMedalLogDm();
        $dm->setMedalid($medalId)
            ->setUid($this->loginUser->uid)
            ->setAwardStatus(2)
            ->setCreatedTime($time);
        $resource = $this->_getMedalLogDs()->replaceMedalLog($dm);
        if ($resource instanceof PwError) {
            $this->showError('MEDAL:fail');
        }
        $this->showMessage('MEDAL:apply.success');
    }

    private function _getMedalDs()
    {
        return Wekit::load('medal.PwMedalInfo');
    }

    private function _getMedalUserDs()
    {
        return Wekit::load('medal.PwMedalUser');
    }

    private function _getMedalLogDs()
    {
        return Wekit::load('medal.PwMedalLog');
    }

    private function _getMedalService()
    {
        return Wekit::load('medal.srv.PwMedalService');
    }
}
