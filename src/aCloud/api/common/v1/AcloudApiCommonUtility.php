<?php

!defined('ACLOUD_PATH') && exit('Forbidden');
/**
 * @author jinling.sujl<emily100813@gmail.com> 2010-11-2
 *
 * @link http://www.phpwind.com
 *
 * @copyright Copyright &copy; 2003-2010 phpwind.com
 * @license
 */
require_once Wind::getRealPath('ACLOUD_VER:common.ACloudVerCommonFactory');

class AcloudApiCommonUtility
{
    public function getThumbAttach($attachurl, $ifthumb = false)
    {
        return $this->getVersionCommonUtility()->getThumbAttach($attachurl, $ifthumb);
    }

    public function getMiniUrl($path, $ifthumb, $where = null)
    {
        return $this->getVersionCommonUtility()->getMiniUrl($path, $ifthumb, $where);
    }

    private function getVersionCommonUtility()
    {
        return ACloudVerCommonFactory::getInstance()->getVersionCommonUtility();
    }
}
