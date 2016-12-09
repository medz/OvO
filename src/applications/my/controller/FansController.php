<?php

/**
 * 粉丝controller.
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @license http://www.phpwind.com
 *
 * @version $Id: FansController.php 23994 2013-01-18 03:51:46Z long.shi $
 */
class FansController extends PwBaseController
{
    public function beforeAction($handlerAdapter)
    {
        parent::beforeAction($handlerAdapter);
        if (!$this->loginUser->isExists()) {
            $this->forwardAction('u/login/run', array('backurl' => WindUrlHelper::createUrl('my/fans/run')));
        }
        $this->setOutput('fans', 'li');
    }

    public function run()
    {
        $page = intval($this->getInput('page'));
        $page < 1 && $page = 1;
        $perpage = 20;
        list($start, $limit) = Pw::page2limit($page, $perpage);

        $count = $this->loginUser->info['fans'];
        $fans = $this->_getDs()->getFans($this->loginUser->uid, $limit, $start);
        $uids = array_keys($fans);
        $follows = $this->_getDs()->fetchFollows($this->loginUser->uid, $uids);
        $userList = Wekit::load('user.PwUser')->fetchUserByUid($uids, PwUser::FETCH_MAIN | PwUser::FETCH_DATA | PwUser::FETCH_INFO);
        $this->setOutput(WindUtility::mergeArray($fans, $userList), 'fans');
        $this->setOutput($follows, 'follows');

        $this->setOutput($page, 'page');
        $this->setOutput($perpage, 'perpage');
        $this->setOutput($count, 'count');
        //$this->setOutput($url, 'url');

        // seo设置
        Wind::import('SRV:seo.bo.PwSeoBo');
        $seoBo = PwSeoBo::getInstance();
        $lang = Wind::getComponent('i18n');
        $seoBo->setCustomSeo($lang->getMessage('SEO:bbs.fans.run.title'), '', '');
        Wekit::setV('seo', $seoBo);
    }

    /**
     * PwAttention.
     *
     * @return PwAttention
     */
    private function _getDs()
    {
        return Wekit::load('attention.PwAttention');
    }

    /**
     * PwAttentionRecommendFriendsService.
     *
     * @return PwAttentionRecommendFriendsService
     */
    protected function _getRecommendService()
    {
        return Wekit::load('attention.srv.PwAttentionRecommendFriendsService');
    }
}
