<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>.
 *
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwMedalLikeDo.php 20027 2012-10-22 11:49:23Z gao.wanggao $
 */
class PwMedalLikeDo
{
    /* (non-PHPdoc)
     * @see PwLikeDoBase::addLike()
     */
    public function addLike(PwUserBo $userBo, PwLikeDm $dm)
    {
        Wind::import('SRV:medal.srv.PwAutoAwardMedal');
        $data = $dm->getData();
        $ds = Wekit::load('user.PwUserBehavior');
        $behavior = $ds->getBehavior($data['belikeuid'], 'belike_times');
        $condition = isset($behavior['number']) ? (int) $behavior['number'] : 0;
        $bp = new PwAutoAwardMedal(new PwUserBo($data['belikeuid']));
        $bp->autoAwardMedal(6, $condition);

        //喜欢统计
        $condition = isset($userBo->info['likes']) ? (int) $userBo->info['likes'] : 0;
        $bp = new PwAutoAwardMedal($userBo);
        $bp->autoAwardMedal(9, $condition);  //like_count
        return true;
    }

    /**
     * PwSimpleHook 勾子.
     *
     * @param int $uid
     * @param int $beLikeUid
     */
    public function delLike($uid, $beLikeUid)
    {
        Wind::import('SRV:medal.srv.PwAutoRecoverMedal');
        //TODO 其它接口
        $ds = Wekit::load('user.PwUserBehavior');
        $behavior = $ds->getBehavior($beLikeUid, 'belike_times');
        $condition = isset($behavior['number']) ? (int) $behavior['number'] : 0;
        $bp = new PwAutoRecoverMedal(new PwUserBo($beLikeUid));
        $bp->autoRecoverMedal(6, $condition);

        //喜欢统计
        $userbo = new PwUserBo($uid);
        $condition = isset($userbo->info['likes']) ? (int) $userbo->info['likes'] : 0;
        $bp = new PwAutoRecoverMedal($userbo);
        $bp->autoRecoverMedal(9, $condition);  //like_count
    }
}
