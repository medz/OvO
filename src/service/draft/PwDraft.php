<?php

/**
 * 草稿箱
 *
 * @author jinlong.panjl <jinlong.panjl@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id$
 * @package wind
 */
class PwDraft
{
    /**
     * 添加
     *
     * @param  PwDraftDm $dm
     * @return bool
     */
    public function addDraft(PwDraftDm $dm)
    {
        if (($result = $dm->beforeAdd()) instanceof PwError) {
            return $result;
        }

        return $this->_getDraftDao()->add($dm->getData());
    }

    /**
     * 删除一条
     *
     * @param  int   $id
     * @return array
     */
    public function deleteDraft($id)
    {
        $id = intval($id);
        if ($id < 1) {
            return false;
        }

        return $this->_getDraftDao()->delete($id);
    }

    /**
     * 根据uid获取num条数据
     *
     * @param  int   $uid
     * @param  int   $num
     * @return array
     */
    public function getByUid($uid, $num = 10)
    {
        $uid = intval($uid);
        $num = intval($num);
        if ($uid < 1) {
            return array();
        }

        return $this->_getDraftDao()->getByUid($uid, $num);
    }

    /**
     * 根据用户统计草稿箱数量
     *
     * @param  int   $uid
     * @return array
     */
    public function countByUid($uid)
    {
        $uid = intval($uid);
        if ($uid < 1) {
            return 0;
        }

        return $this->_getDraftDao()->countByUid($uid);
    }

    /**
     * 获取一条数据
     *
     * @param  int   $id
     * @return array
     */
    public function getDraft($id)
    {
        $id = intval($id);
        if ($id < 1) {
            return array();
        }

        return $this->_getDraftDao()->get($id);
    }

    /**
     * 编辑
     *
     * @param  int   $id
     * @param  array $data
     * @return array
     */
    public function updateDraft($id, PwDraftDm $dm)
    {
        if (($result = $dm->beforeUpdate()) instanceof PwError) {
            return $result;
        }

        return $this->_getDraftDao()->update($id, $dm->getData());
    }

    /**
     * @return PwDraftDao
     */
    protected function _getDraftDao()
    {
        return Wekit::loadDao('draft.dao.PwDraftDao');
    }
}
