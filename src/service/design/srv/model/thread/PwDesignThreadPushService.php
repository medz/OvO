<?php
/**
 * 推送的扩展方法
 * 1:PwDesignThreadPushService->getFromData() 用于对推送数据进行验证
 * 2:PwDesignThreadPushService->afterPush()  推送成功后的更新操作，如发消息  ,增金币等
 * 3:PwDesignThreadDataService->fetchData()	  对推送数据进行模块标签处理
 * the last known user to change this file in the repository  <$LastChangedBy: jieyin $>.
 *
 * @author $Author: jieyin $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwDesignThreadPushService.php 22678 2012-12-26 09:22:23Z jieyin $
 */
class PwDesignThreadPushService
{
    /**
     * 格式化原始推送数据
     * Enter description here ...
     *
     * @param int $fromid
     */
    public function getFromData($fromid)
    {
        $data = Wekit::load('forum.PwThread')->getThread($fromid, PwThread::FETCH_ALL);
        $data['title'] = $data['subject'];
        $data['descrip'] = Pw::stripWindCode($data['content']);
        $data['descrip'] = Pw::substrs($data['descrip'], 144);
        $data['model'] = 'thread';
        $data['fromid'] = $fromid;
        $data['uid'] = $data['created_userid'];

        return $data;
    }

    /**
     * 发送推送消息.
     */
    public function afterPush($pushid)
    {
        $extend = '';
        $pushDs = Wekit::load('design.PwDesignPush');
        $push = $pushDs->getPush($pushid);
        if (!$push) {
            return false;
        }
        $thread = Wekit::load('forum.PwThread')->getThread($push['push_from_id']);
        if (!$thread) {
            return false;
        }
        $info = unserialize($push['push_extend']);
        $standard = unserialize($push['push_standard']);
        $sTitle = $info[$standard['sTitle']];
        $sUrl = $info[$standard['sUrl']];
        $user = Wekit::load('SRV:user.PwUser')->getUserByUid($push['created_userid']);
        Wind::import('SRV:credit.bo.PwCreditBo');
        Wind::import('SRV:forum.bo.PwForumBo');
        $credit = PwCreditBo::getInstance();
        $operation = 'push_thread';
        $forum = new PwForumBo($thread['fid']);
        $credit->operate($operation, PwUserBo::getInstance($thread['created_userid']), true, array('forumname' => $forum->foruminfo['name']), $forum->getCreditSet($operation));
        $credit->execute();

        $strategy = $credit->getStrategy($operation);
        foreach ((array) $strategy['credit'] as $k => $v) {
            $v && $extend .= $credit->cType[$k].'+'.$v;
        }
        //$bo->sets($push['author_uid'], $credit['credit']);

        if ($push['neednotice']) {
            $content = '恭喜，您的帖子<a href="'.$sUrl.'">'.Pw::substrs($sTitle, 20).'</a>被<a href="'.WindUrlHelper::createUrl('space/index/run', array('uid' => $push['created_userid']), '', 'pw').'">'.$user['username'].'</a>执行 推送 操作。';
            $extend && $content .= '获得'.$extend;
            $title = '帖子《<a href="'.$sUrl.'">'.Pw::substrs($sTitle, 20).'</a>》被推送';
            Wekit::load('SRV:message.srv.PwNoticeService')->sendDefaultNotice($push['author_uid'], $content, $title);
            $pushDs->updateNeedNotice($pushid, 0);
        }

        return true;
    }
}
