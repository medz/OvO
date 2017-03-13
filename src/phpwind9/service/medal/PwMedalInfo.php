<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>.
 *
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwMedalInfo.php 7639 2012-04-10 07:20:01Z gao.wanggao $
 */
class PwMedalInfo
{
    const ATUO_AWARD = 1;
    const CHECK_AWARD = 2;

    const SYSTEM_AWARD = 1;
    const USER_AWARD = 2;

    /**
     * 获取一条记录.
     *
     * @param int $medalId
     */
    public function getMedalInfo($medalId)
    {
        $medalId = (int) $medalId;
        if ($medalId < 1) {
            return [];
        }

        return $this->_getDao()->getInfo($medalId);
    }

    /**
     * 获取多条记录.
     *
     * @param array $medalIds
     */
    public function fetchMedalInfo($medalIds)
    {
        if (!is_array($medalIds) || count($medalIds) < 1) {
            return [];
        }

        return $this->_getDao()->fetchInfo($medalIds);
    }

    /**
     * 统计内容.
     *
     * @param int $type
     */
    public function countInfo($medalType = 0)
    {
        $medalType = (int) $medalType;

        return $this->_getDao()->countInfo($medalType);
    }

    /**
     * 内容分页.
     *
     * @param int $receiveType receive_type
     * @param int $awardType   award_type
     * @param int $start
     * @param int $limit
     */
    public function getInfoList($receiveType = 0, $awardType = 0, $start = 0, $limit = 10, $isopen = null)
    {
        $receiveType = (int) $receiveType;
        $awardType = (int) $awardType;
        $start = (int) $start;
        $limit = (int) $limit;

        return $this->_getDao()->getInfoList($receiveType, $awardType, $start, $limit, $isopen);
    }

    /**
     * 按获取条件某获取勋章
     * 勋章多时慎用.
     */
    public function getInfoListByAwardtype($awardType, $isopen = null)
    {
        $awardType = (int) $awardType;
        if (isset($isopen)) {
            $isopen = (int) $isopen;
        }

        return $this->_getDao()->getInfoListByAwardtype($awardType, $isopen);
    }

    /**
     * 获取某类型的勋章
     * 勋章多时慎用.
     */
    public function getInfoListByReceiveType($receiveType, $isopen = null)
    {
        $receiveType = (int) $receiveType;
        if (isset($isopen)) {
            $isopen = (int) $isopen;
        }

        return $this->_getDao()->getInfoListByReceiveType($receiveType, $isopen);
    }

    /**
     * 按类型获取条件获取开启的勋章
     * 勋章多时慎用.
     */
    public function getOpenMedalList($awardType, $receiveType)
    {
        $awardType = (int) $awardType;
        $receiveType = (int) $receiveType;
        if ($awardType < 1 || $receiveType < 1) {
            return [];
        }

        return $this->_getDao()->getOpenMedalList($awardType, $receiveType);
    }

    /**
     * 获取所有开启的勋章
     * 勋章多时慎用.
     */
    public function getAllOpenMedal()
    {
        return $this->_getDao()->getAllOpenMedal();
    }

    /**
     * 获取所有的勋章
     * 勋章多时慎用.
     */
    public function getAllMedal()
    {
        return $this->_getDao()->getAllMedal();
    }

    public function addInfo(PwMedalDm $dm)
    {
        $resource = $dm->beforeAdd();
        if ($resource instanceof PwError) {
            return $resource;
        }

        return $this->_getDao()->addInfo($dm->getData());
    }

    public function updateInfo(PwMedalDm $dm)
    {
        $resource = $dm->beforeUpdate();
        if ($resource instanceof PwError) {
            return $resource;
        }

        return $this->_getDao()->updateInfo($dm->medalId, $dm->getData());
    }

    public function deleteInfo($medalId)
    {
        $medalId = (int) $medalId;
        if ($medalId < 1) {
            return false;
        }

        return $this->_getDao()->deleteInfo($medalId);
    }

    private function _getDao()
    {
        return Wekit::loadDao('medal.dao.PwMedalInfoDao');
    }
}
