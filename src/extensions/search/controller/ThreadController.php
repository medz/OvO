<?php

defined('RUN_STARTTIME') or define('RUN_STARTTIME', microtime(true));
Wind::import('EXT:search.service.AppSearchRecord');
 

/**
 * 本地搜索.
 */
class ThreadController extends PwBaseController
{
    protected $perpage = 20;
    protected $maxNum = 500;
    protected $type = '';
    protected $keywords = '';
    protected $conf;

    public function beforeAction($handlerAdapter)
    {
        parent::beforeAction($handlerAdapter);
        $this->conf = Wekit::C('search');
        if (!Wekit::C('search', 'isopen')) {
            $this->forwardRedirect(WindUrlHelper::createUrl('search/search/run'));
        }
        $this->type = 'thread';
        $this->setOutput($this->_getSearchService()->_limitTimeMap(), 'limittime');
        $this->setOutput($this->type, 'src');
        $this->keywords = $this->getInput('keywords');
        $hots = $this->_getSearchService()->getHotKey($this->type, 8);
        $stypes = $this->_getSearchService()->getTypes('', $this->keywords);
        $this->setOutput($stypes, 'stypes');
        $this->setOutput($hots, 'hots');
        //seo设置
        $seoBo = PwSeoBo::getInstance();
        $_title = $this->keywords ? $this->keywords.' - 搜索结果 - {sitename}' : $this->conf['seo.title'];
        $seoBo->setCustomSeo($_title, $this->conf['seo.keyword'], $this->conf['seo.desc']);
        $this->setOutput($seoBo->getData(), 'seo');
    }

    /* (non-PHPdoc)
     * @see WindController::run()
     */
    public function run()
    {
        if (($result = $this->_getSearchService()->_checkRight()) instanceof PwError) {
            $this->showError($result->getError());
        }
        list($page, $perpage, $keywords, $fid, $limittime, $orderby) = $this->getInput(array('page', 'perpage', 'keywords', 'fid', 'limittime', 'orderby'));
        if ($keywords && $keywords != '请您输入你想搜索的内容') {
            //最后搜索时间
            if (($result = $this->_getSearchService()->_checkSearch()) instanceof PwError) {
                $this->showError($result->getError());
            }
            $page = $page ? $page : 1;
            $perpage = $perpage ? $perpage : $this->perpage;
            list($start, $limit) = Pw::page2limit($page, $perpage);
            !$orderby && $orderby = 'lastpost_time';
            Wind::import('EXT:search.service.vo.PwThreadSo');
            $so = new PwThreadSo();
            $so->setDisabled(0);
            $keywords = urldecode($keywords);
            $so->setKeywordOfTitleOrContent($keywords);
            $fid && $so->setFid($fid);
            $limittime && $so->setCreateTimeStart($this->_getSearchService()->_getLimitTime($limittime));
            $so = $this->_getSearchService()->_getOrderBy($so, $orderby);
            if (($count = $this->_getSearchService()->countSearch('thread', $so)) instanceof PwError) {
                $this->showError($result->getError());
            }
            $count = $count > $this->maxNum ? $this->maxNum : $count;
            if ($count) {
                $threads = $this->_getSearchService()->search('thread', $so, $limit, $start);
                $threads = $this->_getSearchService()->build('thread', $threads, $keywords);
                $this->_getSearchService()->_replaceRecord($keywords, AppSearchRecord::TYPE_THREAD);
            }
            $this->setOutput($page, 'page');
            $this->setOutput($perpage, 'perpage');
            $this->setOutput($count, 'count');
            $this->setOutput($threads, 'threads');
            $this->setOutput(array(1 => 'img', 3 => 'img', 4 => 'file', 5 => 'img', 7 => 'img'), 'uploadIcon');
            $this->setOutput(array('img' => '图片帖', 'file' => '附件'), 'icon');
        }
        $args = array('keywords' => $keywords, 'fid' => $fid, 'limittime' => $limittime, 'orderby' => $orderby);
        $this->setOutput($args, 'args');
        $forumList = Wekit::load('forum.srv.PwForumService')->getForumList();
        $this->setOutput(AppSearchRecord::TYPE_THREAD, 'recordType');
        $this->setOutput($forumList, 'forumList');
        $this->setOutput($this->getCommonForumList($forumList), 'forumdb');
        $this->setTemplate($this->keywords ? 'thread_run' : 'index_run');
    }

    /**
     * (non-PHPdoc).
     *
     * @see src/library/base/PwBaseController::afterAction()
     */
    public function afterAction($handlerAdapter)
    {
        parent::afterAction($handlerAdapter);
        $this->setOutput($this->_getExecTime(), 'exectime');
    }

    /**
     * 取得系统运行所耗时间 **.
     */
    private static function _getExecTime()
    {
        $useTime = microtime(true) - RUN_STARTTIME;

        return $useTime ? round($useTime, 6) : 0;
    }

    public function getCommonForumList($forumList)
    {
        $forumdb = array(0 => array());
        if (!$forumList) {
            return $forumdb;
        }
        foreach ($forumList as $forums) {
            if ($forums['issub'] != 0) {
                continue;
            }
            if (!$forums['isshow']) {
                continue;
            }
            if ($forums['type'] === 'forum') {
                $forumdb[$forums['parentid']][$forums['fid']] = $forums;
            } elseif ($forums['type'] === 'category') {
                $forumdb[0][$forums['fid']] = $forums;
            }
        }

        return $forumdb;
    }

    /**
     * @return App_SearchRecord
     */
    private function _getSearchRecord()
    {
        return Wekit::load('EXT:search.service.AppSearchRecord');
    }

    /**
     * @return AppSearchService
     */
    private function _getSearchService()
    {
        return Wekit::load('EXT:search.service.srv.AppSearchService');
    }
}
