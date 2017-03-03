<?php

!defined('ACLOUD_PATH') && exit('Forbidden');

define('USER_INVALID_PARAMS', 201);
define('USER_INVALID_USERNAME', 202);
define('USER_UPDATE_ERROR', 203);
define('USER_DELETE_ERROR', 204);
define('USER_NOT_EXISTS', 205);
define('USER_PWD_ERROR', 206);
define('USER_REGISTER_CLOSE', 207);
define('USER_REGISTER_SAME_USERNAME_PASSWORD', 208);
define('USER_REGISTER_FAIL', 209);
define('FORUM_FAVOR_MAX', 210);
define('FORUM_FAVOR_ALREADY', 211);

class ACloudVerCustomizedUser extends ACloudVerCustomizedBase
{
    /**
     * 根据用户ID获得用户所有相关信息.
     *
     * @param int $uid 用户ID
     *
     * @return array
     */
    public function getByUid($uid)
    {
        $uid = intval($uid);
        if ($uid < 1) {
            return $this->buildResponse(USER_INVALID_PARAMS, '参数错误');
        }
        $userInfo = $this->getUser()->getUserByUid($uid, PwUser::FETCH_ALL);
        $groupId = ($userInfo['groupid'] == 0) ? $userInfo['memberid'] : $userInfo['groupid'];
        $group = $this->getUserGroup()->getGroupByGid($groupId);
        $loginUser = Wekit::getLoginUser();
        $subjectNum = $this->getThread()->countThreadByUid($uid);    //用户发的主题数
        $result = $this->buildInfo($userInfo, $loginUser, $group['name'], $subjectNum);

        return $this->buildResponse(0, $result);
    }

    /**
     * 根据用户名字获得用户数据信息.
     *
     * @param string $username 用户名
     *
     * @return array
     */
    public function getByName($username)
    {
        $username = trim($username);
        if (!$username) {
            return $this->buildResponse(USER_INVALID_USERNAME, '参数错误');
        }
        $userInfo = $this->getUser()->getUserByName(trim($username), PwUser::FETCH_ALL);
        if ($userInfo instanceof PwError) {
            return $this->buildResponse(-1, $userInfo->getError());
        }
        $groupId = ($userInfo['groupid'] == 0) ? $userInfo['memberid'] : $userInfo['groupid'];
        $group = $this->getUserGroup()->getGroupByGid($groupId);
        $loginUser = Wekit::getLoginUser();
        $subjectNum = $this->getThread()->countThreadByUid($userInfo['uid']);
        $result = $this->buildInfo($userInfo, $loginUser, $group['name'], $subjectNum);

        return $this->buildResponse(0, $result);
    }

    public function updateIcon($uid)
    {
        $userBo = Wekit::getLoginUser();
        if ($userBo->uid != $uid) {
            return $this->buildResponse(USER_NOT_LOGIN, '用户未登录');
        }

        $bhv = new PwAvatarUpload($userBo);

        $upload = new PwUpload($bhv);

        if (($result = $upload->check()) === true) {
            $result = $upload->execute();
        }
        if ($result !== true) {
            return $this->buildResponse(USER_UPDATE_ERROR, '更新头像');
        } else {
            return $this->buildResponse(0, '更新成功');
        }
    }

    /**
     * 获取某用户加入的版块.
     *
     * @param int $uid
     *
     * @return array
     */
    public function getFavoritesForumByUid($uid)
    {
        $uid = intval($uid);
        if ($uid < 1) {
            return $this->buildResponse(USER_INVALID_USERNAME, '参数错误');
        }
        $loginUser = Wekit::getLoginUser();
        if ($loginUser['uid'] == 0) {
            return $this->buildResponse(USER_NOT_LOGIN, '用户未登录');
        }
        $result = $this->getForumUser()->getFroumByUid($uid);
        if ($result instanceof PwError) {
            return $this->buildResponse(-1, $result->getError());
        }
        $fids = array_keys($result);
        $forumInfo = $this->getForum()->fetchForum($fids, 3);
        $result = array_values($result);

        $forums = array();
        foreach ($result as $k => $v) {
            $forums[$k]['fid'] = $v['fid'];
            $forums[$k]['forumname'] = $forumInfo[$v['fid']]['name'];
            $forums[$k]['todaypost'] = $forumInfo[$v['fid']]['todayposts'];
        }

        return $this->buildResponse(0, array('forums' => $forums, 'count' => count($forums)));
    }

    /**
     * 加入版块.
     *
     * @param int $uid
     * @param int $fid
     *
     * @return int $count
     */
    public function addFavoritesForumByUid($uid, $fid)
    {
        list($uid, $fid) = array(intval($uid), intval($fid));
        if ($fid < 1 || $uid < 1) {
            return $this->buildResponse(USER_INVALID_PARAMS, '参数错误');
        }
        $loginUser = Wekit::getLoginUser();
        if ($loginUser['uid'] == 0) {
            return $this->buildResponse(USER_NOT_LOGIN, '用户未登录');
        }
        if ($this->getForumUser()->get($uid, $fid)) {
            return $this->buildResponse(FORUM_FAVOR_ALREADY, '该版块已经收藏');
        }
        $result = $this->getForumUser()->join($uid, $fid);
        if ($result instanceof PwError) {
            return $this->buildResponse(-1, $result->getError());
        }
        $count = count($this->getForumUser()->getFroumByUid($uid));

        return $this->buildResponse(0, array('count' => $count));
    }

    /**
     * 退出版块.
     *
     * @param int $uid
     * @param int $fid
     *
     * @return array
     */
    public function deleteFavoritesForumByUid($uid, $fid)
    {
        list($uid, $fid) = array(intval($uid), intval($fid));
        if ($fid < 1 || $uid < 1) {
            return $this->buildResponse(USER_INVALID_PARAMS, '参数错误');
        }
        $loginUser = Wekit::getLoginUser();
        if ($loginUser['uid'] == 0) {
            return $this->buildResponse(USER_NOT_LOGIN, '用户未登录');
        }
        $result = $this->getForumUser()->quit($uid, $fid);
        if ($result instanceof PwError) {
            return $this->buildResponse(-1, $result->getError());
        }
        $count = count($this->getForumUser()->getFroumByUid($uid));

        return $this->buildResponse(0, array('count' => $count));
    }

    /**
     * 用户登录.
     *
     * @param string $username 用户登录的帐号
     * @param string $password 用户登录的密码
     *
     * @return array
     */
    public function userLogin($username, $password)
    {
        list($username, $password) = array(trim($username), trim($password));
        if (empty($username) || empty($password)) {
            return $this->buildResponse(USER_INVALID_PARAMS, '参数错误');
        }
        $ip = Wind::getApp()->getRequest()->getClientIp();
        $result = $this->getLoginService()->login($username, $password, $ip);
        if ($result instanceof PwError) {
            return $this->buildResponse(-1, $result->getError());
        }

        return $this->buildResponse(0, array('uid' => $result['uid']));
    }

    /**
     * 用户注册信息.
     *
     * @return bool|int
     */
    public function userRegister($username, $password, $email)
    {
        if (!trim($username)) {
            return $this->buildResponse(USER_INVALID_USERNAME, '参数错误');
        }
        if (!$password || !$email || WindValidator::isEmail($email) !== true) {
            return $this->buildResponse(USER_INVALID_PARAMS, '参数错误');
        }

        Wind::import('SRC:service.user.dm.PwUserInfoDm');

        $userDm = new PwUserInfoDm();
        $userDm->setUsername($username);
        $userDm->setPassword($password);
        $userDm->setEmail($email);
        $userDm->setRegdate(Pw::getTime());
        $userDm->setRegip(Wind::getApp()->getRequest()->getClientIp());

        $registerService = new PwRegisterService();
        $registerService->setUserDm($userDm);
        $result = $registerService->register();
        if ($result instanceof PwError) {
            return $this->buildResponse(-1, $result->getError());
        }

        return $this->buildResponse(0, array('uid' => $result['uid']));
    }

    /**
     * 编辑email.
     *
     * @param int $uid   用户id
     * @param int $email email
     *
     * @return bool|PwError
     */
    public function updateEmail($uid, $email)
    {
        $uid = intval($uid);
        if ($uid < 1) {
            return $this->buildResponse(USER_INVALID_PARAMS, '参数错误');
        }
        $loginUser = Wekit::getLoginUser();
        if ($loginUser['uid'] == 0) {
            return $this->buildResponse(USER_NOT_LOGIN, '用户未登录');
        }
        if (!$email || WindValidator::isEmail($email) !== true) {
            return $this->buildResponse(USER_INVALID_PARAMS, '参数错误');
        }

        Wind::import('SRC:service.user.dm.PwUserInfoDm');
        $userDm = new PwUserInfoDm($uid);
        $userDm->setEmail($email);
        $result = $this->getUser()->editUser($userDm, PwUser::FETCH_MAIN);
        if ($result instanceof PwError) {
            return $this->buildResponse(-1, $result->getError());
        }

        return $this->buildResponse(0, array('uid' => $uid));
    }

    private function buildInfo($userInfo, $loginUser, $groupName, $subjectNum)
    {
        $result = array();
        $result['uid'] = $userInfo['uid'];
        $result['username'] = $userInfo['username'];
        $result['gender'] = $userInfo['gender'];
        $result['icon'] = Pw::getAvatar($userInfo['uid']);
        $result['birthday'] = $userInfo['byear'].'-'.$userInfo['bmonth'].'-'.$userInfo['bday'];
        $result['honor'] = ''; //自定义头衔
        $result['postnum'] = $userInfo['postnum'];
        $result['ltitle'] = $groupName;
        $isFollowed = $this->getAttention()->isFollowed($user->uid, $userInfo['uid']);
        $result['isfollowed'] = ($isFollowed == true) ? 1 : 0;
        $result['replycount'] = $userInfo['postnum'] - $subjectNum;
        $result['favorcount'] = 0; //个人收藏数
        $result['messages'] = $userInfo['messages'];
        $result['notices'] = $userInfo['notices'];
        $result['weibo'] = $this->getWeiboInfo($userInfo['uid'], $userInfo['fans'], $userInfo['follows']);

        return $result;
    }

    private function getWeiboInfo($uid, $fans, $follows)
    {
        $weiboDs = $this->getWeibo();
        $result['followedweibo'] = 0; //统计用户关注的微博
        $result['userweibo'] = 0; //统计用户的总微博数
        $result['referweibo'] = 0; //获取提到我的微博
        $result['fans'] = $fans;
        $result['follows'] = $follows;

        return $result;
    }

    public function checkCookie($cookie)
    {
        if (empty($cookie)) {
            $uid = $password = '';
        } else {
            list($uid, $password) = explode("\t", Pw::decrypt($cookie));
        }

        $user = new PwUserBo($uid);
        if (!$user->isExists() || Pw::getPwdCode($user->info['password']) != $password) {
            return $this->buildResponse(-1, 'cookie非法');
        } else {
            return $this->buildResponse(0, array('uid' => $uid, 'username' => $user->username));
        }
    }

    public function getUserBindInfo($uid, $type = '')
    {
        $extService = Wekit::load('EXT:account.service.srv.App_Account_CommonService');
        if (!is_object($extService)) {
            return $this->buildResponse(-1, 'can not find extension');
        }
        $info = $extService->getUserBoundInfo($uid, $type);

        return $this->buildResponse(0, $info);
    }

    private function getUser()
    {
        return Wekit::load('SRV:user.PwUser');
    }

    private function getForumUser()
    {
        return Wekit::load('SRV:forum.PwForumUser');
    }

    private function getLoginService()
    {
        return Wekit::load('SRV:user.srv.PwLoginService');
    }

    private function getUserBanService()
    {
        return Wekit::load('SRV:user.srv.PwUserBanService');
    }

    private function getAttention()
    {
        return Wekit::load('SRV:attention.PwAttention');
    }

    private function getThread()
    {
        return Wekit::load('SRV:forum.PwThread');
    }

    private function getWeibo()
    {
        return Wekit::load('SRV:weibo.PwWeibo');
    }

    private function getUserGroup()
    {
        return Wekit::load('SRV:usergroup.PwUserGroups');
    }

    private function getForum()
    {
        return Wekit::load('SRV:forum.PwForum');
    }
}
