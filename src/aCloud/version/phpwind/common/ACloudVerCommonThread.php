<?php

!defined('ACLOUD_PATH') && exit('Forbidden');

define('THREAD_INVALID_PARAMS', 301);
define('THREAD_USER_NOT_RIGHT', 302);
define('THREAD_FORUM_NOT_EXIST', 303);
define('THREAD_USER_NOT_EXIST', 304);
define('THREAD_ID_NOT_ILLEGAL', 305);
define('THREAD_EDIT_TIME_LIMIT', 306);
define('THREAD_USER_NOT_HTML_RIGHT', 307);
define('THREAD_SYSTEM_ERROR', 500);
define('THREAD_NOT_EXIST', 312);
define('THREAD_ALLOW_READ', 315);

class ACloudVerCommonThread extends ACloudVerCommonBase
{
    /**
     * 获取单个帖子信息.
     *
     * @param int $tid 帖子id
     *                 return array
     */
    public function getByTid($tid)
    {
        $tid = intval($tid);
        if ($tid < 1) {
            return $this->buildResponse(THREAD_ID_NOT_ILLEGAL);
        }
        $result = $this->_getThread()->getThread($tid);
        if ($result instanceof PwError) {
            return $this->buildResponse(-1, $result->getError());
        }

        return $this->buildResponse(0, $result);
    }

    /**
     * 获取用户的帖子.
     *
     * @param int $uid    用户id
     * @param int $limit  个数
     * @param int $offset 起始偏移量
     *                    return array
     */
    public function getByUid($uid, $offset = 0, $limit = 20)
    {
        $userBo = new PwUserBo($uid);
        if (!$userBo->isExists()) {
            return $this->buildResponse(THREAD_USER_NOT_EXIST);
        }
        $result = $this->_getThread()->getThreadByUid($uid, $limit, $offset);
        if ($result instanceof PwError) {
            return $this->buildResponse(-1, $result->getError());
        }

        return $this->buildResponse(0, $result);
    }

    public function getLatestThread($fids, $offset, $limit)
    {
    }

    public function getLatestThreadByFavoritesForum($uid, $offset, $limit)
    {
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
            return $this->buildResponse(THREAD_INVALID_PARAMS);
        }
        $_attaches = $this->_getThreadAttach()->getAttachByTid($tid, 0);
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

    public function getToppedThreadByFid($fid, $offset, $limit)
    {
    }

    /**
     * 获取某个版块的帖子列表.
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
            return $this->buildResponse(THREAD_FORUM_NOT_EXIST);
        }
        $result = $this->_getThread()->getThreadByFid($fid, $limit, $offset);
        if ($result instanceof PwError) {
            return $this->buildResponse(-1, $result->getError());
        }

        return $this->buildResponse(0, $result);
    }

    public function getAtThreadByUid($uid, $offset, $limit)
    {
    }

    public function getThreadByTopic($topic, $offset, $limit)
    {
    }

    /**
     * 发表帖子.
     *
     * @param int    $tid
     * @param int    $fid
     * @param string $subject
     * @param string $content
     *                        return bool
     */
    public function postThread($uid, $fid, $subject, $content)
    {
        $userBo = new PwUserBo($uid);
        if (!$userBo->isExists()) {
            return $this->buildResponse(THREAD_USER_NOT_EXIST);
        }
        Wind::import('SRV:forum.srv.PwPost');
        Wind::import('SRV:forum.srv.post.PwTopicPost');
        $postAction = new PwTopicPost($fid);
        $pwPost = new PwPost($postAction);
        $postDm = $pwPost->getDm();
        $postDm->setFid($fid)->setTitle($subject)->setContent($content)->setAuthor($uid, $userBo->username, $userBo->ip);
        if (($result = $pwPost->execute($postDm)) !== true) {
            $this->buildResponse(-1, $result->getError());
        }

        return $this->buildResponse(0, $result);
    }

    public function shieldThread($tid, $fid)
    {
    }

    public function getPrimaryKeyAndTable()
    {
        return array('bbs_threads', 'tid');
    }

    public function getThreadsByRange($startId, $endId)
    {
        list($startId, $endId) = array(intval($startId), intval($endId));
        if ($startId < 0 || $startId > $endId || $endId < 1) {
            return array();
        }
        $sql = sprintf('SELECT t.* FROM %s t WHERE t.fid != 0 AND t.ischeck = 1 AND t.tid >= %s AND t.tid <= %s', ACloudSysCoreS::sqlMetadata('{{bbs_threads}}'), ACloudSysCoreS::sqlEscape($startId), ACloudSysCoreS::sqlEscape($endId));
        $query = Wind::getComponent('db')->query($sql);
        $result = $query->fetchAll(null, PDO::FETCH_ASSOC);
        if (!ACloudSysCoreS::isArray($result)) {
            return array();
        }
        $result = $this->getContentAndForumInfo($result);

        return $this->_buildThreadData($result);
    }

    public function getThreadDeltaCount($startTime, $endTime)
    {
        list($startTime, $endTime) = array(intval($startTime), intval($endTime));
        if ($startTime < 1 || $endTime < 1 || $startTime > $endTime) {
            return 0;
        }
        $sql = sprintf('SELECT COUNT(*) as count FROM %s WHERE fid != 0 AND ischeck = 1 AND modified_time >= %s AND modified_time <= %s', ACloudSysCoreS::sqlMetadata('{{bbs_threads}}'), ACloudSysCoreS::sqlEscape($startTime), ACloudSysCoreS::sqlEscape($endTime));
        $query = Wind::getComponent('db')->query($sql);

        return current($query->fetch(PDO::FETCH_ASSOC));
    }

    public function getThreadsByModifiedTime($startTime, $endTime, $page, $perpage)
    {
        list($startTime, $endTime, $page, $perpage) = array(intval($startTime), intval($endTime), intval($page), intval($perpage));
        if ($startTime < 1 || $endTime < 1 || $startTime > $endTime || $page < 1 || $perpage < 1) {
            return array();
        }
        $offset = ($page - 1) * $perpage;
        $sql = sprintf('SELECT t.* FROM %s t WHERE t.fid != 0 AND t.ischeck = 1 AND t.modified_time >= %s AND t.modified_time <= %s %s', ACloudSysCoreS::sqlMetadata('{{bbs_threads}}'), ACloudSysCoreS::sqlEscape($startTime), ACloudSysCoreS::sqlEscape($endTime), ACloudSysCoreS::sqlLimit($offset, $perpage));
        $query = Wind::getComponent('db')->query($sql);
        $result = $query->fetchAll(null, PDO::FETCH_ASSOC);
        if (!ACloudSysCoreS::isArray($result)) {
            return array();
        }
        $result = $this->getContentAndForumInfo($result);

        return $this->_buildThreadData($result);
    }

    private function getContentAndForumInfo($result)
    {
        $tids = $fids = array();
        foreach ($result as $value) {
            $tids[] = $value['tid'];
            $fids[] = $value['fid'];
        }
        $query = Wind::getComponent('db')->query(sprintf('SELECT * FROM %s WHERE tid IN(%s)', ACloudSysCoreS::sqlMetadata('{{bbs_threads_content}}'), ACloudSysCoreS::sqlImplode(array_unique($tids))));
        $contents = $query->fetchAll('tid', PDO::FETCH_ASSOC);
        $query = Wind::getComponent('db')->query(sprintf('SELECT fid, name as forumname FROM %s WHERE fid IN(%s)', ACloudSysCoreS::sqlMetadata('{{bbs_forum}}'), ACloudSysCoreS::sqlImplode(array_unique($fids))));
        $forums = $query->fetchAll('fid', PDO::FETCH_ASSOC);
        foreach ($result as $key => $thread) {
            $result[$key] = array_merge($thread, (array) $contents[$thread['tid']]);
            $result[$key]['forumname'] = isset($forums[$thread['fid']]) ? $forums[$thread['fid']]['forumname'] : '';
        }

        return $result;
    }

    private function _buildThreadData($data)
    {
        list($result, $siteUrl) = array(array(), ACloudSysCoreCommon::getGlobal('g_siteurl', $_SERVER['SERVER_NAME']));
        foreach ($data as $value) {
            $value['threadurl'] = 'http://'.$siteUrl.'/read.php?tid='.$value['tid'];
            $value['forumurl'] = 'http://'.$siteUrl.'/index.php?m=bbs&c=thread&fid='.$value['fid'];
            $result[$value['tid']] = $value;
        }

        return $result;
    }

    private function _getThread()
    {
        return Wekit::load('SRV:forum.PwThread');
    }

    private function _getThreadAttach()
    {
        return Wekit::load('SRV:attach.PwThreadAttach');
    }
}
