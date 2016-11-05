<?php

Wind::import('APPS:manage.controller.BaseManageController');

/**
 * 帖子审核管理
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: ContentController.php 28815 2013-05-24 09:39:50Z jieyin $
 * @package forum
 */

class ContentController extends BaseManageController
{
    /* (non-PHPdoc)
     * @see BaseManageController::beforeAction()
     */
    public function beforeAction($handlerAdapter)
    {
        parent::beforeAction($handlerAdapter);
        $result = $this->loginUser->getPermission('panel_bbs_manage', false, array());
        if (!$result['thread_check']) {
            $this->showError('BBS:manage.thread_check.right.error');
        }
    }

    public function run()
    {
        $page = intval($this->getInput('page'));
        list($author, $fid, $createdTimeStart, $createdTimeEnd) = $this->getInput(array('author', 'fid', 'created_time_start', 'created_time_end'));

        $page < 1 && $page = 1;
        $perpage = 20;
        list($start, $limit) = Pw::page2limit($page, $perpage);

        Wind::import('SRV:forum.vo.PwThreadSo');
        $so = new PwThreadSo();
        $so->setDisabled(1)->orderbyCreatedTime(0);
        $url = array();

        if ($author) {
            $so->setAuthor($author);
            $url['author'] = $author;
        }
        if ($fid) {
            $so->setFid($fid);
            $url['fid'] = $fid;
        }
        if ($createdTimeStart) {
            $so->setCreateTimeStart(Pw::str2time($createdTimeStart));
            $url['created_time_start'] = $createdTimeStart;
        }
        if ($createdTimeEnd) {
            $so->setCreateTimeEnd(Pw::str2time($createdTimeEnd));
            $url['created_time_end'] = $createdTimeEnd;
        }

        $count = Wekit::load('forum.PwThread')->countSearchThread($so);
        $threaddb = Wekit::load('forum.PwThread')->searchThread($so, $limit, $start);
        $this->setOutput($threaddb, 'threadb');
        $this->setOutput(Wekit::load('forum.srv.PwForumService')->getForumOption($fid), 'option_html');

        $this->setOutput($page, 'page');
        $this->setOutput($perpage, 'perpage');
        $this->setOutput($count, 'count');
        $this->setOutput($url, 'url');

        // seo设置
        Wind::import('SRV:seo.bo.PwSeoBo');
        $seoBo = PwSeoBo::getInstance();
        $lang = Wind::getComponent('i18n');
        $seoBo->setCustomSeo($lang->getMessage('SEO:manage.content.run.title'), '', '');
        Wekit::setV('seo', $seoBo);
    }

    public function doPassThreadAction()
    {
        $tid = $this->getInput('tid', 'post');
        if (empty($tid)) {
            $this->showError('operate.select');
        }
        !is_array($tid) && $tid = array($tid);

        $fids = array();
        $threaddb = Wekit::load('forum.PwThread')->fetchThread($tid);
        foreach ($threaddb as $key => $value) {
            $fids[$value['fid']]++;
        }
        Wind::import('SRV:forum.dm.PwTopicDm');
        $dm = new PwTopicDm(true);
        $dm->setDisabled(0);
        Wekit::load('forum.PwThread')->batchUpdateThread($tid, $dm, PwThread::FETCH_MAIN);

        foreach ($fids as $fid => $value) {
            Wekit::load('forum.srv.PwForumService')->updateStatistics($fid, $value, 0, $value);
        }

        $this->showMessage('success');
    }

    public function doDeleteThreadAction()
    {
        $tid = $this->getInput('tid', 'post');
        if (empty($tid)) {
            $this->showError('operate.select');
        }
        !is_array($tid) && $tid = array($tid);

        Wind::import('SRV:forum.srv.operation.PwDeleteTopic');
        Wind::import('SRV:forum.srv.dataSource.PwFetchTopicByTid');
        $deleteTopic = new PwDeleteTopic(new PwFetchTopicByTid($tid), new PwUserBo($this->loginUser->uid));
        $deleteTopic->setIsDeductCredit(1)->execute();

        $this->showMessage('success');
    }

    public function replyAction()
    {
        $page = intval($this->getInput('page'));
        list($author, $fid, $createdTimeStart, $createdTimeEnd) = $this->getInput(array('author', 'fid', 'created_time_start', 'created_time_end'));

        $page < 1 && $page = 1;
        $perpage = 20;
        list($start, $limit) = Pw::page2limit($page, $perpage);

        Wind::import('SRV:forum.vo.PwPostSo');
        $so = new PwPostSo();
        $so->setDisabled(1)->orderbyCreatedTime(0);
        $url = array();

        if ($author) {
            $so->setAuthor($author);
            $url['author'] = $author;
        }
        if ($fid) {
            $so->setFid($fid);
            $url['fid'] = $fid;
        }
        if ($createdTimeStart) {
            $so->setCreateTimeStart(Pw::str2time($createdTimeStart));
            $url['created_time_start'] = $createdTimeStart;
        }
        if ($createdTimeEnd) {
            $so->setCreateTimeEnd(Pw::str2time($createdTimeEnd));
            $url['created_time_end'] = $createdTimeEnd;
        }

        $count = Wekit::load('forum.PwThread')->countSearchPost($so);
        $postdb = Wekit::load('forum.PwThread')->searchPost($so, $limit, $start);

        $this->setOutput($postdb, 'postdb');

        $this->setOutput(Wekit::load('forum.srv.PwForumService')->getForumOption($fid), 'option_html');

        $this->setOutput($page, 'page');
        $this->setOutput($perpage, 'perpage');
        $this->setOutput($count, 'count');
        $this->setOutput($url, 'url');

        // seo设置
        Wind::import('SRV:seo.bo.PwSeoBo');
        $seoBo = PwSeoBo::getInstance();
        $lang = Wind::getComponent('i18n');
        $seoBo->setCustomSeo($lang->getMessage('SEO:manage.content.reply.title'), '', '');
        Wekit::setV('seo', $seoBo);
    }

    public function doPassPostAction()
    {
        $pid = $this->getInput('pid', 'post');
        if (empty($pid)) {
            $this->showError('operate.select');
        }
        !is_array($pid) && $pid = array($pid);

        $fids = $tids = array();
        $postdb = Wekit::load('forum.PwThread')->fetchPost($pid);
        foreach ($postdb as $key => $value) {
            $fids[$value['fid']]++;
            $tids[$value['tid']]++;
        }

        Wind::import('SRV:forum.dm.PwReplyDm');
        Wind::import('SRV:forum.dm.PwTopicDm');
        $dm = new PwReplyDm(true);
        $dm->setDisabled(0);
        Wekit::load('forum.PwThread')->batchUpdatePost($pid, $dm);

        foreach ($tids as $key => $value) {
            $post = current(Wekit::load('forum.PwThread')->getPostByTid($key, 1, 0, false));
            $dm = new PwTopicDm($key);
            $dm->addReplies($value);
            $dm->setLastpost($post['created_userid'], $post['created_username'], $post['created_time']);
            Wekit::load('forum.PwThread')->updateThread($dm, PwThread::FETCH_MAIN);
        }
        foreach ($fids as $fid => $value) {
            Wekit::load('forum.srv.PwForumService')->updateStatistics($fid, 0, $value, $value);
        }

        $this->showMessage('success');
    }

    public function doDeletePostAction()
    {
        $pid = $this->getInput('pid', 'post');
        if (empty($pid)) {
            $this->showError('operate.select');
        }
        !is_array($pid) && $pid = array($pid);

        Wind::import('SRV:forum.srv.operation.PwDeleteReply');
        Wind::import('SRV:forum.srv.dataSource.PwFetchReplyByPid');
        $deleteReply = new PwDeleteReply(new PwFetchReplyByPid($pid), PwUserBo::getInstance($this->loginUser->uid));
        $deleteReply->setIsDeductCredit(1)->execute();

        $this->showMessage('success');
    }
}
