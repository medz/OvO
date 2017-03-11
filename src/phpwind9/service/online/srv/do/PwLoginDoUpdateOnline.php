<?php


/**
 * the last known user to change this file in the repository  <$LastChangedBy: xiaoxia.xuxx $>.
 *
 * @author $Author: xiaoxia.xuxx $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwLoginDoUpdateOnline.php 18341 2012-09-14 06:53:14Z xiaoxia.xuxx $
 */
class PwLoginDoUpdateOnline extends PwUserLoginDoBase
{
    /* (non-PHPdoc)
     * @see PwUserLoginDoBase::welcome()
     */
    public function welcome(PwUserBo $userBo, $ip)
    {
        $srv = Wekit::load('online.srv.PwOnlineService');
        $srv->loginOnline($userBo->uid, $userBo->username, $userBo->gid, $ip);

        return true;
    }
}
