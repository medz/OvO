<?php

defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 帖子列表页.
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @license http://www.phpwind.com
 *
 * @version $Id: ThreadController.php 23994 2013-01-18 03:51:46Z long.shi $
 */
class ThreadController extends PwBaseController
{
    protected $topictypes;

    /**
     * 帖子列表页.
     */
    public function run()
    {
        $tab = $this->getInput('tab');
        $fid = intval($this->getInput('fid'));
        $type = intval($this->getInput('type', 'get')); //主题分类ID
        $page = $this->getInput('page', 'get');
        $orderby = $this->getInput('orderby', 'get');

        $pwforum = new PwForumBo($fid, true);
        if (! $pwforum->isForum()) {
            $this->showError('BBS:forum.exists.not');
        }
        if ($pwforum->allowVisit($this->loginUser) !== true) {
            $this->showError(['BBS:forum.permissions.visit.allow', ['{grouptitle}' => $this->loginUser->getGroupInfo('name')]]);
        }
        if ($pwforum->forumset['jumpurl']) {
            $this->forwardRedirect($pwforum->forumset['jumpurl']);
        }
        if ($pwforum->foruminfo['password']) {
            if (! $this->loginUser->isExists()) {
                $this->forwardAction('u/login/run', ['backurl' => WindUrlHelper::createUrl('bbs/cate/run', ['fid' => $fid])]);
            } elseif (Pw::getPwdCode($pwforum->foruminfo['password']) != Pw::getCookie('fp_'.$fid)) {
                $this->forwardAction('bbs/forum/password', ['fid' => $fid]);
            }
        }
        $isBM = $pwforum->isBM($this->loginUser->username);
        if ($operateThread = $this->loginUser->getPermission('operate_thread', $isBM, [])) {
            $operateThread = Pw::subArray($operateThread, ['topped', 'digest', 'highlight', 'up', 'copy', 'type', 'move', /*'unite',*/ 'lock', 'down', 'delete', 'ban']);
        }
        $this->_initTopictypes($fid, $type);

        $threadList = new PwThreadList();
        $this->runHook('c_thread_run', $threadList);

        $threadList->setPage($page)
            ->setPerpage($pwforum->forumset['threadperpage'] ? $pwforum->forumset['threadperpage'] : Wekit::C('bbs', 'thread.perpage'))
            ->setIconNew($pwforum->foruminfo['newtime']);

        $defaultOrderby = $pwforum->forumset['threadorderby'] ? 'postdate' : 'lastpost';
        ! $orderby && $orderby = $defaultOrderby;

        if ($tab == 'digest') {
            $dataSource = new PwDigestThread($pwforum->fid, $type, $orderby);
        } elseif ($type) {
            $dataSource = new PwSearchThread($pwforum);
            $dataSource->setOrderby($orderby);
            $dataSource->setType($type, $this->_getSubTopictype($type));
        } elseif ($orderby == 'postdate') {
            $dataSource = new PwNewForumThread($pwforum);
        } else {
            $dataSource = new PwCommonThread($pwforum);
        }
        $orderby != $defaultOrderby && $dataSource->setUrlArg('orderby', $orderby);
        $threadList->execute($dataSource);

        $this->setOutput($threadList, 'threadList');
        $this->setOutput($threadList->getList(), 'threaddb');
        $this->setOutput($fid, 'fid');
        $this->setOutput($type ? $type : null, 'type');
        $this->setOutput($tab, 'tab');
        $this->setOutput($pwforum, 'pwforum');
        $this->setOutput($pwforum->headguide(), 'headguide');
        $this->setOutput($threadList->icon, 'icon');
        $this->setOutput($threadList->uploadIcon, 'uploadIcon');
        $this->setOutput($operateThread, 'operateThread');
        $this->setOutput($pwforum->forumset['numofthreadtitle'] ? $pwforum->forumset['numofthreadtitle'] : 26, 'numofthreadtitle');
        $this->setOutput((! $this->loginUser->uid && ! $this->allowPost($pwforum)) ? ' J_qlogin_trigger' : '', 'postNeedLogin');

        $this->setOutput($threadList->page, 'page');
        $this->setOutput($threadList->perpage, 'perpage');
        $this->setOutput($threadList->total, 'count');
        $this->setOutput($threadList->maxPage, 'totalpage');
        $this->setOutput($defaultOrderby, 'defaultOrderby');
        $this->setOutput($orderby, 'orderby');
        $this->setOutput($threadList->getUrlArgs(), 'urlargs');
        $this->setOutput($this->_formatTopictype($type), 'topictypes');

        //版块风格
        if ($pwforum->foruminfo['style']) {
            $this->setTheme('forum', $pwforum->foruminfo['style']);
            //$this->addCompileDir($pwforum->foruminfo['style']);
        }

        //seo设置

        $seoBo = PwSeoBo::getInstance();
        $lang = Wind::getComponent('i18n');
        if ($threadList->page <= 1) {
            if ($type) {
                $seoBo->setDefaultSeo($lang->getMessage('SEO:bbs.thread.run.type.title'), '', $lang->getMessage('SEO:bbs.thread.run.type.description'));
            } else {
                $seoBo->setDefaultSeo($lang->getMessage('SEO:bbs.thread.run.title'), '', $lang->getMessage('SEO:bbs.thread.run.description'));
            }
        }
        $seoBo->init('bbs', 'thread', $fid);
        $seoBo->set([
            '{forumname}'        => $pwforum->foruminfo['name'],
            '{forumdescription}' => Pw::substrs($pwforum->foruminfo['descrip'], 100, 0, false),
            '{classification}'   => $this->_getSubTopictypeName($type),
            '{page}'             => $threadList->page,
        ]);
        Wekit::setV('seo', $seoBo);
        Pw::setCookie('visit_referer', 'fid_'.$fid.'_page_'.$threadList->page, 300);
    }

    private function _initTopictypes($fid, &$type)
    {
        $this->topictypes = $this->_getTopictypeService()->getTopicTypesByFid($fid);
        if (! isset($this->topictypes['all_types'][$type])) {
            $type = 0;
        }
    }

    private function _getSubTopictype($type)
    {
        if (isset($this->topictypes['sub_topic_types']) && isset($this->topictypes['sub_topic_types'][$type])) {
            return array_keys($this->topictypes['sub_topic_types'][$type]);
        }

        return [];
    }

    private function _getSubTopictypeName($type)
    {
        return isset($this->topictypes['all_types'][$type]) ? $this->topictypes['all_types'][$type]['name'] : '';
    }

    private function _formatTopictype($type)
    {
        $topictypes = $this->topictypes;
        if (isset($topictypes['all_types'][$type]) && $topictypes['all_types'][$type]['parentid']) {
            $topictypeService = Wekit::load('forum.srv.PwTopicTypeService');
            $topictypes = $topictypeService->sortTopictype($type, $topictypes);
        }

        return $topictypes;
    }

    private function _getTopictypeService()
    {
        return Wekit::load('forum.PwTopicType');
    }

    private function allowPost(PwForumBo $forum)
    {
        return $forum->foruminfo['allow_post'] ? $forum->allowPost($this->loginUser) : $this->loginUser->getPermission('allow_post');
    }
}
