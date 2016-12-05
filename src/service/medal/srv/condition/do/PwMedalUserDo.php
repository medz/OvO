<?php

Wind::import('SRV:user.srv.login.PwUserLoginDoBase');
Wind::import('SRV:medal.srv.PwAutoAwardMedal');
/**
 * the last known user to change this file in the repository  <$LastChangedBy: xiaoxia.xuxx $>.
 *
 * @author $Author: xiaoxia.xuxx $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwMedalUserDo.php 18823 2012-09-28 05:21:28Z xiaoxia.xuxx $
 */
class PwMedalUserDo extends PwUserLoginDoBase
{
    /* (non-PHPdoc)
     * @see PwUserLoginDoBase::welcome()
     */
    public function welcome(PwUserBo $userBo, $ip)
    {
        /* @var $ds PwUserBehavior */
        $ds = Wekit::load('user.PwUserBehavior');
        $time = Pw::getTime();

        $behavior = $ds->getBehaviorList($userBo->uid);
        $bp = new PwAutoAwardMedal($userBo);

        /* login_days:连续登录天数*/
        $condition = isset($behavior['login_days']['number']) ? (int) $behavior['login_days']['number'] : 0;
        $bp->autoAwardMedal(1, $condition);

        /* login_count:连续登录天数*/
        $condition = isset($behavior['login_count']['number']) ? (int) $behavior['login_count']['number'] : 0;
        $bp->autoAwardMedal(10, $condition);

        //回收过期勋章
        Wekit::load('medal.srv.PwMedalService')->recoverMedal($userBo->uid);

        return true;
    }
}
