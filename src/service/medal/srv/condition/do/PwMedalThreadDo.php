<?php

Wind::import('SRV:forum.srv.post.do.PwPostDoBase');
Wind::import('SRV:medal.srv.PwAutoAwardMedal');
/**
 * @author Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwMedalThreadDo.php 18821 2012-09-28 03:47:15Z xiaoxia.xuxx $
 * @package
 */
class PwMedalThreadDo extends PwPostDoBase
{
    /**
     * 当前发帖用户
     *
     * @var PwUserBo
     */
    private $uesr = null;

    /**
     * 构造函数
     *
     * @param PwPost $pwpost
     */
    public function __construct(PwPost $pwpost)
    {
        $this->user = $pwpost->user;
    }

    /* (non-PHPdoc)
     * @see PwPostDoBase::addThread()
     */
    public function addThread($tid)
    {
        /* @var $ds PwUserBehavior */
        $ds = Wekit::load('user.PwUserBehavior');
        $time = Pw::getTime();
        $behavior = $ds->getBehaviorList($this->user->uid);
        /* thread_days：连续发帖天数 统计情况 */
        $condition = isset($behavior['thread_days']['number']) ? (int) $behavior['thread_days']['number'] : 0 ;
        $bp = new PwAutoAwardMedal($this->user);
        $bp->autoAwardMedal(3, $condition);

        /* thread_count：总发帖数 统计情况 */
        $condition = isset($behavior['thread_count']['number']) ? (int) $behavior['thread_count']['number'] : 0 ;
        $bp->autoAwardMedal(7, $condition);

        return true;
    }
}
