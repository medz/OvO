<?php

! defined('ACLOUD_PATH') && exit('Forbidden');

define('THREAD_INVALID_PARAMS', 301);
define('THREAD_USER_NOT_RIGHT', 302);
define('THREAD_FORUM_NOT_EXIST', 303);
define('THREAD_USER_NOT_EXIST', 304);
define('THREAD_ID_NOT_ILLEGAL', 305);
define('THREAD_EDIT_TIME_LIMIT', 306);
define('THREAD_USER_NOT_HTML_RIGHT', 307);
define('THREAD_SYSTEM_ERROR', 500);
define('THREAD_FAVOR_MAX', 309);
define('THREAD_FAVOR_ALREADY', 310);
define('THREAD_NOT_EXIST', 312);
define('THREAD_LOCKED', 500);
define('POST_GP_LIMIT', 314);
define('THREAD_ALLOW_READ', 315);
define('USER_NOT_EXISTS', 316);

Wind::import('SRV:forum.bo.PwThreadBo');

class ACloudVerCustomizedThread extends ACloudVerCustomizedBase
{
    /**
     * 获取单个帖子信息
     *
     * @param int $tid 帖子id
     *                 return array
     */
    public function getByTid($tid)
    {
        $tid = intval($tid);
        if ($tid < 1) {
            return $this->buildResponse(THREAD_INVALID_PARAMS, '参数不存在');
        }
            //TODO 权限


        $thread = new PwThreadBo($tid);
        $thread = $thread->info;
        if ($thread instanceof PwError) {
            return $this->buildResponse(- 1, $thread->getError());
        }
        $result = array();
        $user = Wekit::getLoginUser();

        $result ['tid'] = $thread ['tid'];
        $result ['fid'] = $thread ['fid'];
        $result ['icon'] = Pw::getAvatar($thread ['created_userid']);
        $result ['titlefont'] = ''; //TODO 标题字体
        $result ['author'] = $thread ['created_username'];
        $result ['authorid'] = $thread ['created_userid'];
        $result ['subject'] = $thread ['subject'];
        $result ['type'] = $thread ['topic_type'];
        $result ['postdate'] = $thread ['create_time'];
        $result ['lastpost'] = $thread ['lastpost_time'];
        $result ['lastposter'] = $thread ['lastpost_username'];
        $result ['hits'] = $thread ['hits'];
        $result ['replies'] = $thread ['replies'];
        $result ['topped'] = $thread ['topped'];
        $result ['locked'] = $thread ['locked'];
        $result ['digest'] = $thread ['digest'];
        $result ['special'] = $thread ['special'];
        $result ['state'] = $thread ['thread_status']; //帖子状态
        $result ['tpcstatus'] = $thread ['thread_status'];
        $result ['specialsort'] = $thread ['special_sort'];
        $result ['uid'] = $user->uid;
        $result ['groupid'] = $user->gid;
        $result ['userip'] = $user->ip;
        $result ['ifsign'] = $user->bbs_sign;
        $result ['ipfrom'] = $thread ['created_ip'];
        $result ['content'] = $thread ['content'];
        $result ['attachlist'] = $thread ['attach'];

        return $this->buildResponse(0, $result);
    }

    /**
     * 获取用户的帖子
     *
     * @param int $uid    用户id
     * @param int $limit  个数
     * @param int $offset 起始偏移量
     *                    return array
     */
    public function getByUid($uid, $offset = 0, $limit = 20)
    {
        $user = PwUserBo::getInstance($uid);
        if (! $user->username) {
            return $this->buildResponse(THREAD_USER_NOT_EXIST, '用户不存在');
        }
        $thread = $this->_getThread()->getThreadByUid($uid, $limit, $offset);
        if ($thread instanceof PwError) {
            return $this->buildResponse(- 1, $thread->getError());
        }
        $result = array();
        $thread = array_values($thread);
        foreach ($thread as $k => $v) {
            $result [$k] ['tid'] = $v ['tid'];
            $result [$k] ['fid'] = $v ['fid'];
            $forum = $this->_getForum()->getForum($v ['fid']);
            $result [$k] ['forumname'] = $forum ['name'];
            $result [$k] ['icon'] = Pw::getAvatar($user->uid);
            $result [$k] ['author'] = $user->username;
            $result [$k] ['authorid'] = $user->uid;
            $result [$k] ['subject'] = $v ['subject'];
            $result [$k] ['postdate'] = $v ['created_time'];
        }

        return $this->buildResponse(0, array('threads' => $result));
    }


    public function getLatestThread($fids, $offset, $limit)
    {
        Wind::import('SRV:forum.vo.PwThreadSo');
        $fids = $fids ? explode(',', $fids) : '';
        $forums = $this->_getForum()->fetchForum($fids, PwForum::FETCH_MAIN);
        $uids = $result = array();
        $threaddb = array();
        $so = new PwThreadSo();
        foreach ($fids as $fid) {
            $so->setFid($fid)
              ->setDisabled(0)
              ->orderbyCreatedTime(0);
            $thread = array_values($this->_getThread()->searchThread($so, 1, 0));
            $threaddb[$fid] = $thread[0];
        }
        if (!$threaddb) {
            return $this->buildResponse(THREAD_NOT_EXIST, '帖子不存在');
        }
        foreach ($threaddb as $key => $value) {
            if (!$value) {
                continue;
            }
            $result[$key]['fid'] = $value['fid'];
            $result[$key]['tid'] = $value['tid'];
            $result[$key]['forumname'] = strip_tags($forums[$value]['name']);
            $result[$key]['author'] = $value['created_username'];
            $result[$key]['authorid'] = $value['created_userid'];
            $result[$key]['icon'] = Pw::getAvatar($value['created_userid']);
            $uids[$value['tid']] = $value['created_userid'];
        }

        return $this->buildResponse(0, $result);
    }

    public function getLatestThreadByFavoritesForum($uid, $offset, $limit)
    {
        list($uid, $offset, $limit) = array(intval($uid), intval($offset), intval($limit));
        if ($uid <= 0) {
            return $this->buildResponse(THREAD_INVALID_PARAMS, '参数错误');
        }
        $userBo = new PwUserBo($uid);
        if (! $userBo->isExists()) {
            return $this->buildResponse(USER_NOT_EXISTS, '用户不存在');
        }
        $fids = array_keys($this->_getForumUser()->getFroumByUid($uid));
        if (!$fids) {
            return $this->buildResponse(0, array());
        }

        return $this->getLatestThreadsByFids($fids, $offset, $limit);
    }

    public function getLatestThreadByFollowUser($uid, $offset, $limit)
    {
    }

    public function getLatestImgThread($fids, $offset, $limit)
    {
    }

    public function getThreadImgs($tid)
    {
        $tid = intval($tid);
        if ($tid < 1) {
            return $this->buildResponse(THREAD_INVALID_PARAMS, '参数错误');
        }
        $_attaches = $this->_getThreadAttach()->getAttachByTid($tid, (array) 0);
        $attaches = array();
        foreach ($_attaches as $v) {
            if ($v['type'] != 'img') {
                continue;
            }
            $attaches['img'][] = array('url' => Pw::getPath($v['path']));
        }
        $attaches['count'] = count($attaches['img']);

        return $this->buildResponse(0, $attaches);
    }

    /**
     * 获取版块的置顶帖
     *
     * @param int $fid    版块id
     * @param int $limit  个数
     * @param int $offset 起始偏移量
     *                    return array
     */
    public function getToppedThreadByFid($fid, $offset, $limit)
    {
        list($fid, $offset, $limit) = array(intval($fid), intval($offset), intval($limit));
        if ($fid < 1) {
            return $this->buildResponse(THREAD_FORUM_NOT_EXIST, '版块不存在');
        }
        $tops = $this->_getSpecialSortDs()->getSpecialSortByFid($fid);
        if ($tops instanceof PwError) {
            return $this->buildResponse(- 1, $tops->getError());
        }
        $forum = $this->_getForum()->getForum($fid);
        if (! $forum) {
            return $this->buildResponse(THREAD_FORUM_NOT_EXIST, '版块不存在');
        }

        $specialCount = count($tops);
        $topThreads = array();
        if ($specialCount > $offset) {
            $topThreads = $this->_getThread()->fetchThreadByTid(array_keys($tops), $limit, $offset);
            if ($topThreads instanceof PwError) {
                return $this->buildResponse(- 1, $topThreads->getError());
            }
            if ($specialCount - $offset < $limit) {
                $limit = min($limit - $specialCount + $offset, $limit);
                $start = max($start - $specialCount, 0);
            } else {
                $limit = 0;
            }
        }

        $result = array();
        foreach ($topThreads as $v) {
            $tid = $v ['tid'];
            $result [$tid] ['tid'] = $tid;
            $result [$tid] ['fid'] = $v ['fid'];
            $result [$tid] ['author'] = $v ['created_username'];
            $result [$tid] ['authorid'] = $v ['created_userid'];
            $result [$tid] ['subject'] = $v ['subject'];
            $result [$tid] ['postdate'] = $v ['created_time'];
            $result [$tid] ['hits'] = $v ['hits'];
            $result [$tid] ['replies'] = $v ['replies'];
            $result [$tid] ['forumname'] = $forum ['name'];
            $result [$tid] ['icon'] = Pw::getAvatar($v ['created_userid']);
        }

        return $this->buildResponse(0, array('threads' => $result, 'count' => $specialCount));
    }

    /**
     * 获取某个版块的帖子列表
     *
     * @param int $fid    版块id
     * @param int $limit  个数
     * @param int $offset 起始偏移量
     *                    return array
     */
    public function getThreadByFid($fid, $offset, $limit)
    {
        $fid = intval($fid);
        if ($fid < 1) {
            return $this->buildResponse(THREAD_FORUM_NOT_EXIST, '版块不存在');
        }
        $result = $this->_getThread()->getThreadByFid($fid, $limit, $offset);
        if ($result instanceof PwError) {
            return $this->buildResponse(- 1, $result->getError());
        }
        $threads = array();
        $forumStatics = $this->_getForum()->getForum($fid, 2);
        $count = $forumStatics ['threads'];
        foreach ($result as $k => $v) {
            $threads [$k] ['tid'] = $v ['tid'];
            $threads [$k] ['fid'] = $v ['fid'];
            $threads [$k] ['author'] = $v ['created_username'];
            $threads [$k] ['authorid'] = $v ['created_userid'];
            $threads [$k] ['subject'] = $v ['subject'];
            $threads [$k] ['postdate'] = $v ['created_time'];
            $forum = $this->_getForum()->getForum($v ['fid']);
            $threads [$k] ['forumname'] = $forum ['name'];
            $threads [$k] ['hits'] = $v ['hits'];
            $threads [$k] ['replies'] = $v ['replies'];
            $threads [$k] ['icon'] = Pw::getAvatar($v ['created_userid']);
        }

        return $this->buildResponse(0, array('count' => $count, 'threads' => $threads));
    }

    public function getAtThreadByUid($uid, $offset, $limit)
    {
    }

    public function getThreadByTopic($topic, $offset, $limit)
    {
    }

    /**
     *
     * 获取帖子详细页只看楼主的回复
     *
     * @param int $tid
     * @param int $uid
     */
    public function getByTidAndUid($tid, $uid, $page, $offset = 0, $limit = 10)
    {
        list($tid, $uid, $page, $offset, $limit) = array(intval($tid), intval($uid), intval($page), intval($offset), intval($limit));
        if ($tid < 1 || $uid < 1) {
            return $this->buildResponse(THREAD_INVALID_PARAMS, '参数错误');
        }
        $user = PwUserBo::getInstance($uid);
        if (! $user->isExists()) {
            return $this->buildResponse(USER_NOT_EXISTS, '用户不存在');
        }
        list($start, $limit) = Pw::page2limit($page, $limit);
        $postResult = $this->_getThread()->getPostByTidAndUid($tid, $uid, $limit, $start);
        if (!$postResult) {
            return $this->buildResponse(-1, $postResult->getError());
        }

        Wind::import('SRV:forum.vo.PwPostSo');
        $so = new PwPostSo();
        $so->setTid($tid)
        ->setAuthorId($uid);
        $count = $this->_getThread()->countSearchPost($so);

        return $this->buildResponse(0, array('posts' => $postResult, 'count' => $count));
    }

    /**
     * 发表帖子
     * @param int    $tid
     * @param int    $fid
     * @param string $subject
     * @param string $content
     *                        return bool
     */
    public function postThread($uid, $fid, $subject, $content)
    {
        list($uid, $fid, $subject, $content) = array(intval($uid), intval($fid), trim($subject), trim($content));
        if ($uid < 1 || $fid < 1 || ! $subject || ! $content) {
            return $this->buildResponse(THREAD_INVALID_PARAMS, '参数错误');
        }
        $user = PwUserBo::getInstance($uid);
        if (! $user->isExists()) {
            return $this->buildResponse(USER_NOT_EXISTS, '用户不存在');
        }
        Wind::import('SRV:forum.srv.PwPost');
        Wind::import('SRV:forum.srv.post.PwTopicPost');
        $postAction = new PwTopicPost($fid);
        $pwPost = new PwPost($postAction);
        $postDm = $pwPost->getDm();
        $postDm->setFid($fid)->setTitle($subject)->setContent($content)->setAuthor($uid, $user->username, $user->ip);
        if (($result = $pwPost->execute($postDm)) !== true) {
            $this->buildResponse(- 1, $result->getError());
        }
        $tid = $pwPost->getNewId();

        return $this->buildResponse(0, array('tid' => $tid));
    }

    public function getLatestThreadsByFids($fids, $offset, $limit)
    {
        if (! ACloudSysCoreS::isArray($fids)) {
            return $this->buildResponse(0, array());
        }
        Wind::import('SRV:forum.vo.PwThreadSo');
        $uids = $result = array();
        $threaddb = array();
        $so = new PwThreadSo();
        $so->setFid($fids)
            ->setDisabled(0)
            ->orderbyCreatedTime(0);
        $threaddb = $this->_getThread()->searchThread($so, $limit, $offset);
        if (!$threaddb) {
            return $this->buildResponse(THREAD_NOT_EXIST, '帖子不存在');
        }
        $forums = $this->_getForum()->fetchForum($fids, PwForum::FETCH_MAIN);
        $result = array();
        foreach ($threaddb as $key => $value) {
            if (!$value) {
                continue;
            }
            $value['forumname'] = strip_tags($forums[$value]['name']);
            $value['icon'] = Pw::getAvatar($value['created_userid']);
            $uids[$value['tid']] = $value['created_userid'];
            $result['threads'][$value['tid']] = $value;
        }

        return $this->buildResponse(0, $result);
    }

    private function _getThread()
    {
        return Wekit::load('SRV:forum.PwThread');
    }

    private function _getForum()
    {
        return Wekit::load('SRV:forum.PwForum');
    }

    private function _getSpecialSortDs()
    {
        return Wekit::load('SRV:forum.PwSpecialSort');
    }

    private function _getForumUser()
    {
        return Wekit::load('SRV:forum.PwForumUser');
    }

    private function _getThreadAttach()
    {
        return Wekit::load('SRV:attach.PwThreadAttach');
    }
}
