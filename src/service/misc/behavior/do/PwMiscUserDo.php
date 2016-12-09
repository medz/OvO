<?php

Wind::import('SRV:user.srv.login.PwUserLoginDoBase');
/**
 * the last known user to change this file in the repository  <$LastChangedBy: xiaoxia.xuxx $>.
 *
 * @author $Author: xiaoxia.xuxx $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwMiscUserDo.php 18831 2012-09-28 06:44:01Z xiaoxia.xuxx $
 */
class PwMiscUserDo extends PwUserLoginDoBase
{
    /* (non-PHPdoc)
     * @see PwUserLoginDoBase::welcome()
     */
    public function welcome(PwUserBo $userBo, $ip)
    {
        $ds = Wekit::load('user.PwUserBehavior');
        $ds->replaceBehavior($userBo->uid, 'login_days', Pw::getTime());
        $ds->replaceBehavior($userBo->uid, 'login_count');
    }
}
