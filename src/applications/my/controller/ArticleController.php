<?php



/**
 * 我的帖子回复
 *
 * @author jinlong.panjl <jinlong.panjl@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: ArticleController.php 23994 2013-01-18 03:51:46Z long.shi $
 * @package wind
 */
class ArticleController extends PwBaseController
{
    private $perpage = 20;

    public function beforeAction($handlerAdapter)
    {
        parent::beforeAction($handlerAdapter);
        if (!$this->loginUser->isExists()) {
            $this->forwardAction('u/login/run', array('backurl' => WindUrlHelper::createUrl('my/article/run')));
        }
    }

    /* (non-PHPdoc)
     * @see WindController::run()
     */
    public function run()
    {
        Wind::import('SRV:forum.srv.PwThreadList');
        list($page, $perpage) = $this->getInput(array('page', 'perpage'));
        $page = $page ? $page : 1;
        $perpage = $perpage ? $perpage : $this->perpage;
        $threadList = new PwThreadList();

        $threadList->setPage($page)->setPerpage($perpage);
        Wind::import('SRV:forum.srv.threadList.PwMyThread');
        $dataSource = new PwMyThread($this->loginUser->uid);

        $threadList->execute($dataSource);
        $threads = $threadList->getList();
        $topic_type = array();
        foreach ($threads as &$v) {
            $topic_type[] = $v['topic_type'];
        }
        $topictypes = $topic_type ? Wekit::load('forum.PwTopicType')->fetchTopicType($topic_type) : array();

        $this->setOutput($threadList->total, 'count');
        $this->setOutput($threadList->page, 'page');
        $this->setOutput($threadList->perpage, 'perpage');
        $this->setOutput($threads, 'threads');
        $this->setOutput($topictypes, 'topictypes');

        // seo设置

        Wind::import('SRV:seo.bo.PwSeoBo');
        $seoBo = PwSeoBo::getInstance();
        $lang = Wind::getComponent('i18n');

        $seoBo->setCustomSeo($lang->getMessage('SEO:bbs.article.run.title'), '', '');
        Wekit::setV('seo', $seoBo);
    }

    /**
     * 回复列表
     */
    public function replyAction()
    {
        list($page, $perpage) = $this->getInput(array('page', 'perpage'));
        $page = $page ? $page : 1;
        $perpage = $perpage ? $perpage : $this->perpage;
        list($start, $limit) = Pw::page2limit($page, $perpage);
        $count = $this->_getThreadExpandDs()->countDisabledPostByUid($this->loginUser->uid);
        if ($count) {
            $tmpPosts = $this->_getThreadExpandDs()->getDisabledPostByUid($this->loginUser->uid, $limit, $start);
            $posts = $tids = array();
            foreach ($tmpPosts as $v) {
                $tids[] = $v['tid'];
            }
            $threads = $this->_getThreadDs()->fetchThread($tids);
            foreach ($tmpPosts as $v) {
                $v['threadSubject'] = Pw::substrs($threads[$v['tid']]['subject'], 30);
                $v['content'] = Pw::substrs($v['content'], 30);
                $v['created_time'] = PW::time2str($v['created_time'], 'auto');
                $posts[] = $v;
            }
        }
        $this->setOutput($count, 'count');
        $this->setOutput($page, 'page');
        $this->setOutput($perpage, 'perpage');
        $this->setOutput($posts, 'posts');

        // seo设置

        Wind::import('SRV:seo.bo.PwSeoBo');
        $seoBo = PwSeoBo::getInstance();
        $lang = Wind::getComponent('i18n');
        $seoBo->setCustomSeo($lang->getMessage('SEO:bbs.article.reply.title'), '', '');
        Wekit::setV('seo', $seoBo);
    }

    /**
     * Enter description here ...
     *
     * @return PwThreadExpand
     */
    protected function _getThreadExpandDs()
    {
        return Wekit::load('forum.PwThreadExpand');
    }

    /**
     * Enter description here ...
     *
     * @return PwThread
     */
    protected function _getThreadDs()
    {
        return Wekit::load('forum.PwThread');
    }
}
