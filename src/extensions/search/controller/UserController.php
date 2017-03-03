<?php

defined('RUN_STARTTIME') or define('RUN_STARTTIME', microtime(true));
Wind::import('EXT:search.service.AppSearchRecord');

/**
 * 本地搜索.
 *
 * @author jinlong.panjl <jinlong.panjl@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id$
 */
class UserController extends PwBaseController
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
        $this->type = 'user';
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

    public function run()
    {
        if (($result = $this->_getSearchService()->_checkRight()) instanceof PwError) {
            $this->showError($result->getError());
        }
        list($page, $perpage, $keywords, $limittime, $orderby) = $this->getInput(array('page', 'perpage', 'keywords', 'limittime', 'orderby'));
        $args = array();
        if ($keywords && $keywords != '请您输入你想搜索的内容') {
            //最后搜索时间
            if (($result = $this->_getSearchService()->_checkSearch()) instanceof PwError) {
                $this->showError($result->getError());
            }
            $page = $page ? $page : 1;
            $perpage = $perpage ? $perpage : $this->perpage;
            list($start, $limit) = Pw::page2limit($page, $perpage);

            $keywords = urldecode($keywords);
            $so = new PwUserSo();
            $so->setUsername($keywords)
                ->orderbyLastpost(0);
            $limittime && $so->setRegdate($this->_getSearchService()->_getLimitTime($limittime));
            if (($count = $this->_getSearchService()->countSearch('user', $so)) instanceof PwError) {
                $this->showError($result->getError());
            }
            $count = $count > $this->maxNum ? $this->maxNum : $count;
            if ($count) {
                $users = $this->_getSearchService()->search('user', $so, $limit, $start);
                $users = $this->_getSearchService()->build('user', $users, $keywords);
                $uids = array_keys($users);
                $follows = Wekit::load('attention.PwAttention')->fetchFollows($this->loginUser->uid, $uids);
                $fans = Wekit::load('attention.PwAttention')->fetchFans($this->loginUser->uid, $uids);
                $friends = array_intersect_key($fans, $follows);
                $this->setOutput($fans, 'fans');
                $this->setOutput($friends, 'friends');
                $this->setOutput($follows, 'follows');
                $this->_getSearchService()->_replaceRecord($keywords, AppSearchRecord::TYPE_USER);
            }
            $this->setOutput($page, 'page');
            $this->setOutput($perpage, 'perpage');
            $this->setOutput($count, 'count');
            $this->setOutput(array('keywords' => $keywords), 'args');
            $this->setOutput($users, 'users');
        }
        $this->setOutput(AppSearchRecord::TYPE_USER, 'recordType');
        $this->setTemplate($this->keywords ? 'user_run' : 'index_run');
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

    /**
     * @return AppSearchRecord
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
