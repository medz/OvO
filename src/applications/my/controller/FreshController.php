<?php

defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * Enter description here ...
 *
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 *
 * @version $Id: FreshController.php 28843 2013-05-28 01:57:37Z jieyin $
 */
class FreshController extends PwBaseController
{
    /* (non-PHPdoc)
     * @see PwBaseController::beforeAction()
     */
    public function beforeAction($handlerAdapter)
    {
        parent::beforeAction($handlerAdapter);
        if (!$this->loginUser->isExists()) {
            $this->forwardAction('u/login/run', array('backurl' => WindUrlHelper::createUrl('my/fresh/run')));
        }
    }

    /* (non-PHPdoc)
     * @see WindController::run()
     */
    public function run()
    {
        $page = intval($this->getInput('page', 'get'));
        $page < 1 && $page = 1;
        $perpage = 20;
        list($start, $limit) = Pw::page2limit($page, $perpage);
        $gid = $this->getInput('gid');
        $url = array();
        if ($gid) {
            $url['gid'] = $gid;
            $current = $gid;
            $user = Wekit::load('attention.PwAttentionType')->getUserByType($this->loginUser->uid, $gid, 2000);
            $uids = array_keys($user);
            $count = $this->_getService()->countAttentionFreshByUid($this->loginUser->uid, $uids);

            $dataSource = new PwFetchAttentionFreshByUid($this->loginUser->uid, $uids, $limit, $start);
        } else {
            $current = 'all';
            $count = $this->_getService()->countAttentionFresh($this->loginUser->uid);
            if ($count > 200) {
                $count > 250 && Wekit::load('attention.PwFresh')->deleteAttentionFresh($this->loginUser->uid, $count - 200);
                $count = 200;
            }

            $dataSource = new PwFetchAttentionFresh($this->loginUser->uid, $limit, $start);
        }
        $freshDisplay = new PwFreshDisplay($dataSource);
        $fresh = $freshDisplay->gather();
        $type = Wekit::load('attention.srv.PwAttentionService')->getAllType($this->loginUser->uid);

        $unpost = '';
        !$this->loginUser->info['lastpost'] && $this->loginUser->info['lastpost'] = $this->loginUser->info['regdate'];
        $tmp = Pw::getTime() - $this->loginUser->info['lastpost'];
        if ($tmp > 31536000) {
            $unpost = floor($tmp / 31536000).'年多';
        } elseif ($tmp > 2592000) {
            $unpost = floor($tmp / 2592000).'个多月';
        } elseif ($tmp > 172800) {
            $unpost = floor($tmp / 86400).'天';
        }
        $type = Wekit::load('attention.srv.PwAttentionService')->getAllType($this->loginUser->uid);

        $allowUpload = $this->loginUser->getPermission('allow_upload');
        if ($imgextsize = Pw::subArray(Wekit::C('attachment', 'extsize'), array('jpg', 'jpeg', 'png', 'gif', 'bmp'))) {
            $maxSize = max($imgextsize).' KB';
            $filetypes = '*.'.implode(';*.', array_keys($imgextsize));
            $attachnum = intval(Wekit::C('attachment', 'attachnum'));
            if ($perday = $this->loginUser->getPermission('uploads_perday')) {
                $todayupload = $this->loginUser->info['lastpost'] < Pw::getTdtime() ? 0 : $this->loginUser->info['todayupload'];
                $attachnum = max(min($attachnum, $perday - $todayupload), 0);
                $attachnum == 0 && $allowUpload = 0;
            }
        } else {
            $allowUpload = $attachnum = $maxSize = 0;
            $filetypes = '';
        }

        $this->setOutput($allowUpload, 'allowUpload');
        $this->setOutput($attachnum, 'attachnum');
        $this->setOutput($maxSize, 'maxSize');
        $this->setOutput($filetypes, 'filetypes');

        $this->setOutput($current, 'currents');
        $this->setOutput($type, 'type');
        $this->setOutput($unpost, 'unpost');
        $this->setOutput($fresh, 'freshdb');
        $this->setOutput($this->loginUser->getPermission('fresh_delete'), 'freshDelete');

        $this->setOutput($page, 'page');
        $this->setOutput($perpage, 'perpage');
        $this->setOutput($count, 'count');
        $this->setOutput($url, 'url');

        // seo设置

        $seoBo = PwSeoBo::getInstance();
        $lang = Wind::getComponent('i18n');
        $seoBo->setCustomSeo($lang->getMessage('SEO:bbs.fresh.run.title'), '', '');
        Wekit::setV('seo', $seoBo);
    }

    public function replyAction()
    {
        $id = $this->getInput('id');

        $reply = new PwFreshReplyList($id);
        $fresh = $reply->getData();
        $replies = $reply->getReplies(7);
        $replies = Wekit::load('forum.srv.PwThreadService')->displayReplylist($replies);

        $count = count($replies);
        if ($count > 6) {
            $replies = array_slice($replies, 0, 6, true);
        }
        $this->setOutput($count, 'count');
        $this->setOutPut($replies, 'replies');
        $this->setOutPut($fresh, 'fresh');
    }

    public function doreplyAction()
    {
        $id = $this->getInput('id');
        $content = $this->getInput('content', 'post');
        $transmit = $this->getInput('transmit', 'post');

        $reply = new PwFreshReplyPost($id, $this->loginUser);

        if (($result = $reply->check()) !== true) {
            $this->showError($result->getError());
        }
        $reply->setContent($content);
        $reply->setIsTransmit($transmit);

        if (($result = $reply->execute()) instanceof PwError) {
            $this->showError($result->getError());
        }
        if (!$reply->getIscheck()) {
            $this->showError('BBS:post.reply.ischeck');
        }
        $content = Wekit::load('forum.srv.PwThreadService')->displayContent($content, $reply->getIsuseubb(), $reply->getRemindUser());
        /*
        $content = WindSecurity::escapeHTML($content);
        if ($reply->getIsuseubb()) {


            $content = PwSimpleUbbCode::convert($content, 140, new PwUbbCodeConvertThread());
        }*/
        $fresh = array();
        if ($transmit && ($newId = $reply->getNewFreshSrcId())) {
            $data = $reply->getData();
            $freshDisplay = new PwFreshDisplay(new PwFetchFreshByTypeAndSrcId($data['type'] == 3 ? 3 : 2, array($newId)));
            $fresh = $freshDisplay->gather();
            $fresh = current($fresh);
        }

        $this->setOutPut(Pw::getTime(), 'timestamp');
        $this->setOutPut($content, 'content');
        $this->setOutPut($this->loginUser->uid, 'uid');
        $this->setOutPut($this->loginUser->username, 'username');
        $this->setOutPut($fresh, 'fresh');
    }

    public function readAction()
    {
        $id = $this->getInput('id');
        $fresh = $this->_getService()->getFresh($id);
        if ($fresh['type'] == 1) {
            $thread = new PwThreadBo($fresh['src_id']);
            $array = $thread->info;
            $array['pid'] = 0;
        } else {
            $array = $this->_getThread()->getPost($fresh['src_id']);
            $thread = new PwThreadBo($array['tid']);
        }

        $array['content'] = WindSecurity::escapeHTML($array['content']);
        $array['content'] = str_replace("\n", '<br />', $array['content']);
        $array['useubb'] && $array['content'] = PwUbbCode::convert($array['content'], new PwUbbCodeConvertThread($thread, $array, $this->loginUser));

        echo $array['content'];
        $this->setTemplate('');
        //$this->setOutPut($array['content'], 'data');
        //$this->showMessage('success');
    }

    public function postAction()
    {
        $fid = $this->getInput('fid');
        $_getHtml = $this->getInput('_getHtml', 'get');
        list($content, $topictype, $subtopictype) = $this->getInput(array('content', 'topictype', 'sub_topictype'), 'post');

        $postAction = new PwTopicPost($fid);
        $pwpost = new PwPost($postAction);
        $this->runHook('c_fresh_post', $pwpost);
        if (($result = $pwpost->check()) !== true) {
            $this->showError($result->getError());
        }
        $postDm = $pwpost->getDm();
        $postDm->setTitle(Pw::substrs(Pw::stripWindCode($content), 30))
            ->setContent($content);

        $topictype_id = $subtopictype ? $subtopictype : $topictype;
        $topictype_id && $postDm->setTopictype($topictype_id);

        if (($result = $pwpost->execute($postDm)) !== true) {
            $data = $result->getData();
            $data && $this->addMessage($data, 'data');
            $this->showError($result->getError());
        }
        if (!$postDm->getField('ischeck')) {
            $this->showMessage('BBS:post.topic.ischeck');
        } elseif ($_getHtml == 1) {
            $freshDisplay = new PwFreshDisplay(new PwFetchFreshByTypeAndSrcId(1, array($pwpost->getNewId())));
            $fresh = $freshDisplay->gather();
            $fresh = current($fresh);
            $this->setOutput($fresh, 'fresh');
        } else {
            $this->showMessage('success');
        }
    }

    public function deleteAction()
    {
        $id = $this->getInput('id', 'post');
        if (!$id) {
            $this->showError('operate.select');
        }
        if (!$this->loginUser->getPermission('fresh_delete')) {
            $this->showError('permission.fresh.delete.deny');
        }

        $srv = new PwDeleteFresh(new PwGetFreshById($id), $this->loginUser);
        $srv->setIsDeductCredit(true)
            ->execute();

        $this->showMessage('success');
    }

    protected function _getService()
    {
        return Wekit::load('attention.PwFresh');
    }

    protected function _getThread()
    {
        return Wekit::load('forum.PwThread');
    }
}
