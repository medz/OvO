<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>.
 *
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwEmotion.php 6968 2012-03-28 08:53:37Z gao.wanggao $
 */
class PwEmotion
{
    /**
     * 获取一条表情信息.
     *
     * @param int $emotionId
     */
    public function getEmotion($emotionId)
    {
        $emotionId = (int) $emotionId;
        if ($emotionId < 1) {
            return [];
        }

        return $this->_getDao()->getEmotion($emotionId);
    }

    /**
     * 获取多条表情信息.
     *
     * @param array $emotionIds
     */
    public function fetchEmotion($emotionIds)
    {
        if (! is_array($emotionIds) || ! $emotionIds) {
            return [];
        }

        return $this->_getDao()->fetchEmotion($emotionIds);
    }

    /**
     * 获取多个分类的表情.
     *
     * @param array $categoryIds
     */
    public function fetchEmotionByCatid($categoryIds)
    {
        if (! is_array($categoryIds) || ! $categoryIds) {
            return [];
        }

        return $this->_getDao()->fetchEmotionByCatid($categoryIds);
    }

    /**
     * 获取一个分类的表情.
     *
     * @param int  $categoryId
     * @param bool $isUsed
     */
    public function getListByCatid($categoryId, $isUsed = null)
    {
        $categoryId = (int) $categoryId;
        if ($categoryId < 1) {
            return [];
        }
        isset($isUsed) && $isUsed = (int) $isUsed;

        return $this->_getDao()->getListByCatid($categoryId, $isUsed);
    }

    /**
     * 获取所有表情.
     */
    public function getAllEmotion()
    {
        return $this->_getDao()->getAllEmotion();
    }

    public function addEmotion(PwEmotionDm $dm)
    {
        $resource = $dm->beforeAdd();
        if ($resource instanceof PwError) {
            return $resource;
        }

        return $this->_getDao()->addEmotion($dm->getData());
    }

    public function updateEmotion(PwEmotionDm $dm)
    {
        $resource = $dm->beforeUpdate();
        if ($resource instanceof PwError) {
            return $resource;
        }

        return $this->_getDao()->updateEmotion($dm->emotionId, $dm->getData());
    }

    public function deleteEmotion($emotionId)
    {
        $emotionId = (int) $emotionId;
        if ($emotionId < 1) {
            return false;
        }

        return $this->_getDao()->deleteEmotion($emotionId);
    }

    public function deleteEmotionByCatid($cateId)
    {
        if (empty($cateId)) {
            return false;
        }

        return $this->_getDao()->deleteEmotionByCatid($cateId);
    }

    private function _getDao()
    {
        return Wekit::loadDao('emotion.dao.PwEmotionDao');
    }
}
