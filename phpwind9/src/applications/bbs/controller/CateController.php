<?php

defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 分类页面.
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @license http://www.phpwind.com
 *
 * @version $Id: CateController.php 28799 2013-05-24 06:47:37Z yetianshi $
 */
class CateController extends PwBaseController
{
    /* (non-PHPdoc)
     * @see WindController::run()
     */
    public function run()
    {
        $fid = intval($this->getInput('fid'));
        $pwforum = new PwForumBo($fid, true);
        if (!$pwforum->isForum(true)) {
            $this->showError('BBS:forum.exists.not');
        }
        if ($pwforum->allowVisit($this->loginUser) !== true) {
            $this->showError(['BBS:forum.permissions.visit.allow', ['{grouptitle}' => $this->loginUser->getGroupInfo('name')]]);
        }
        if ($pwforum->forumset['jumpurl']) {
            $this->forwardRedirect($pwforum->forumset['jumpurl']);
        }
        if ($pwforum->foruminfo['password']) {
            if (!$this->loginUser->isExists()) {
                $this->forwardAction('u/login/run', ['backurl' => WindUrlHelper::createUrl('bbs/cate/run', ['fid' => $fid])]);
            } elseif (Pw::getPwdCode($pwforum->foruminfo['password']) != Pw::getCookie('fp_'.$fid)) {
                $this->forwardAction('bbs/forum/password', ['fid' => $fid]);
            }
        }
        $isBM = $pwforum->isBM($this->loginUser->username);
        if ($operateThread = $this->loginUser->getPermission('operate_thread', $isBM, [])) {
            $operateThread = Pw::subArray($operateThread, ['delete']);
        }
        $pwforum->foruminfo['threads'] = $pwforum->foruminfo['subthreads'];
        $this->setOutput($operateThread, 'operateThread');

        $tab = $this->getInput('tab'); //tab标签
        $page = intval($this->getInput('page', 'get'));
        $orderby = $this->getInput('orderby', 'get');

        $threadList = new PwThreadList();
        $this->runHook('c_cate_run', $threadList);

        $threadList->setPage($page)
            ->setPerpage($pwforum->forumset['threadperpage'] ? $pwforum->forumset['threadperpage'] : Wekit::C('bbs', 'thread.perpage'))
            ->setIconNew($pwforum->foruminfo['newtime']);

        $defaultOrderby = $pwforum->forumset['threadorderby'] ? 'postdate' : 'lastpost';
        !$orderby && $orderby = $defaultOrderby;

        $isCommon = 0;
        if ($tab == 'digest') {
            $dataSource = new PwCateDigestThread($pwforum->fid, $orderby);
        } else {
            $srv = Wekit::load('forum.srv.PwForumService');
            $forbidFids = $srv->getForbidVisitForum($this->loginUser, $srv->getForumsByLevel($fid, $srv->getForumMap()), true);
            $dataSource = new PwCateThread($pwforum, $forbidFids);
            $dataSource->setOrderby($orderby);
            $isCommon = 1;
        }
        $orderby != $defaultOrderby && $dataSource->setUrlArg('orderby', $orderby);
        $threadList->execute($dataSource);
        if ($isCommon && $threadList->total > 12000) {
            Wekit::load('forum.PwThreadCateIndex')->deleteOver($fid, $threadList->total - 10000);
        }

        $this->setOutput($threadList, 'threadList');
        $this->setOutput($threadList->getList(), 'threaddb');
        $this->setOutput($tab, 'tab');
        $this->setOutput($defaultOrderby, 'defaultOrderby');
        $this->setOutput($orderby, 'orderby');
        $this->setOutput($pwforum->fid, 'fid');
        $this->setOutput($pwforum, 'pwforum');
        $this->setOutput($pwforum->headguide(), 'headguide');
        $this->setOutput($threadList->icon, 'icon');
        $this->setOutput($threadList->uploadIcon, 'uploadIcon');
        $this->setOutput($pwforum->forumset['numofthreadtitle'] ? $pwforum->forumset['numofthreadtitle'] : 26, 'numofthreadtitle');

        $this->setOutput($threadList->page, 'page');
        $this->setOutput($threadList->perpage, 'perpage');
        $this->setOutput($threadList->total, 'count');
        $this->setOutput($threadList->maxPage, 'totalpage');
        $this->setOutput($threadList->getUrlArgs(), 'urlargs');

        //版块风格
        if ($pwforum->foruminfo['style']) {
            $this->setTheme('forum', $pwforum->foruminfo['style']);

            //$this->addCompileDir($pwforum->foruminfo['style']);
        }

        //seo设置

        $seoBo = PwSeoBo::getInstance();
        $lang = Wind::getComponent('i18n');
        if ($threadList->page <= 1) {
            $seoBo->setDefaultSeo($lang->getMessage('SEO:bbs.thread.run.title'), '', $lang->getMessage('SEO:bbs.thread.run.description'));
        }
        $seoBo->init('bbs', 'thread', $fid);
        $seoBo->set([
            '{forumname}'        => $pwforum->foruminfo['name'],
            '{forumdescription}' => Pw::substrs($pwforum->foruminfo['descrip'], 100, 0, false),
            '{classification}'   => '',
            '{page}'             => $threadList->page,
        ]);
        Wekit::setV('seo', $seoBo);
    }

    /**
     * 主题分类.
     */
    public function topictypesAction()
    {
        $fid = (int) $this->getInput('fid', 'post');
        if ($fid < 1) {
            $this->showError('data.error');
        }
        $topicTypes = Wekit::load('forum.srv.PwTopicTypeService')->getTopictypes($fid);
        $topicTypes = $topicTypes ? $topicTypes : '';
        Pw::echoJson(['state' => 'success', 'data' => $topicTypes]);
        exit;
    }
}
