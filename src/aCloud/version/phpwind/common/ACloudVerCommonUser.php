<?php

!defined('ACLOUD_PATH') && exit('Forbidden');

define('USER_INVALID_PARAMS', 201);
define('USER_INVALID_USERNAME', 202);
define('USER_UPDATE_ERROR', 203);
define('USER_DELETE_ERROR', 204);
define('USER_NOT_EXISTS', 205);
define('USER_REGISTER_FAIL', 209);
define('USER_FORUM_FAVOR_ALREADY', 211);
define('USER_FORUM_NOT_EXIST', 303);
class ACloudVerCommonUser extends ACloudVerCommonBase
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
        $userBo = new PwUserBo(intval($uid));
        if (!$userBo->isExists()) {
            return $this->buildResponse(USER_NOT_EXISTS);
        }
        $result = $this->getUser()->getUserByUid($uid, PwUser::FETCH_MAIN + PwUser::FETCH_INFO);
        if ($result instanceof PwError) {
            return $this->buildResponse(-1, $result->getError());
        }

        return $this->buildResponse(0, $this->buildInfo($result));
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
            return $this->buildResponse(USER_INVALID_USERNAME);
        }
        $result = $this->getUser()->getUserByName(trim($username), PwUser::FETCH_ALL);
        if (isset($result['password'])) {
            unset($result['password']);
        }
        if ($result instanceof PwError) {
            return $this->buildResponse(-1, $result->getError());
        }

        return $this->buildResponse(0, $this->buildInfo($result));
    }

    public function updateIcon($uid)
    {
    }

    public function banUser($uid)
    {
        $this->user = new PwUserBo($uid);
        if (!$this->user->isExists()) {
            return $this->buildResponse(USER_NOT_EXISTS);
        }

        $rightType = array(PwUserBan::BAN_AVATAR, PwUserBan::BAN_SIGN, PwUserBan::BAN_SPEAK);
        $dmArray = array();
        foreach ($rightType as $k => $v) {
            $dm = new PwUserBanInfoDm();
            $dm->setUid($uid)->setTypeid($v)->setReason('App ban');
            $dmArray[] = $dm;
        }
        $result = $this->getUserBanService()->banUser($dmArray);
        if ($result instanceof PwError) {
            return $this->buildResponse(-1, $result->getError());
        }

        return $this->buildResponse(0, $result);
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
        $userBo = new PwUserBo(intval($uid));
        if (!$userBo->isExists()) {
            return $this->buildResponse(USER_NOT_EXISTS);
        }
        $result = $this->getForumUser()->getFroumByUid($uid);
        if ($result instanceof PwError) {
            return $this->buildResponse(-1, $result->getError());
        }

        return $this->buildResponse(0, $result);
    }

    /**
     * 加入版块.
     *
     * @param int $uid
     * @param int $fid
     *
     * @return array
     */
    public function addFavoritesForumByUid($uid, $fid)
    {
        $fid = intval($fid);
        if ($fid < 1) {
            return $this->buildResponse(USER_FORUM_NOT_EXIST);
        }
        $userBo = new PwUserBo(intval($uid));
        if (!$userBo->isExists()) {
            return $this->buildResponse(USER_NOT_EXISTS);
        }
        if ($this->getForumUser()->get($uid, $fid)) {
            return $this->buildResponse(
            USER_FORUM_FAVOR_ALREADY);
        }
        $result = $this->getForumUser()->add($uid, $fid);
        if ($result instanceof PwError) {
            return $this->buildResponse(-1, $result->getError());
        }

        return $this->buildResponse(0, $result);
    }

    public function deleteFavoritesForumByUid($uid, $fid)
    {
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
        $ip = Wind::getApp()->getRequest()->getClientIp();
        $result = $this->getLoginService()->login($username, $password, $ip);
        if ($result instanceof PwError) {
            return $this->buildResponse(-1, $result->getError());
        }

        return $this->buildResponse(0, $result['uid']);
    }

    /**
     * 用户注册信息.
     *
     * @return bool|int
     */
    public function userRegister($username, $password, $email)
    {
        if (!trim($username)) {
            return $this->buildResponse(USER_INVALID_USERNAME);
        }
        if (!$password || !$email || WindValidator::isEmail($email) !== true) {
            return $this->buildResponse(
            USER_INVALID_PARAMS);
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

        return $this->buildResponse(0, $result);
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
        $userBo = new PwUserBo(intval($uid));
        if (!$userBo->isExists()) {
            return $this->buildResponse(USER_NOT_EXISTS);
        }
        if (!$email || WindValidator::isEmail($email) !== true) {
            return $this->buildResponse(
            USER_INVALID_PARAMS);
        }

        Wind::import('SRC:service.user.dm.PwUserInfoDm');
        $userDm = new PwUserInfoDm($uid);
        $userDm->setEmail($email);
        $result = $this->getUser()->editUser($userDm, PwUser::FETCH_MAIN);
        if ($result instanceof PwError) {
            return $this->buildResponse(-1, $result->getError());
        }

        return $this->buildResponse(0, $result);
    }

    public function getPrimaryKeyAndTable()
    {
        return array('user', 'uid');
    }

    public function getUsersByRange($startId, $endId)
    {
        list($startId, $endId) = array(intval($startId), intval($endId));
        if ($startId < 0 || $startId > $endId || $endId < 1) {
            return array();
        }
        $result = $members = array();
        $sql = sprintf('SELECT u.* FROM %s u WHERE u.uid >= %s AND u.uid <= %s',
            ACloudSysCoreS::sqlMetadata('{{user}}'), ACloudSysCoreS::sqlEscape($startId),
            ACloudSysCoreS::sqlEscape($endId));
        $query = Wind::getComponent('db')->query($sql);
        $result = $query->fetchAll('uid', PDO::FETCH_ASSOC);
        if (!ACloudSysCoreS::isArray($result)) {
            return array();
        }
        $query = Wind::getComponent('db')->query(
            sprintf('SELECT ud.* FROM %s ud WHERE ud.uid >= %s AND ud.uid <= %s',
                ACloudSysCoreS::sqlMetadata('{{user_data}}'), ACloudSysCoreS::sqlEscape($startId),
                ACloudSysCoreS::sqlEscape($endId)));
        $userData = $query->fetchAll('uid', PDO::FETCH_ASSOC);
        $query = Wind::getComponent('db')->query(
            sprintf('SELECT ui.* FROM %s ui WHERE ui.uid >= %s AND ui.uid <= %s',
                ACloudSysCoreS::sqlMetadata('{{user_info}}'), ACloudSysCoreS::sqlEscape($startId),
                ACloudSysCoreS::sqlEscape($endId)));
        $userInfo = $query->fetchAll('uid', PDO::FETCH_ASSOC);
        foreach ($result as $key => $value) {
            $result[$key] = array_merge($value, $userData[$value['uid']],
                $userInfo[$value['uid']]);
        }
        $siteUrl = ACloudSysCoreCommon::getGlobal('g_siteurl', $_SERVER['SERVER_NAME']);
        foreach ($result as $member) {
            $member['memberurl'] = 'http://'.$siteUrl.'/index.php?m=space&uid='.$member['uid'];
            $member['icon'] = Pw::getAvatar($member['uid']);
            $members[$member['uid']] = $member;
        }

        return $this->filterMemberFields($members);
    }

    private function filterMemberFields($members)
    {
        if (!ACloudSysCoreS::isArray($members)) {
            return array();
        }
        $result = array();
        foreach ($members as $value) {
            unset($value['password']);
            $result[] = $value;
        }

        return $result;
    }

    private function buildInfo($data)
    {
        $data['icon'] = Pw::getAvatar($data['uid']);
        if (isset($data['password'])) {
            unset($data['password']);
        }

        return $data;
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
}
