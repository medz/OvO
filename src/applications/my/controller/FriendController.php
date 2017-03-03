<?php

/**
 * 找人Controller.
 *
 * @author jinlong.panjl <jinlong.panjl@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: FriendController.php 23994 2013-01-18 03:51:46Z long.shi $
 */
class FriendController extends PwBaseController
{
    private $_fetchNum = 100;

    public function beforeAction($handlerAdapter)
    {
        parent::beforeAction($handlerAdapter);
        if (!$this->loginUser->isExists()) {
            $this->forwardAction('u/login/run', array('backurl' => WindUrlHelper::createUrl('my/friend/run')));
        }
        $this->setOutput('friend', 'li');
    }

    /**
     * 推荐关注.
     */
    public function run()
    {
        $uids = $this->getOnlneUids(40);
        $userList = $this->_buildUserInfo($this->loginUser->uid, $uids, 20);
        $this->setOutput($userList, 'userList');

        // seo设置
         
        $seoBo = PwSeoBo::getInstance();
        $lang = Wind::getComponent('i18n');
        $seoBo->setCustomSeo($lang->getMessage('SEO:bbs.friend.run.title'), '', '');
        Wekit::setV('seo', $seoBo);
    }

    /**
     * 可能认识.
     */
    public function friendAction()
    {
        $uids = $this->getOnlneUids(40);
        $userList = $this->_buildUserInfo($this->loginUser->uid, $uids, 20);
        $this->setOutput($userList, 'userList');
    }

    /**
     * 搜索用户.
     */
    public function searchAction()
    {
        list($username, $usertag) = $this->getInput(array('username', 'usertag'));
        $page = intval($this->getInput('page'));
        $username = trim($username);
        $usertag = trim($usertag);
        $page < 1 && $page = 1;
        $perpage = 20;
        list($start, $limit) = Pw::page2limit($page, $perpage);

        $usertags = $this->_getUserTagService()->getUserTagList($this->loginUser->uid);
        !$usertags && $hotTags = $this->_getUserTagDs()->getHotTag(10);
        $args = array();
        if ($username) {
            // 按用户名搜索
             
            $vo = new PwUserSo();
            $vo->setUsername($username);
            $searchDs = Wekit::load('SRV:user.PwUserSearch');
            $count = $searchDs->countSearchUser($vo);
            if ($count) {
                $users = $searchDs->searchUser($vo, $limit, $start);
                $uids = array_keys($users);
            }
            $args['username'] = $username;
        }
        if ($usertag) {
            // 按用户标签搜索
            $tagInfo = $this->_getUserTagDs()->getTagByName($usertag);
            if ($tagInfo) {
                $count = $this->_getUserTagRelationDs()->countRelationByTagid($tagInfo['tag_id']);
                $tagRelations = $this->_getUserTagRelationDs()->getRelationByTagid($tagInfo['tag_id'], $limit, $start);
                $uids = array();
                foreach ($tagRelations as $v) {
                    $uids[] = $v['uid'];
                }
            }
            $args['usertag'] = $usertag;
        }
        if ($uids) {
            $userList = Wekit::load('user.PwUser')->fetchUserByUid($uids, PwUser::FETCH_MAIN | PwUser::FETCH_DATA | PwUser::FETCH_INFO);
            $follows = $this->_getAttentionDs()->fetchFollows($this->loginUser->uid, $uids);
            $fans = $this->_getAttentionDs()->fetchFans($this->loginUser->uid, $uids);
            $friends = array_intersect_key($fans, $follows);

            $this->setOutput($fans, 'fans');
            $this->setOutput($friends, 'friends');
            $this->setOutput($userList, 'userList');
            $this->setOutput($follows, 'follows');
        }

        $this->setOutput($page, 'page');
        $this->setOutput($perpage, 'perpage');
        $this->setOutput($count, 'count');
        $this->setOutput($args, 'args');
        $this->setOutput($hotTags, 'hotTags');
        $this->setOutput($usertags, 'usertags');
    }

    private function getOnlneUids($num)
    {
        $onlineUser = Wekit::load('online.PwUserOnline')->getInfoList('', 0, $num);

        return array_keys($onlineUser);
    }

    /**
     * 组装用户数据.
     *
     * @param int   $uid
     * @param array $uids
     * @param int   $num
     *
     * @return array
     */
    private function _buildUserInfo($uid, $uids, $num)
    {
        $attentions = $this->_getAttentionDs()->fetchFollows($uid, $uids);
        $uids = array_diff($uids, array($uid), array_keys($attentions));
        $uids = array_slice($uids, 0, $num);

        return $this->_getUserDs()->fetchUserByUid($uids, PwUser::FETCH_MAIN | PwUser::FETCH_DATA | PwUser::FETCH_INFO);
    }

    /**
     * PwUserDs.
     *
     * @return PwUser
     */
    private function _getUserDs()
    {
        return Wekit::load('user.PwUser');
    }

    /**
     * PwAttention.
     *
     * @return PwAttention
     */
    private function _getAttentionDs()
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

    /**
     * PwUserTag.
     *
     * @return PwUserTag
     */
    private function _getUserTagDs()
    {
        return Wekit::load('usertag.PwUserTag');
    }

    /**
     * PwUserTagRelation.
     *
     * @return PwUserTagRelation
     */
    private function _getUserTagRelationDs()
    {
        return Wekit::load('usertag.PwUserTagRelation');
    }

    /**
     * PwUserTagService.
     *
     * @return PwUserTagService
     */
    private function _getUserTagService()
    {
        return Wekit::load('usertag.srv.PwUserTagService');
    }
}
