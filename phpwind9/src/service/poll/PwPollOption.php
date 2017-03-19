<?php

defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 投票选项DS.
 *
 * @author Mingqu Luo<luo.mingqu@gmail.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 *
 * @version $Id$
 */
class PwPollOption
{
    /**
     * 获得单条选项信息.
     *
     * @param int $optionId
     *
     * @return array
     */
    public function get($optionid)
    {
        $optionid = intval($optionid);
        if (1 > $optionid) {
            return [];
        }

        return $this->_getDao()->get($optionid);
    }

    /**
     * 获得多条选项信息.
     *
     * @param array $optionIds
     *
     * @return array
     */
    public function fetch($optionids)
    {
        if (empty($optionids) && !is_array($optionids)) {
            return [];
        }

        return $this->_getDao()->fetch($optionids);
    }

    /**
     * 根据投票id获得投票选项.
     *
     * @param unknown_type $pollid
     */
    public function getByPollid($pollid)
    {
        $pollid = intval($pollid);
        if (1 > $pollid) {
            return [];
        }

        return $this->_getDao()->getByPollid($pollid);
    }

    /**
     * 根据投票ids获得投票选项.
     *
     * @param unknown_type $pollid
     */
    public function fetchByPollid($pollids)
    {
        if (empty($pollids) || !is_array($pollids)) {
            return [];
        }

        return $this->_getDao()->fetchByPollid($pollids);
    }

    /**
     * 统计该投票的选项数.
     *
     * @param int $pollid
     *
     * @return int
     */
    public function countByPollid($pollid)
    {
        $pollid = intval($pollid);
        if (!$pollid) {
            return 0;
        }

        return $this->_getDao()->countByPollid($pollid);
    }

    /**
     * 添加.
     *
     * @param PwPollOptionDm $dm
     */
    public function add(PwPollOptionDm $dm)
    {
        if (($result = $dm->beforeAdd()) !== true) {
            return $result;
        }

        $fieldData = $dm->getData();
        if (!$fieldData) {
            return false;
        }

        return $this->_getDao()->add($fieldData);
    }

    /**
     * 删除.
     *
     * @param int $optionid
     *
     * @return bool
     */
    public function delete($optionid)
    {
        $optionid = intval($optionid);
        if ($optionid < 1) {
            return false;
        }

        return $this->_getDao()->delete($optionid);
    }

    /**
     * 根据pollid删除.
     *
     * @param unknown_type $pollid
     *
     * @return bool
     */
    public function deleteByPollid($pollid)
    {
        $pollid = intval($pollid);
        if ($pollid < 1) {
            return false;
        }

        return $this->_getDao()->deleteByPollid($pollid);
    }

    /**
     * 更新.
     *
     * @param PwPollOptionDm $dm
     */
    public function update(PwPollOptionDm $dm)
    {
        if (($result = $dm->beforeUpdate()) !== true) {
            return $result;
        }

        return $this->_getDao()->update($dm->id, $dm->getData(), $dm->getIncreaseData());
    }

    /**
     * 批量删除.
     *
     * @param array $optionIds 选项
     *
     * @return int
     */
    public function batchDelete($optionIds)
    {
        return $this->_getDao()->batchDelete($optionIds);
    }

    /**
     * 批量更新.
     *
     * @param array          $optionids
     * @param PwPollOptionDm $dm
     */
    public function batchUpdate($optionids, PwPollOptionDm $dm)
    {
        if (empty($optionids) || !is_array($optionids)) {
            return false;
        }
        if (($result = $dm->beforeUpdate()) !== true) {
            return $result;
        }

        return $this->_getDao()->batchUpdate($optionids, $dm->getData());
    }

    /**
     * get PwPollOptionDao.
     *
     * @return PwPollOptionDao
     */
    protected function _getDao()
    {
        return Wekit::loadDao('poll.dao.PwPollOptionDao');
    }
}
