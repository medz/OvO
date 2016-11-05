<?php

defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('SRV:forum.srv.PwPost');
Wind::import('WIND:utility.WindJson');
Wind::import('SRV:credit.bo.PwCreditBo');

/**
 * 发帖
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @license http://www.phpwind.com
 * @version $Id: PostController.php 27729 2013-04-28 02:00:50Z jieyin $
 * @package forum
 */

class PostController extends PwBaseController
{
    public $post;

    public function beforeAction($handlerAdapter)
    {
        parent::beforeAction($handlerAdapter);
        $action = $handlerAdapter->getAction();

        if (in_array($action, array('fastreply', 'replylist'))) {
            return;
        }
        $this->post = $this->_getPost($action);
        if (($result = $this->post->check()) !== true) {
            $error = $result->getError();
            if (is_array($error) && $error[0] == 'BBS:post.forum.allow.ttype'
            && ($allow = $this->post->forum->getThreadType($this->post->user))) {
                $special = key($allow);
                $this->forwardAction('bbs/post/run?fid='.$this->post->forum->fid.($special ? ('&special='.$special) : ''));
            }
            $this->showError($error);
        }

        //版块风格
        $pwforum = $this->post->forum;
        if ($pwforum->foruminfo['password']) {
            if (!$this->loginUser->isExists()) {
                $this->forwardAction('u/login/run', array('backurl' => WindUrlHelper::createUrl('bbs/post/'.$action, array('fid' => $$pwforum->fid))));
            } elseif (Pw::getPwdCode($pwforum->foruminfo['password']) != Pw::getCookie('fp_'.$pwforum->fid)) {
                $this->forwardAction('bbs/forum/password', array('fid' => $pwforum->fid));
            }
        }
        if ($pwforum->foruminfo['style']) {
            $this->setTheme('forum', $pwforum->foruminfo['style']);
        }

        $this->setOutput($action, 'action');
    }

    /**
     * 发帖页
     */
    public function run()
    {
        $this->runHook('c_post_run', $this->post);

        $this->setOutput('doadd', 'do');
        $this->setOutput($this->post->special, 'special');
        $this->setOutput('checked', 'reply_notice');
        $this->setOutput($this->post->forum->headguide(), 'headguide');
        $this->setOutput(in_array('postthread', (array) Wekit::C('verify', 'showverify')), 'hasVerifyCode');
        $this->_initTopictypes(0);
        $this->_initVar();
        // seo设置
        Wind::import('SRV:seo.bo.PwSeoBo');
        $seoBo = PwSeoBo::getInstance();
        $lang = Wind::getComponent('i18n');
        $seoBo->setCustomSeo($lang->getMessage('SEO:bbs.post.run.title'), '', '');
        Wekit::setV('seo', $seoBo);
    }

    /**
     * 发帖
     */
    public function doaddAction()
    {
        list($title, $content, $topictype, $subtopictype, $reply_notice, $hide) = $this->getInput(array('atc_title', 'atc_content', 'topictype', 'sub_topictype', 'reply_notice', 'hide'), 'post');
        $pwPost = $this->post;
        $this->runHook('c_post_doadd', $pwPost);

        $postDm = $pwPost->getDm();
        $postDm->setTitle($title)
            ->setContent($content)
            ->setHide($hide)
            ->setReplyNotice($reply_notice);

        //set topic type
        $topictype_id = $subtopictype ? $subtopictype : $topictype;
        $topictype_id && $postDm->setTopictype($topictype_id);

        if (($result = $pwPost->execute($postDm)) !== true) {
            $data = $result->getData();
            $data && $this->addMessage($data, 'data');
            $this->showError($result->getError());
        }
        $tid = $pwPost->getNewId();

        $this->showMessage('success', 'bbs/read/run/?tid='.$tid.'&fid='.$pwPost->forum->fid, true);
    }

    /**
     * 发回复页
     */
    public function replyAction()
    {
        $pid = $this->getInput('pid');
        $this->runHook('c_post_reply', $this->post);

        $info = $this->post->getInfo();
        $this->setOutput('', 'atc_title');
        $this->setOutput('Re:'.$info['subject'], 'default_title');
        $this->setOutput('doreply', 'do');
        $this->setOutput($info['tid'], 'tid');
        $this->setOutput($pid, 'pid');
        $this->setOutput('checked', 'reply_notice');
        $this->setOutput($this->post->forum->headguide().$this->post->forum->bulidGuide(array($info['subject'], WindUrlHelper::createUrl('bbs/read/run', array('tid' => $info['tid'], 'fid' => $this->post->forum->fid)))), 'headguide');
        $this->_initVar();
        $this->setTemplate('post_run');
        // seo设置
        Wind::import('SRV:seo.bo.PwSeoBo');
        $seoBo = PwSeoBo::getInstance();
        $lang = Wind::getComponent('i18n');
        $seoBo->setCustomSeo($lang->getMessage('SEO:bbs.post.reply.title'), '', '');
        Wekit::setV('seo', $seoBo);
    }

    /**
     * 快速回复
     */
    public function fastreplyAction()
    {
        $this->_replylist();
    }

    /**
     * 回复列表
     */
    public function replylistAction()
    {
        $this->_replylist();
    }

    /**
     * 回复
     */
    public function doreplyAction()
    {
        $tid = $this->getInput('tid');
        list($title, $content, $hide, $rpid) = $this->getInput(array('atc_title', 'atc_content', 'hide', 'pid'), 'post');
        $_getHtml = $this->getInput('_getHtml', 'get');
        $pwPost = $this->post;
        $this->runHook('c_post_doreply', $pwPost);

        $info = $pwPost->getInfo();
        $title == 'Re:'.$info['subject'] && $title = '';
        if ($rpid) {
            $post = Wekit::load('thread.PwThread')->getPost($rpid);
            if ($post && $post['tid'] == $tid && $post['ischeck']) {
                $post['content'] = $post['ifshield'] ? '此帖已被屏蔽' : trim(Pw::stripWindCode(preg_replace('/\[quote(=.+?\,\d+)?\].*?\[\/quote\]/is', '', $post['content'])));
                $post['content'] && $content = '[quote='.$post['created_username'].','.$rpid.']'.Pw::substrs($post['content'], 120).'[/quote] '.$content;
            } else {
                $rpid = 0;
            }
        }

        $postDm = $pwPost->getDm();
        $postDm->setTitle($title)
            ->setContent($content)
            ->setHide($hide)
            ->setReplyPid($rpid);

        if (($result = $pwPost->execute($postDm)) !== true) {
            $data = $result->getData();
            $data && $this->addMessage($data, 'data');
            $this->showError($result->getError());
        }
        $pid = $pwPost->getNewId();

        if ($_getHtml == 1) {
            Wind::import('SRV:forum.srv.threadDisplay.PwReplyRead');
            Wind::import('SRV:forum.srv.PwThreadDisplay');
            $threadDisplay = new PwThreadDisplay($tid, $this->loginUser);
            $this->runHook('c_post_replyread', $threadDisplay);
            $dataSource = new PwReplyRead($tid, $pid);
            $threadDisplay->execute($dataSource);
            $_cache = Wekit::cache()->fetch(array('level', 'group_right'));

            $this->setOutput($threadDisplay, 'threadDisplay');
            $this->setOutput($tid, 'tid');
            $this->setOutput($threadDisplay->fid, 'fid');
            $this->setOutput($threadDisplay->getThreadInfo(), 'threadInfo');
            $this->setOutput(current($threadDisplay->getList()), 'read');
            $this->setOutput($threadDisplay->getUsers(), 'users');
            $this->setOutput($threadDisplay->getArea(), 'area');
            $this->setOutput($threadDisplay->getForum(), 'pwforum');
            $this->setOutput(PwCreditBo::getInstance(), 'creditBo');
            $this->setOutput(Wekit::C('bbs', 'read.display_member_info'), 'displayMemberInfo');
            $this->setOutput(Wekit::C('bbs', 'read.display_info'), 'displayInfo');

            $this->setOutput($_cache['level']['ltitle'], 'ltitle');
            $this->setOutput($_cache['level']['lpic'], 'lpic');
            $this->setOutput($_cache['level']['lneed'], 'lneed');
            $this->setOutput($_cache['group_right'], 'groupRight');

            $this->setTemplate('read_floor');
        } elseif ($_getHtml == 2) {
            $content = Wekit::load('forum.srv.PwThreadService')->displayContent($content, $postDm->getField('useubb'), $postDm->getField('reminds'));
            $this->setOutput($postDm->getField('ischeck'), 'ischeck');
            $this->setOutput($content, 'content');
            $this->setOutput($this->loginUser->uid, 'uid');
            $this->setOutput($this->loginUser->username, 'username');
            $this->setOutput($pid, 'pid');
            $this->setOutput(Pw::getTime() - 1, 'time');
            $this->setTemplate('read_reply_floor');
        } else {
            $this->showMessage('success', 'bbs/read/run/?tid='.$tid.'&fid='.$pwPost->forum->fid.'&page=e#'.$pid, true);
        }
    }

    /**
     * 帖子编辑页
     */
    public function modifyAction()
    {
        $tid = $this->getInput('tid');
        $this->runHook('c_post_modify', $this->post);
        $info = $this->post->getInfo();

        $this->setTemplate('post_run');
        $this->setOutput($info['subject'], 'atc_title');
        $this->setOutput($info['content'], 'atc_content');
        $this->setOutput('domodify', 'do');
        $this->setOutput($info['tid'], 'tid');
        $this->setOutput($this->getInput('pid'), 'pid');
        $this->setOutput($this->_bulidAttachs($this->post->getAttachs()), 'attach');
        $info['reply_notice'] && $this->setOutput('checked', 'reply_notice');
        $this->setOutput($this->post->special, 'special');
        if ($this->post->action instanceof PwTopicModify) {
            $this->_initTopictypes($info['topic_type']);
            $headtitle = $info['subject'];
        } else {
            $thread = Wekit::load('forum.PwThread')->getThread($info['tid']);
            $headtitle = $thread['subject'];
        }
        $this->setOutput($this->post->forum->headguide().$this->post->forum->bulidGuide(array($headtitle, WindUrlHelper::createUrl('bbs/read/run', array('tid' => $info['tid'], 'fid' => $this->post->forum->fid)))), 'headguide');
        $this->_initVar();
        // seo设置
        Wind::import('SRV:seo.bo.PwSeoBo');
        $seoBo = PwSeoBo::getInstance();
        $lang = Wind::getComponent('i18n');
        $seoBo->setCustomSeo($lang->getMessage('SEO:bbs.post.modify.title'), '', '');
        Wekit::setV('seo', $seoBo);
    }

    /**
     * 编辑帖子
     */
    public function domodifyAction()
    {
        $tid = $this->getInput('tid');
        $pid = $this->getInput('pid');
        list($title, $content, $topictype, $subtopictype, $reply_notice, $hide) = $this->getInput(array('atc_title', 'atc_content', 'topictype', 'sub_topictype', 'reply_notice', 'hide'), 'post');
        $pwPost = $this->post;
        $this->runHook('c_post_domodify', $pwPost);

        $postDm = $pwPost->getDm();
        $postDm->setTitle($title)
            ->setContent($content)
            ->setHide($hide)
            ->setReplyNotice($reply_notice);

        //set topic type
        $topictype_id = $subtopictype ? $subtopictype : $topictype;
        $topictype_id && $postDm->setTopictype($topictype_id);

        if (($result = $pwPost->execute($postDm)) !== true) {
            $data = $result->getData();
            $data && $this->addMessage($data, 'data');
            $this->showError($result->getError());
        }
        $this->showMessage('success', 'bbs/read/jump/?tid='.$tid.'&pid='.$pid, true);
    }

    private function _getPost($action)
    {
        switch ($action) {
            case 'reply':
            case 'doreply':
                $tid = $this->getInput('tid');
                Wind::import('SRV:forum.srv.post.PwReplyPost');
                $postAction = new PwReplyPost($tid);
                break;
            case 'modify':
            case 'domodify':
                $tid = $this->getInput('tid');
                $pid = $this->getInput('pid');
                if ($pid) {
                    Wind::import('SRV:forum.srv.post.PwReplyModify');
                    $postAction = new PwReplyModify($pid);
                } else {
                    Wind::import('SRV:forum.srv.post.PwTopicModify');
                    $postAction = new PwTopicModify($tid);
                }
                break;
            default:
                $fid = $this->getInput('fid');
                $special = $this->getInput('special');
                Wind::import('SRV:forum.srv.post.PwTopicPost');
                $postAction = new PwTopicPost($fid);
                $special && $postAction->setSpecial($special);
        }

        return new PwPost($postAction);
    }

    private function _replylist()
    {
        list($tid, $pid, $page) = $this->getInput(array('tid', 'pid', 'page'), 'get');

        $page = intval($page);
        $page < 1 && $page = 1;
        $perpage = 10;

        $info = Wekit::load('forum.PwThread')->getThread($tid);
        $replydb = array();
        if ($pid) {
            $reply = Wekit::load('forum.PwThread')->getPost($pid);
            $total = $reply['replies'];
            list($start, $limit) = Pw::page2limit($page, $perpage);
            Wind::import('LIB:ubb.PwSimpleUbbCode');
            Wind::import('LIB:ubb.config.PwUbbCodeConvertThread');
            $replydb = Wekit::load('forum.PwPostsReply')->getPostByPid($pid, $limit, $start);
            $replydb = Wekit::load('forum.srv.PwThreadService')->displayReplylist($replydb);
        } else {
            $total = 0;
        }
        $this->setOutput($page, 'page');
        $this->setOutput($perpage, 'perpage');
        $this->setOutput($total, 'count');

        $this->setOutput($pid, 'pid');
        $this->setOutput($replydb, 'replydb');
        $this->setOutput($info['tid'], 'tid');
    }

    private function _initVar()
    {
        $creditBo = PwCreditBo::getInstance();
        $sellCreditRange = $this->loginUser->getPermission('sell_credit_range', false, array());
        $allowThreadExtend = $this->loginUser->getPermission('allow_thread_extend', false, array());
        $sellConfig = array(
            'ifopen' => ($this->post->forum->forumset['allowsell'] && $allowThreadExtend['sell']) ? 1 : 0,
            'price' => $sellCreditRange['maxprice'],
            'income' => $sellCreditRange['maxincome'],
            'credit' => Pw::subArray($creditBo->cType, $this->loginUser->getPermission('sell_credits')),
        );
        !$sellConfig['credit'] && $sellConfig['credit'] = array_slice($creditBo->cType, 0, 1, true);

        $enhideConfig = array(
            'ifopen' => ($this->post->forum->forumset['allowhide'] && $allowThreadExtend['hide']) ? 1 : 0,
            'credit' => Pw::subArray($creditBo->cType, $this->loginUser->getPermission('enhide_credits')),
        );
        !$enhideConfig['credit'] && $enhideConfig['credit'] = array_slice($creditBo->cType, 0, 1, true);

        $allowUpload = ($this->post->user->isExists() && $this->post->forum->allowUpload($this->post->user) && ($this->post->user->getPermission('allow_upload') || $this->post->forum->foruminfo['allow_upload'])) ? 1 : 0;
        $attachnum = intval(Wekit::C('attachment', 'attachnum'));
        if ($perday = $this->post->user->getPermission('uploads_perday')) {
            $count = $this->post->user->info['lastpost'] < Pw::getTdtime() ? 0 : $this->post->user->info['todayupload'];
            $attachnum = max(min($attachnum, $perday - $count), 0);
        }

        $this->setOutput(PwSimpleHook::getInstance('PwEditor_app')->runWithFilters(array()), 'editor_app_config');
        $this->setOutput($this->post, 'pwpost');
        $this->setOutput($this->post->getDisabled(), 'needcheck');
        $this->setOutput($this->post->forum->fid, 'fid');
        $this->setOutput($this->post->forum, 'pwforum');
        $this->setOutput($sellConfig, 'sellConfig');
        $this->setOutput($enhideConfig, 'enhideConfig');
        $this->setOutput($allowThreadExtend, 'allowThreadExtend');
        $this->setOutput($allowUpload, 'allowUpload');
        $this->setOutput($attachnum, 'attachnum');
    }

    private function _bulidAttachs($attach)
    {
        if (!$attach) {
            return '';
        }
        $array = array();
        ksort($attach);
        reset($attach);
        foreach ($attach as $key => $value) {
            $array[$key] = array(
                'name' => $value['name'],
                'size' => $value['size'],
                'path' => Pw::getPath($value['path'], $value['ifthumb'] & 1),
                'thumbpath' => Pw::getPath($value['path'], $value['ifthumb']),
                'desc' => $value['descrip'],
                'special' => $value['special'],
                'cost' => $value['cost'],
                'ctype' => $value['ctype'],
            );
        }

        return $array;
    }

    private function _initTopictypes($defaultTopicType = 0)
    {
        $topictypes = $jsonArray = array();
        $forceTopicType = $this->post->forum->forumset['force_topic_type'];
        if ($this->post->forum->forumset['topic_type']) {
            $permission = $this->loginUser->getPermission('operate_thread', false, array());
            $topictypes = $this->_getTopictypeDs()->getTopicTypesByFid($this->post->forum->fid, !$permission['type']);
            foreach ($topictypes['sub_topic_types'] as $key => $value) {
                if (!is_array($value)) {
                    continue;
                }
// 				if (!$forceTopicType && $value) $jsonArray[$key][$key] = '无分类';
                foreach ($value as $k => $v) {
                    $jsonArray[$key][$k] = strip_tags($v['name']);
                }
            }
        }
        if ($defaultTopicType && isset($topictypes['all_types'][$defaultTopicType])) {
            $defaultParentTopicType = $topictypes['all_types'][$defaultTopicType]['parentid'];
        } else {
            $defaultTopicType = $defaultParentTopicType = 0;
        }
        $json = Pw::jsonEncode($jsonArray);
        $this->setOutput($defaultTopicType, 'defaultTopicType');
        $this->setOutput($defaultParentTopicType, 'defaultParentTopicType');
        $this->setOutput($topictypes, 'topictypes');
        $this->setOutput($json, 'subTopicTypesJson');
        $this->setOutput($forceTopicType ? 1 : 0, 'forceTopic');
        $this->setOutput('1', 'isTopic');
    }

    /**
     *
     * Enter description here ...
     * @return PwTopicType
     */
    private function _getTopictypeDs()
    {
        return Wekit::load('forum.PwTopicType');
    }
}
