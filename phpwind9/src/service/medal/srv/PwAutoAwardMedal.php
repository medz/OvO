<?php


/**
 * 自动勋章颁发流程
 * <1>checkAutoMedal()
 * <2>checkNeedAward()
 * <3>awardMedal().
 *
 * @author Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwAutoAwardMedal.php 18821 2012-09-28 03:47:15Z xiaoxia.xuxx $
 */
class PwAutoAwardMedal
{
    private $awardMedalIds = []; //该颁发的勋章ID数组
    private $awardMedalId = 0; //条件最高勋章ID
    private $medalBo;
    private $awardTypeMedals = [];
    /**
     * @var PwUserBo
     */
    private $userBo = null;

    /**
     * @param PwUserBo $userBo
     */
    public function __construct(PwUserBo $userBo)
    {
        $this->userBo = $userBo;
        $this->medalBo = new PwUserMedalBo($userBo->uid);
    }

    /**
     * 自动勋章颁发流程 发放所有满足条件的.
     *
     * @param int $awardType    @source PwMedalService->awardTypes()
     * @param int $nowCondition
     */
    public function autoAwardMedal($awardType, $nowCondition)
    {
        if ($this->checkAutoMedal($awardType, $nowCondition)) {
            if ($this->checkNeedAllAward()) {
                $this->awardAllMedal();
            }
        }
        $this->updateBehaviorMedal($awardType);

        return false;
    }

    /**
     * 自动勋章颁发流程 发放条件最高.
     *
     * @param int $awardType    @source PwMedalService->awardTypes()
     * @param int $nowCondition
     */
    public function autoAwardMaxMedal($awardType, $nowCondition)
    {
        if ($this->checkAutoMedal($awardType, $nowCondition)) {
            if ($this->checkNeedMaxAward()) {
                $this->awardMaxMedal();
            }
        }
        $this->updateBehaviorMedal($awardType);

        return false;
    }

    /**
     * 对自动勋章进行判断，是否达到发放条件.
     *
     * @param int $awardType
     * @param int $nowCondition
     *
     * @return bool
     */
    protected function checkAutoMedal($awardType, $nowCondition)
    {
        $medals = Wekit::load('medal.PwMedalInfo')->getInfoListByAwardtype($awardType, 1);
        $awardMedal = [];
        $userGids = array_merge($this->userBo->groups, [$this->userBo->info['memberid']]);

        /* @var $srv PwMedalService */
        $srv = Wekit::load('medal.srv.PwMedalService');
        foreach ($medals as $medal) {
            if (! $srv->allowAwardMedal($userGids, $medal['medal_gids'])) {
                continue;
            }
            $this->awardTypeMedals[] = $medal['medal_id'];
            if ($medal['award_condition'] <= $nowCondition) {
                $this->_appendAwardMedalId($medal['medal_id']);
                if (empty($awardMedal)) {
                    $awardMedal = $medal;
                }

                if ($medal['award_condition'] >= $awardMedal['award_condition']) {
                    $awardMedal = $medal;
                }
            }
        }
        if (count($awardMedal) < 1) {
            return false;
        }
        $this->awardMedalId = $awardMedal['medal_id'];

        return true;
    }

    /**
     * 判断是否需要更新勋章.
     *
     * @return bool
     */
    protected function checkNeedAllAward()
    {
        $alreadId = $this->medalBo->medals;
        $_needId = [];
        foreach ($this->awardMedalIds as $v) {
            if (array_key_exists($v, $alreadId)) {
                continue;
            }
            $_needId[] = $v;
        }
        if (! $_needId) {
            return false;
        }
        $this->awardMedalIds = $_needId;

        return true;
    }

    /**
     * 自动勋章发放 所有满足条件的勋章.
     *
     * 写入状态为可领取
     */
    protected function awardAllMedal()
    {
        $ds = Wekit::load('medal.PwMedalLog');
        Wekit::load('medal.dm.PwMedalLogDm');
        $srv = Wekit::load('medal.srv.PwMedalService');
        $time = Pw::getTime();
        foreach ($this->awardMedalIds as $_medalid) {
            $dm = new PwMedalLogDm();
            $dm->setMedalid($_medalid)->setUid($this->userBo->uid)->setAwardStatus(3)->setCreatedTime(
                $time)->setExpiredTime(0);
            $resource = $ds->replaceMedalLog($dm);
            if (! $resource instanceof PwError) {
                $srv->sendNotice($this->userBo->uid, $resource,
                $_medalid, 1);
            }
        }
    }

    /**
     * 判断是否需要更新勋章 条件最高的一个.
     */
    protected function checkNeedMaxAward()
    {
        if (array_key_exists($this->awardMedalId, $this->medalBo->medals)) {
            return false;
        }

        return true;
    }

    /**
     * 自动勋章发放 条件最高的一个.
     *
     * 写入状态为可领取
     */
    protected function awardMaxMedal()
    {
        $ds = Wekit::load('medal.PwMedalLog');
        //$ds->deleteInfosByUidMedalIds($this->medalBo->uid, array_keys($this->medalBo->getAlreadyMedals()));//删除已发放给用户这一类型的勋章
        Wekit::load('medal.dm.PwMedalLogDm');
        $dm = new PwMedalLogDm();
        $time = Pw::getTime();
        $dm->setMedalid($this->awardMedalId)->setUid($this->userBo->uid)->setAwardStatus(3)->setCreatedTime(
            $time)->setExpiredTime(0);
        $resource = $ds->replaceMedalLog($dm);
        if (! $resource instanceof PwError) {
            Wekit::load('medal.srv.PwMedalService')->sendNotice(
            $this->userBo->uid, $resource, $this->awardMedalId, 1);
        }
    }

    /**
     * 更新现有连续行为勋章.
     *
     * @param int $awardType
     *
     * @return bool
     */
    protected function updateBehaviorMedal($awardType)
    {
        if (! in_array($awardType, [1, 2, 3])) {
            return false;
        }
        $intersect = array_intersect($this->awardTypeMedals, array_keys($this->medalBo->medals));
        if (! $intersect) {
            return false;
        }
        $ds = Wekit::load('medal.PwMedalLog');
        $time = Pw::getTime();
        foreach ($intersect as $medalId) {
            $expired = (int) $this->medalBo->medals[$medalId]['expired_days'];
            if ($expired) {
                $expired = 86400 * $expired + $time;
                $ds->updateExpiredByUidMedalId($this->medalBo->uid, $medalId, $expired);
            }
        }
        Wekit::load('medal.srv.PwMedalService')->updateMedalUser($this->userBo->uid);

        return true;
    }

    /**
     * 添加可以获取的勋章ID.
     *
     * @param int $medalId
     */
    private function _appendAwardMedalId($medalId)
    {
        $this->awardMedalIds[] = $medalId;
    }
}
