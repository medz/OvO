<?php

 

/**
 * Enter description here ...
 *
 * @author jinlong.panjl <jinlong.panjl@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id$
 */
class PwRecommendUserDo extends PwUserLoginDoBase
{
    /* (non-PHPdoc)
     * @see PwUserLoginDoBase::welcome()
     */
    public function welcome(PwuserBo $userBo, $ip)
    {
        //	$this->_getDs()->deleteByCreatedTime(Pw::getTime()-86400);
        $this->_getDs()->replaceCron($userBo->uid);
        $srv = Wekit::load('cron.srv.PwCronService');
        $srv->getSysCron('PwCronDoRecommendUser', Pw::getTime());

        return true;
    }

    /**
     * Enter description here ...
     *
     * @return PwAttentionRecommendCron
     */
    protected function _getDs()
    {
        return Wekit::load('attention.PwAttentionRecommendCron');
    }
}
