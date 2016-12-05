<?php

!defined('ACLOUD_PATH') && exit('Forbidden');

define('Friend_INVALID_PARAMS', 101);
define('Friend_NOT_EXISTS', 102);
define('Friend_ALREADY_FOLLOWED', 103);
define('Friend_FOLLOWED_BLACKLIST', 104);
define('Friend_NOT_FOLLOWED', 105);

class ACloudVerCustomizedFriend extends ACloudVerCustomizedBase
{
    public function getAllFriend($uid, $offset, $limit)
    {
    }

    public function searchAllFriend($uid, $keyword, $offset, $limit)
    {
    }

    /**
     * 获取用户关注的人.
     *
     * @param int $uid    用户id
     * @param int $limit
     * @param int $offset
     *
     * @return array
     */
    public function getFollowByUid($uid, $offset, $limit)
    {
        $uid = intval($uid);
        $user = PwUserBo::getInstance($uid);
        if (!$user->isExists()) {
            return $this->buildResponse(Friend_NOT_EXISTS, '好友不存在');
        }
        $attentionResult = $this->getAttention()->getFollows($uid, intval($offset), intval($limit));
        if ($attentionResult instanceof PwError) {
            return $this->buildResponse(-1, $attentionResult->getError());
        }
        $count = $user->info['follows'];
        $result = array();
        $loginUid = Wekit::getLoginUser()->uid;
        foreach ($attentionResult as $k => $v) {
            $result[$k]['uid'] = $v['uid'];
            $result[$k]['icon'] = Pw::getAvatar($v['uid']);
            $result[$k]['username'] = PwUserBo::getInstance($v['uid'])->username;
            $isFollowed = $this->getAttention()->isFollowed($loginUid, $v['uid']);
            $result[$k]['isfollowd'] = ($isFollowed == false) ? 0 : 1;
        }

        return $this->buildResponse(0, array('friends' => $result, 'count' => $count));
    }

    /**
     * 用户(A)关注了用户(B).
     *
     * @param int $uid   用户A
     * @param int $touid 用户B
     *
     * @return bool| object PwError()
     */
    public function addFollowByUid($uid, $touid)
    {
        list($uid, $touid) = array(intval($uid), intval($touid));
        if ($touid < 1 || $uid < 1) {
            return $this->buildResponse(Friend_INVALID_PARAMS, '参数错误');
        }
        $result = $this->getAttentionService()->addFollow($uid, $touid);
        if ($result instanceof PwError) {
            return $this->buildResponse(-1, $result->getError());
        }
        $user = PwUserBo::getInstance($uid);

        return $this->buildResponse(0, array('follows' => $user->info['follows']));
    }

    /**
     * 用户(A)取消了对用户(B)关注.
     *
     * @param int $uid   用户A
     * @param int $touid 用户B
     *
     * @return bool| object PwError()
     */
    public function deleteFollowByUid($uid, $touid)
    {
        list($uid, $touid) = array(intval($uid), intval($touid));
        if ($uid < 1 || $touid < 1) {
            return $this->buildResponse(Friend_INVALID_PARAMS, '参数错误');
        }
        $result = $this->getAttentionService()->deleteFollow($uid, $touid);
        if ($result instanceof PwError) {
            return $this->buildResponse(-1, $result->getError());
        }
        $user = PwUserBo::getInstance($uid);
        $count = $user->info['follows'];

        return $this->buildResponse(0, array('follows' => $count));
    }

    /**
     * 获取用户的粉丝.
     *
     * @param int $uid    用户id
     * @param int $limit
     * @param int $offset
     *
     * @return array
     */
    public function getFanByUid($uid, $offset, $limit)
    {
        list($uid, $offset, $limit) = array(intval($uid), intval($offset), intval($limit));
        if ($uid < 1) {
            $this->buildResponse(Friend_INVALID_PARAMS, '参数错误');
        }
        $fansResult = $this->getAttention()->getFans($uid, $limit, $offset);
        if ($fansResult instanceof PwError) {
            return $this->buildResponse(-1, $fansResult->getError());
        }
        $result = array();
        $loginUid = Wekit::getLoginUser()->uid;
        foreach ($fansResult as $k => $v) {
            $result[$k]['uid'] = $v['uid'];
            $result[$k]['icon'] = Pw::getAvatar($v['uid']);
            $result[$k]['username'] = PwUserBo::getInstance($v['uid'])->username;
            $isFollowed = $this->getAttention()->isFollowed($loginUid, $v['uid']);
            $result[$k]['isfollowd'] = ($isFollowed == false) ? 0 : 1;
        }
        $user = PwUserBo::getInstance($uid);
        $count = $user->info['fans'];

        return $this->buildResponse(0, array('friends' => $result, 'count' => $count));
    }

    private function getAttention()
    {
        return Wekit::load('SRV:attention.PwAttention');
    }

    private function getAttentionService()
    {
        return Wekit::load('SRV:attention.srv.PwAttentionService');
    }
}
