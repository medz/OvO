<?php

defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 版块基础服务
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwBbsinfo.php 21328 2012-12-04 11:32:35Z jieyin $
 */
class PwBbsinfo
{
    /**
     * 获取论坛信息.
     *
     * @param int $id
     *
     * @return array
     */
    public function getInfo($id)
    {
        if (empty($id)) {
            return array();
        }

        return $this->_getDao()->get($id);
    }

    /**
     * 更新论坛信息.
     *
     * @param object $dm 更新信息
     *
     * @return bool
     */
    public function updateInfo(PwBbsinfoDm $dm)
    {
        if (($result = $dm->beforeUpdate()) !== true) {
            return $result;
        }

        return $this->_getDao()->update($dm->id, $dm->getData(), $dm->getIncreaseData());
    }

    protected function _getDao()
    {
        return Wekit::loadDao('site.dao.PwBbsinfoDao');
    }
}
