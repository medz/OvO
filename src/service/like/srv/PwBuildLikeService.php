<?php

Wind::import('LIB:ubb.PwSimpleUbbCode');
Wind::import('LIB:ubb.config.PwUbbCodeConvertThread');
/**
 * 喜欢列表组装服务
 *
 * 喜欢类型扩展流程：
 * <1>PwLikeContent 增加类型const 变量
 * <2>PwLikeContent->transformTypeid() 增加类型转换
 * <3>PwBuildLikeService->_getDataFrom??? 增加喜欢内容
 * <4>PwBuildLikeService->_getReplyFrom??? 增加喜欢回复内容
 * <5>PwLikeService->_getSpecialAndBeLikeuid 增加被喜欢信息
 * the last known user to change this file in the repository  <$LastChangedBy: jieyin $>
 * @author $Author: jieyin $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwBuildLikeService.php 22678 2012-12-26 09:22:23Z jieyin $
 * @package
 */
class PwBuildLikeService
{
    private $_likeids = array();
    private $_infoids = array();
    private $_likeInfo = array();
    private $_fids = array();
    private $_lastpids = array();
    private $_replyInfo = array();
    private $_myTagids = array();

    public function getTagsByUid($uid)
    {
        $tags = $this->_getLikeTagDs()->getInfoByUid($uid);
        $this->_myTagids = array_keys($tags);

        return $tags;
    }
    /**
     * 分页获取喜欢记录
     *
     * @param int $uid
     * @param int $page
     * @param int $limit
     */
    public function getLogList($uid, $start, $limit = 10)
    {
        $ds = $this->_getLikeLogDs();
        $logLists = $ds->getInfoList($uid, $start, $limit);
        if (!is_array($logLists) || count($logLists) < 1) {
            return array();
        }
        foreach ($logLists as &$logList) {
            $logList['tags'] = array();
            if ($logList['likeid'] < 1) {
                continue;
            }
            $this->_likeids[] = $logList['likeid'];
            $logList['tags'] = empty($logList['tagids']) ? array() : explode(',', $logList['tagids']);
            foreach ($logList['tags'] as $k => $tag) {
                if (!in_array($tag, $this->_myTagids)) {
                    unset($logList['tags'][$k]);
                }
            }
        }

        return $logLists;
    }

    /**
     * 获取我关注的喜欢
     *
     * @param int $uid
     * @param int $start
     * @param int $limit
     */
    public function getFollowLogList($uid, $start = 0, $limit = 10)
    {
        $follows = Wekit::load('attention.PwAttention')->getFollows($uid, 100, 0);
        $uids = array_keys($follows);
        if (count($uids) < 1) {
            return array();
        }
        $ds = $this->_getLikeLogDs();
        $logLists = $ds->getInfoList($uids, $start, $limit);
        if (!is_array($logLists) || count($logLists) < 1) {
            return array();
        }
        $_tmpIds = array();
        foreach ($logLists as $key => $logList) {
            $logList['tags'] = array();
            if ($logList['likeid'] < 1) {
                continue;
            }
            if ($_tmpIds[$logList['likeid']]) {
                $logLists[$_tmpIds[$logList['likeid']]]['uids'][] = $logList['uid'];
                unset($logLists[$key]);
                continue;
            }
            $this->_likeids[] = $logList['likeid'];
            $_tmpIds[$logList['likeid']] = $logList['logid'];
            $logLists[$key]['uids'] = array($logList['uid']);
            $logLists[$key]['tags'] = empty($logList['tagids']) ? array() : explode(',', $logList['tagids']);
        }
        for ($i = 1; $i < 10 && count($_tmpIds) < $limit; $i++) {
            $appendLog = $ds->getInfoList($uids, $limit + $i, 1);
            if (!$appendLog) {
                break;
            }
            $append = array_shift($appendLog);
            if ($append['likeid'] < 1) {
                continue;
            }
            if ($_tmpIds[$append['likeid']]) {
                $logLists[$_tmpIds[$append['likeid']]]['uids'][] = $append['uid'];
                continue;
            }
            $this->_likeids[] = $append['likeid'];
            $_tmpIds[$append['likeid']] = $append['logid'];
            $logLists[$append['logid']] = $append;
            $logLists[$append['logid']]['uids'] = array($append['uid']);
            $logLists[$append['logid']]['tags'] = empty($append['tagids']) ? array() : explode(',', $append['tagids']);
        }

        return $logLists;
    }

    public function getLogidsByTagid($tagid, $page, $limit)
    {
        $logids = array();
        $ds = $this->_getLikeRelationsDs();
        list($start, $limit) = Pw::page2limit($page, $limit);
        $logLists = $ds->getInfoList($tagid, $start, $limit);
        if (!is_array($logLists) || count($logLists) < 1) {
            return array();
        }
        foreach ($logLists as $logList) {
            $logids[] = $logList['logid'];
        }

        return $logids;
    }

    public function getLogLists($logids)
    {
        $logLists = $this->_getLikeLogDs()->fetchLikeLog($logids);
        foreach ($logLists as &$logList) {
            $logList['tags'] = empty($logList['tagids']) ? array() : explode(',', $logList['tagids']);
            if ($logList['likeid'] < 1) {
                continue;
            }
            $this->_likeids[] = $logList['likeid'];
        }

        return $logLists;
    }

    /**
     * 喜欢对应关系转换
     *
     * 根据业务规则增加查询类型appendId
     */
    public function getLikeList()
    {
        $ds = $this->_getLikeContentDs();
        $likeLists = $ds->fetchLikeContent($this->_likeids);
        if (!is_array($likeLists) || count($likeLists) < 1) {
            return array();
        }
        foreach ($likeLists as $likeList) {
            $from = $ds->transformTypeid($likeList['typeid']);
            if (!$from) {
                continue;
            }
            $this->_appendId($from, $likeList['likeid'], $likeList['fromid']);
            $this->_appendPid($from, $likeList['likeid'], $likeList['reply_pid']);
        }

        return $likeLists;
    }

    /**
     * 获取喜欢内容
     *
     */
    public function getLikeInfo()
    {
        if (!is_array($this->_infoids) || count($this->_infoids) < 1) {
            return array();
        }
        $_tmpInfo = array();
        foreach ($this->_infoids as $key => $infoids) {
            $func = '_getDataFrom'.ucfirst($key);
            $infos = $this->$func($infoids);
            $this->setLikeOrder($infoids, $infos);
        }
        //$this->_bindForum();
        return $this->_likeInfo;
    }

    public function getLastReplyInfo()
    {
        if (!is_array($this->_lastpids) || count($this->_lastpids) < 1) {
            return array();
        }
        $_tmpInfo = array();
        foreach ($this->_lastpids as $key => $infoids) {
            $func = '_getReplyFrom'.ucfirst($key);
            $this->$func($infoids);
        }

        return $this->_replyInfo;
    }

    private function _appendId($type, $likeid, $infoid)
    {
        $this->_infoids[$type][$likeid] = $infoid;
    }

    private function _appendPid($type, $likeid, $infoid)
    {
        if ($infoid) {
            $this->_lastpids[$type][$likeid] = $infoid;
        }
    }

    private function _appendFid($fid)
    {
        $this->_fids[] = $fid;
    }

    private function _buildLikeContent($content)
    {
        //$content = Pw::stripWindCode($content);
        //return Pw::substrs($content,140);
        $errcode = array();

        return $this->_bulidContent($content, 1, $errcode);
    }

    protected function _bulidContent($content, $ubb, &$errcode)
    {
        $errcode = array();
        $content = str_replace(array("\r", "\n", "\t"), '', $content);
        $content = WindSecurity::escapeHTML($content);
        if ($ubb) {
            $content = PwSimpleUbbCode::convert($content, 140, new PwUbbCodeConvertThread());
            PwSimpleUbbCode::isSubstr() && $errcode['is_read_all'] = true;
        } elseif (Pw::strlen($content) > 140) {
            $errcode['is_read_all'] = true;
            $content = Pw::substrs($content, 140);
        }
        //var_dump($content);
        //$content = WindSecurity::escapeHTML($content);
        //$content = preg_replace('/(?<!&|&amp;)#([^#]+?)#/ie', "self::_parseTopic('\\1')", $content);
        return $content;
    }

    private function setLikeOrder($infoids, $infos)
    {
        foreach ($infoids as $key => $value) {
            $this->_likeInfo[$key] = $infos[$value];
        }
    }

    /**
     * 喜欢内容扩展
     *
     * @param array $infoids
     */
    private function _getDataFromPost($infoids)
    {
        $_aPid = array(); //有附件的回复
        $datas = $data = array();
        $infos = Wekit::load('forum.PwThread')->fetchPost($infoids);
        foreach ($infos as $info) {
            $data['subject'] = $info['subject'];
            $data['lasttime'] = $info['created_time'];
            $data['content'] = $this->_buildLikeContent($info['content']);
            $data['from'] = '帖子回复';
            $data['uid'] = $info['created_userid'];
            $data['username'] = $info['created_username'];
            $data['fid'] = $info['fid'];
            $data['like_count'] = $info['like_count'];
            $data['url'] = WindUrlHelper::createUrl('bbs/read/run', array(
                'tid' => $info['tid'],
                'fid' => $info['fid'],
                'pid' => $info['pid'],
            ), 'read_'.$info['pid']);
            $data['content'] .= '   <a href="'.$data['url'].'">'.'查看'.'</a>';
            $this->_appendFid($info['fid']);
            if ($info['aids']) {
                $_aPid[] = array($info['tid'], $info['pid']);
            }
            $datas[$info['pid']] = $data;
        }
        $images = $this->_getPostAttachs($_aPid);
        foreach ($images as $img) {
            $datas[$img['pid']]['image'] = Pw::getPath($img['path'], $img['ifthumb']);
        }

        return $datas;
    }

    private function _getDataFromThread($infoids)
    {
        $_aTid = array(); //有附件的帖子
        $datas = $data = array();
        $infos = Wekit::load('forum.PwThread')->fetchThread($infoids, PwThread::FETCH_ALL);
        foreach ($infos as $info) {
            $data['subject'] = $info['subject'];
            $data['lasttime'] = $info['created_time'];
            $data['content'] = $this->_buildLikeContent($info['content']);
            $data['from'] = '帖子';
            $data['uid'] = $info['created_userid'];
            $data['username'] = $info['created_username'];
            $data['fid'] = $info['fid'];
            $data['like_count'] = $info['like_count'];
            $data['url'] = WindUrlHelper::createUrl('bbs/read/run', array('tid' => $info['tid'], 'fid' => $info['fid']));
            $this->_appendFid($info['fid']);
            if ($info['aids']) {
                $_aTid[] = $info['tid'];
            }
            $datas[$info['tid']] = $data;
        }
        $images = $this->_getThreadAttachs($_aTid);
        foreach ($images as $img) {
            $datas[$img['tid']]['image'] = Pw::getPath($img['path'], $img['ifthumb']);
        }

        return $datas;
    }

    private function _getDataFromWeiBo($infoids)
    {
        $datas = $data = array();
        $infos = Wekit::load('weibo.PwWeibo')->getWeibos($infoids);
        foreach ($infos as $info) {
            $data['subject'] = $info['title'];
            $data['lasttime'] = $info['created_time'];
            $data['content'] = $this->_buildLikeContent($info['content']);
            $data['from'] = '微博';
            $data['uid'] = $info['created_userid'];
            $data['username'] = $info['created_username'];
            $data['like_count'] = $info['like_count'];
            $data['url'] = WindUrlHelper::createUrl('space/index/fresh', array('typeid' => 3, 'id' => $info['weibo_id'], 'uid' => $info['created_userid']));
            if (!$data['subject'] && $info['type'] != PwWeibo::TYPE_MEDAL) {
                $data['content'] .= '   <a href="'.$data['url'].'">'.'查看'.'</a>';
            }
            $datas[$info['weibo_id']] = $data;
        }

        return $datas;
    }

    private function _getDataFromApp($infoids)
    {
        $datas = $data = array();
        $infos = Wekit::load('like.PwLikeSource')->fetchSource($infoids);
        foreach ($infos as $info) {
            $data['subject'] = $info['subject'];
            $data['from'] = 'App';
            $data['like_count'] = $info['like_count'];
            $data['url'] = $info['source_url'];
            $datas[$info['sid']] = $data;
        }

        return $datas;
    }

    /**
     * 喜欢回复扩展
     *
     * @param array $infoids
     */
    private function _getReplyFromPost($infoids)
    {
        $datas = $data = array();
        $infos = Wekit::load('forum.PwThread')->fetchPost($infoids);
        foreach ($infos as $info) {
            $data['lasttime'] = $info['created_time'];
            $data['content'] = $this->_buildLikeContent($info['content']);
            $data['uid'] = $info['created_userid'];
            $data['username'] = $info['created_username'];
            $data['url'] = WindUrlHelper::createUrl('bbs/read/run', array('tid' => $info['tid'], 'fid' => $info['fid'], 'pid' => $info['pid']), $info['pid']);
            $this->_replyInfo[$info['pid']] = $data;
        }
    }

    private function _getReplyFromThread($infoids)
    {
        $datas = $data = array();
        $infos = Wekit::load('forum.PwThread')->fetchPost($infoids);
        foreach ($infos as $info) {
            $data['lasttime'] = $info['created_time'];
            $data['content'] = $this->_buildLikeContent($info['content']);
            $data['uid'] = $info['created_userid'];
            $data['username'] = $info['created_username'];
            $data['url'] = WindUrlHelper::createUrl('bbs/read/run', array('tid' => $info['tid'], 'fid' => $info['fid'], 'pid' => $info['pid']), $info['pid']);
            $this->_replyInfo[$info['pid']] = $data;
        }
    }
    //TODO
    private function _getThreadAttachs($tids = array())
    {
        $attachs = array();
        foreach ($tids as $tid) {
            $_attachs = Wekit::load('attach.PwThreadAttach')->getAttachByTid($tid, array(0));
            foreach ($_attachs as $v) {
                if ($v['type'] == 'img') {
                    $attachs[$tid] = $v;
                    break;
                }
            }
        }

        return $attachs;
    }
    //TODO
    private function _getPostAttachs($pids = array())
    {
        $attachs = array();
        foreach ($pids as $v) {
            list($tid, $pid) = $v;
            $_attachs = Wekit::load('attach.PwThreadAttach')->getAttachByTid($tid, $pid);
            foreach ($_attachs as $v) {
                if ($v['type'] == 'img') {
                    $attachs[$pid] = $v;
                    break;
                }
            }
        }

        return $attachs;
    }

    private function _bindForum()
    {
        $_forumsInfo = Wekit::load('forum.PwForum')->fetchForum($this->_fids);
        foreach ($this->_likeInfo as &$likeInfo) {
            $likeInfo['forumname'] = $_forumsInfo[$likeInfo['fid']]['name'];
        }
    }

    private function _getLikeContentDs()
    {
        return Wekit::load('like.PwLikeContent');
    }

    private function _getLikeLogDs()
    {
        return Wekit::load('like.PwLikeLog');
    }

    private function _getLikeStatisticsDs()
    {
        return Wekit::load('like.PwLikeStatistics');
    }

    private function _getLikeTagDs()
    {
        return Wekit::load('like.PwLikeTag');
    }

    private function _getLikeRelationsDs()
    {
        return Wekit::load('like.PwLikeRelations');
    }
}
