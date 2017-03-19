<?php


/**
 * 首页.
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @license http://www.phpwind.com
 *
 * @version $Id: FollowController.php 28843 2013-05-28 01:57:37Z jieyin $
 */
class FollowController extends PwBaseController
{
    /* (non-PHPdoc)
     * @see PwBaseController::beforeAction()
     */
    public function beforeAction($handlerAdapter)
    {
        parent::beforeAction($handlerAdapter);
        if (!$this->loginUser->isExists()) {
            $this->forwardAction('u/login/run', ['backurl' => WindUrlHelper::createUrl('my/follow/run')]);
        }
        $this->setOutput('follow', 'li');
    }

    /**
     * 关注-首页.
     */
    public function run()
    {
        $type = $this->getInput('type');
        $page = intval($this->getInput('page'));
        $page < 1 && $page = 1;
        $perpage = 20;
        list($start, $limit) = Pw::page2limit($page, $perpage);
        $url = $classCurrent = [];

        $typeCounts = $this->_getTypeDs()->countUserType($this->loginUser->uid);
        if ($type) {
            $tmp = $this->_getTypeDs()->getUserByType($this->loginUser->uid, $type, $limit, $start);
            $follows = $this->_getDs()->fetchFollows($this->loginUser->uid, array_keys($tmp));
            $count = $typeCounts[$type] ? $typeCounts[$type]['count'] : 0;
            $url['type'] = $type;
            $classCurrent[$type] = 'current';
        } else {
            $follows = $this->_getDs()->getFollows($this->loginUser->uid, $limit, $start);
            $count = $this->loginUser->info['follows'];
            $classCurrent[0] = 'current';
        }
        $uids = array_keys($follows);
        $fans = $this->_getDs()->fetchFans($this->loginUser->uid, $uids);
        $userList = Wekit::load('user.PwUser')->fetchUserByUid($uids, PwUser::FETCH_MAIN | PwUser::FETCH_DATA | PwUser::FETCH_INFO);

        $service = $this->_getService();
        $typeArr = $service->getAllType($this->loginUser->uid);
        $userType = $service->getUserType($this->loginUser->uid, $uids);
        foreach ($userType as $key => $value) {
            $tmp = [];
            foreach ($value as $k => $v) {
                $tmp[$v] = $typeArr[$v];
            }
            ksort($tmp);
            $userType[$key] = $tmp;
        }
        $follows = WindUtility::mergeArray($follows, $userList);
        if (!$type && !$follows) {
            $num = 30;
            $uids = $this->_getRecommendService()->getOnlneUids($num);
            $uids = array_slice($uids, 0, 24);
            $this->setOutput($this->_getRecommendService()->buildUserInfo($this->loginUser->uid, $uids, $num), 'recommend');
        }

        $this->setOutput($follows, 'follows');
        $this->setOutput($typeArr, 'typeArr');
        $this->setOutput($type, 'type');
        $this->setOutput($userType, 'userType');
        $this->setOutput($typeCounts, 'typeCounts');
        $this->setOutput($fans, 'fans');
        $this->setOutput($classCurrent, 'classCurrent');

        $this->setOutput($page, 'page');
        $this->setOutput($perpage, 'perpage');
        $this->setOutput($count, 'count');
        $this->setOutput($url, 'url');

        // seo设置

        $seoBo = PwSeoBo::getInstance();
        $lang = Wind::getComponent('i18n');
        $seoBo->setCustomSeo($lang->getMessage('SEO:bbs.follow.run.title'), '', '');
        Wekit::setV('seo', $seoBo);
    }

    /**
     * 关注用户.
     */
    public function addAction()
    {
        $uid = $this->getInput('uid', 'post');
        if (!$uid) {
            $this->showError('operate.select');
        }
        $private = Wekit::load('user.PwUserBlack')->checkUserBlack($this->loginUser->uid, $uid);
        if ($private) {
            $this->showError('USER:attention.private.black');
        }
        $result = $this->_getService()->addFollow($this->loginUser->uid, $uid);

        if ($result instanceof PwError) {
            $this->showError($result->getError());
        }
        $this->showMessage('success', 'my/follow/run');
    }

    /**
     * 批量关注用户.
     */
    public function batchaddAction()
    {
        $uids = $this->getInput('uids', 'post');
        if (!$uids) {
            $this->showError('USER:attention.uid.empty');
        }
        foreach ($uids as $uid) {
            $private = Wekit::load('user.PwUserBlack')->checkUserBlack($this->loginUser->uid, $uid);
            if ($private) {
                if (count($uids) == 1) {
                    $this->showError('USER:attention.private.black');
                }
                continue;
            }
            $this->_getService()->addFollow($this->loginUser->uid, $uid);
        }
        $this->showMessage('success', 'my/follow/run');
    }

    /**
     * 取消关注.
     */
    public function deleteAction()
    {
        $uid = $this->getInput('uid');
        if (!$uid) {
            $this->showError('operate.select');
        }
        $result = $this->_getService()->deleteFollow($this->loginUser->uid, $uid);
        if ($result instanceof PwError) {
            $this->showError($result->getError());
        }
        $this->showMessage('success', 'my/follow/run');
    }

    /**
     * 添加关注分类.
     */
    public function addtypeAction()
    {
        $name = $this->getInput('name', 'post');
        $uid = (int) $this->getInput('uid');
        if (!$name) {
            $this->showError('operate.select');
        }
        $result = $this->_getService()->addType($this->loginUser->uid, $name);

        if ($result instanceof PwError) {
            $this->showError($result->getError());
        }
        if ($uid) {
            $this->_getTypeDs()->addUserType($this->loginUser->uid, $uid, $result);
        }
        $this->setOutput(['id' => $result, 'name' => $name], 'data');
        $this->showMessage('success');
    }

    /**
     * 保存用户分类.
     */
    public function savetypeAction()
    {
        list($uid, $id, $type) = $this->getInput(['uid', 'id', 'type'], 'post');
        if (!$uid) {
            $this->showError('operate.select');
        }
        if ($type == 1) {
            $this->_getTypeDs()->addUserType($this->loginUser->uid, $uid, $id);
        } else {
            $this->_getTypeDs()->deleteByUidAndTouidAndType($this->loginUser->uid, $uid, $id);
        }
        $this->showMessage('success');
    }

    /**
     * 修改关注分类.
     */
    public function editTypeAction()
    {
        list($id, $name) = $this->getInput(['id', 'name'], 'post');
        if (!$id) {
            $this->showError('operate.select');
        }
        $type = $this->_getTypeDs()->getType($id);
        if (empty($type) || $type['uid'] != $this->loginUser->uid) {
            $this->showError('USER:attention.type.edit.self');
        }

        $types = $this->_getService()->getAllType($this->loginUser->uid);
        if (count($types) > 20) {
            $this->showError('USER:attention.type.count.error');
        }
        unset($types[$id]);
        if (in_array($name, $types)) {
            $this->showError('USER:attention.type.repeat');
        }

        $result = $this->_getTypeDs()->editType($id, $name);
        if ($result instanceof PwError) {
            $this->showError($result->getError());
        }
        $this->setOutput(['id' => $id, 'name' => $name], 'data');
        $this->showMessage('success');
    }

    /**
     * 删除关注分类.
     */
    public function deleteTypeAction()
    {
        $id = $this->getInput('id', 'post');
        if (!$id) {
            $this->showError('operate.select');
        }
        $type = $this->_getTypeDs()->getType($id);
        if (empty($type) || $type['uid'] != $this->loginUser->uid) {
            $this->showError('USER:attention.type.delete.self');
        }
        $this->_getTypeDs()->deleteType($id);
        $this->showMessage('success');
    }

    public function samefriendAction()
    {
        $uid = (int) $this->getInput('uid');
        $result = $this->_getRecommendFriendsDs()->getSameUser($this->loginUser->uid, $uid);
        $sameUser = $result['recommend_user'] ? unserialize($result['recommend_user']) : [];
        $sameUser['sameUser'] = $sameUser['sameUser'] ? array_slice($sameUser['sameUser'], 0, 3) : [];
        $this->setOutput($sameUser, 'sameUser');
        $this->setTemplate('TPL:my.recommend_same_user');
    }

    public function recommendfriendAction()
    {
        $this->setOutput($this->loginUser, 'loginUser');
        $this->setTemplate('TPL:my.recommend_mod_user');
    }

    public static function bulidGroup($group)
    {
        $str = implode('', $group);
        $str = trim($str);
        if (Pw::strlen($str) <= 5) {
            return implode(',', $group);
        }
        $i = 0;
        $t = [];
        foreach ($group as $value) {
            $value = trim($value);
            $len = Pw::strlen($value);
            if ($i + $len < 5) {
                $t[] = $value;
            } else {
                $t[] = Pw::substrs($value, 5 - $i, 0, false);
                break;
            }
            $i += $len;
        }

        return implode(',', $t).'...';
    }

    public static function bulidUserType($userType)
    {
        foreach ($userType as $k => $v) {
            $_tmp['id'] = $k;
            $items = [];
            foreach ($v as $tk => $tv) {
                $items[] = ['id' => $tk, 'value' => $tv];
            }
            $_tmp['items'] = $items;
            $array[] = $_tmp;
        }

        return $array;
    }

    /**
     * PwUser.
     *
     * @return PwUser
     */
    protected function _getUser()
    {
        return Wekit::load('user.PwUser');
    }

    /**
     * PwAttention.
     *
     * @return PwAttention
     */
    protected function _getDs()
    {
        return Wekit::load('attention.PwAttention');
    }

    /**
     * PwAttentionType.
     *
     * @return PwAttentionType
     */
    protected function _getTypeDs()
    {
        return Wekit::load('attention.PwAttentionType');
    }

    /**
     * PwAttentionService.
     *
     * @return PwAttentionService
     */
    protected function _getService()
    {
        return Wekit::load('attention.srv.PwAttentionService');
    }

    /**
     * Enter description here ...
     *
     * @return PwAttentionRecommendFriends
     */
    private function _getRecommendFriendsDs()
    {
        return Wekit::load('attention.PwAttentionRecommendFriends');
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
