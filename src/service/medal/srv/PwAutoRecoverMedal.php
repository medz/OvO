<?php

Wind::import('SRV:medal.bo.PwUserMedalBo');
/**
 * 勋章自动回收流程
 * the last known user to change this file in the repository  <$LastChangedBy$>.
 *
 * @author $Author$ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id$
 */
class PwAutoRecoverMedal
{
    private $uid;
    private $medalBo;
    private $recoverIds = array();

    public function __construct($uid)
    {
        if ($uid < 1) {
            return false;
        }
        $this->uid = $uid;
        $this->medalBo = new PwUserMedalBo($uid);
    }

    public function autoRecoverMedal($awardType, $nowCondition)
    {
        $this->checkRecoverMedal($awardType, $nowCondition);
        if (!$this->recoverIds) {
            return false;
        }
        $medalIds = array_keys($this->medalBo->medals);
        if (!$medalIds) {
            return false;
        }
        $intersect = array_intersect($medalIds, $this->recoverIds);
        if (!$intersect) {
            return false;
        }

        return $this->recoverMedals($intersect);
    }

    /**
     * 判断应该回收的勋章.
     *
     * @param int $awardType    @source PwMedalService->awardTypes()
     * @param int $nowCondition
     */
    protected function checkRecoverMedal($awardType, $nowCondition)
    {
        $nowCondition = (int) $nowCondition;
        //if ($nowCondition < 1) return false;
        $medals = Wekit::load('medal.PwMedalInfo')->getInfoListByAwardtype($awardType);
        foreach ($medals as $medal) {
            if ($medal['award_condition'] > $nowCondition) {
                $this->recoverIds[] = $medal['medal_id'];
            }
        }

        return true;
    }

    protected function recoverMedals($recoverIds)
    {
        if (!is_array($recoverIds)) {
            return false;
        }
        $ds = Wekit::load('medal.PwMedalLog');
        $service = Wekit::load('medal.srv.PwMedalService');
        foreach ($recoverIds as  $medalId) {
            $log = $ds->getInfoByUidMedalId($this->uid, $medalId);
            if ($log['log_id'] < 1) {
                continue;
            }
            $service->stopAward($log['log_id'], 5);
        }

        return true;
    }
}
