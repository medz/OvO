<?php

!defined('ACLOUD_PATH') && exit('Forbidden');

define('THREAD_INVALID_PARAMS', 301);
define('THREAD_USER_NOT_RIGHT', 302);
define('THREAD_FORUM_NOT_EXIST', 303);
define('THREAD_USER_NOT_EXIST', 304);
define('THREAD_ID_NOT_ILLEGAL', 305);
define('THREAD_EDIT_TIME_LIMIT', 306);
define('THREAD_USER_NOT_HTML_RIGHT', 307);
define('THREAD_SYSTEM_ERROR', 308);
define('THREAD_FAVOR_MAX', 309);
define('THREAD_FAVOR_ALREADY', 310);
define('THREAD_NOT_EXIST', 312);
define('THREAD_LOCKED', 500);
define('POST_GP_LIMIT', 314);
define('THREAD_ALLOW_READ', 315);

class ACloudVerCustomizedPost extends ACloudVerCustomizedBase
{
    /**
     * 获取一个帖子的回复列表.
     *
     * @param int $tid    帖子id
     * @param int $limit
     * @param int $offset
     * @param bool sort
     * return array
     */
    public function getPost($tid, $sort, $offset, $limit)
    {
        list($tid, $sort, $offset, $limit) = array(intval($tid), (bool) $sort, intval($offset), intval($limit));
        $postResult = $this->_getThread()->getPostByTid($tid, $limit, $offset, $sort);
        if ($postResult instanceof PwError) {
            return $this->buildResponse(-1, $postResult->getError());
        }
        $postResult = array_values($postResult);
        $thread = $this->_getThread()->getThread($tid);
        $count = $thread['replies'];
        //TODO 用户阅读和访问权限
        //TODO 回复附件
        $result = array();
        foreach ($postResult as $k => $v) {
            $result[$k]['pid'] = $v['pid'];
            $result[$k]['aid'] = $v['aids'];
            $result[$k]['tid'] = $v['tid'];
            $result[$k]['author'] = $v['created_username'];
            $result[$k]['authorid'] = $v['created_userid'];
            $result[$k]['icon'] = Pw::getAvatar($v['created_userid']);
            $result[$k]['postdate'] = $v['created_time'];
            $result[$k]['subject'] = $v['subject'];
            $result[$k]['content'] = $v['content'];
            $result[$k]['attachlist'] = '';
        }

        return $this->buildResponse(0, array('count' => $count, 'posts' => $result));
    }

    /**
     * 获取用户的回复.
     *
     * @param int $uid    用户id
     * @param int $limit  个数
     * @param int $offset 起始偏移量
     *                    return array
     */
    public function getPostByUid($uid, $offset, $limit)
    {
        list($uid, $offset, $limit) = array(intval($uid), intval($offset), intval($limit));
        $user = PwUserBo::getInstance($uid);
        if (!$user->username) {
            return $this->buildResponse(THREAD_USER_NOT_EXIST, '用户不存在');
        }
        $postResult = $this->_getThread()->getPostByUid($uid, $limit, $offset);
        if ($postResult instanceof PwError) {
            return $this->buildResponse(-1, $postResult->getError());
        }
        $tids = array();
        foreach ($postResult as $v) {
            $tids[] = $v['tid'];
        }
        $threads = $this->_getThread()->fetchThread($tids);
        if ($threads instanceof PwError) {
            return $this->buildResponse(-1, $threads->getError());
        }
        $postResult = array_values($postResult);
        $count = $this->_getThread()->countThreadByUid($uid);
        $result = array();

        foreach ($postResult as $k => $v) {
            $result[$k]['pid'] = $v['pid'];
            $result[$k]['tid'] = $v['tid'];
            $result[$k]['author'] = $uid;
            $result[$k]['authorid'] = $user->username;
            $result[$k]['subject'] = $v['subject'];
            $result[$k]['postdate'] = $v['created_time'];
            $result[$k]['icon'] = Pw::getAvatar($uid);
            $result[$k]['content'] = $v['content'];
            isset($threads[$v['tid']]) && $result[$k]['threadsubject'] = $threads[$v['tid']]['subject'];
            $result[$k]['attachlist'] = '';
            $result[$k]['fid'] = $v['fid'];
        }

        return $this->buildResponse(0, array('count' => $count, 'posts' => $result));
    }

    /**
     * 获取用户(A)在帖子(B)中的回复.
     *
     * @param int $tid
     * @param int $uid
     * @param int $limit
     * @param int $offset
     *
     * @return array
     */
    public function getPostByTidAndUid($tid, $uid, $offset, $limit)
    {
        list($uid, $tid, $offset, $limit) = array(intval($uid), intval($tid), intval($offset), intval($limit));
        $user = PwUserBo::getInstance($uid);
        if (!$user->username) {
            return $this->buildResponse(THREAD_USER_NOT_EXIST, '用户不存在');
        }
        $postResult = $this->_getThread()->getPostByTidAndUid($tid, $uid, $limit, $offset);
        if ($postResult instanceof PwError) {
            return $this->buildResponse(-1, $postResult->getError());
        }
        $postResult = array_values($postResult);
        $count = $this->_getThread()->countPostByTidAndUid($tid, $uid);
        $result = array();

        foreach ($postResult as $k => $v) {
            $result[$k]['pid'] = $v['pid'];
            $result[$k]['tid'] = $v['tid'];
            $result[$k]['author'] = $uid;
            $result[$k]['authorid'] = $user->username;
            $result[$k]['subject'] = $v['subject'];
            $result[$k]['postdate'] = $v['created_time'];
            $result[$k]['icon'] = Pw::getAvatar($uid);
            $result[$k]['content'] = $v['content'];
            $result[$k]['attachlist'] = '';
            $result[$k]['fid'] = $v['fid'];
        }

        return $this->buildResponse(0, array('count' => $count, 'posts' => $result));
    }

    /**
     * 获取帖子详细页最新回复.
     *
     * @param int $tid
     * @param int $page
     * @param int $limit
     * @param int $offset
     *
     * @return array
     */
    public function getLatestPost($tid, $page, $offset = 20, $limit = 0)
    {
        Wind::import('SRV:forum.bo.PwThreadBo');
        $thread = new PwThreadBo($tid);
        if (!$thread) {
            $this->buildResponse(-1, 'THREAD_IS_NOT_EXISTS');
        }
        $total = $thread->info['replies'] + 1;
        $maxPage = ceil($total / $offset);
        if ($thread->info['replies'] > 0) {
            list($start, $limit) = Pw::page2limit($maxPage, $offset);
            $reply = $this->_getThread()->getPostByTid($tid, $limit, $start, false);
        }

        return $this->buildResponse(0, array('count' => $thread->info['replies'], 'reply' => $reply));
    }

    /**
     * 发送回复.
     *
     * @param int    $tid
     * @param int    $uid
     * @param string $title
     * @param string $content
     *                        return bool
     */
    public function sendPost($tid, $uid, $title, $content)
    {
        list($uid, $tid, $title, $content) = array(intval($uid), intval($tid), trim($title), trim($content));
        if ($uid < 1 || $tid < 1 || !$content) {
            return $this->buildResponse(THREAD_INVALID_PARAMS, '参数错误');
        }
        if ($this->_getOnline()->isOnline($uid) !== true) {
            $this->buildResponse(USER_NOT_LOGIN, '用户没有登录');
        }

        Wind::import('SRV:forum.srv.PwPost');
        Wind::import('SRV:forum.srv.post.PwReplyPost');
        $postAction = new PwReplyPost($tid);
        $pwPost = new PwPost($postAction);
        $info = $pwPost->getInfo();
        $title == 'Re:'.$info['subject'] && $title = '';
        $postDm = $pwPost->getDm();
        $postDm->setTitle($title)->setContent($content)->setAuthor($uid, $user->username, $user->ip);
        if (($result = $pwPost->execute($postDm)) !== true) {
            $this->buildResponse(-1, $result->getError());
        }
        $postId = $pwPost->getNewId();

        return $this->buildResponse(0, array('pid' => $postId));
    }

    public function checkSensitiveWord($word)
    {
        if (empty($word) || is_array($word)) {
            return $this->buildResponse(-1, '传入参数不合法');
        }
        $result = $this->_loadPwWordFilter()->filter($word);
        if ($result) {
            return $this->buildResponse(500, '帖子被锁定');
        }

        return $this->buildResponse(0);
    }

    private function _loadPwWordFilter()
    {
        return Wekit::load('SRV:word.srv.PwWordFilter');
    }

    private function _getThread()
    {
        return Wekit::load('SRV:forum.PwThread');
    }

    private function _getOnline()
    {
        return Wekit::load('SRV:online.PwUserOnline');
    }
}
