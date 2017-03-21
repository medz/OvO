<?php

defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 附件基础服务
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwAttach.php 20516 2012-10-30 09:50:29Z jieyin $
 */
class PwAttach
{
    /**
     * 获取一个附件信息.
     *
     * @param int $aid
     *
     * @return array
     */
    public function getAttach($aid)
    {
        if (empty($aid)) {
            return [];
        }

        return $this->_getDao()->getAttach($aid);
    }

    /**
     * 获取多个附件信息.
     *
     * @param array $aids
     *
     * @return array
     */
    public function fetchAttach($aids)
    {
        if (empty($aids) || is_array($aids)) {
            return [];
        }

        return $this->_getDao()->fetchAttach($aids);
    }

    public function addAttach(PwAttachDm $dm)
    {
        if (($result = $dm->beforeAdd()) !== true) {
            return $result;
        }

        return $this->_getDao()->addAttach($dm->getData());
    }

    public function updateAttach(PwAttachDm $dm)
    {
        if (($result = $dm->beforeUpdate()) !== true) {
            return $result;
        }

        return $this->_getDao()->updateAttach($dm->aid, $dm->getData());
    }

    public function batchUpdateAttach($aids, PwAttachDm $dm)
    {
        if (! $aids || ! is_array($aids)) {
            return false;
        }
        if (($result = $dm->beforeUpdate()) !== true) {
            return $result;
        }

        return $this->_getDao()->batchUpdateAttach($aids, $dm->getData());
    }

    public function deleteAttach($aid)
    {
        return $this->_getDao()->deleteAttach($aid);
    }

    public function batchDeleteAttach($aids)
    {
        if (! $aids || ! is_array($aids)) {
            return false;
        }

        return $this->_getDao()->batchDeleteAttach($aids);
    }

    protected function _getDao()
    {
        return Wekit::loadDao('attach.dao.PwAttachDao');
    }
}
