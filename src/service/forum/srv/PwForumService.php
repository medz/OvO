<?php

defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 版块公共服务
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @license http://www.phpwind.com
 *
 * @version $Id: PwForumService.php 24758 2013-02-20 06:55:42Z jieyin $
 */
class PwForumService
{
    protected static $_forums = null;
    protected static $_map = null;

    /**
     * 获取版块列表.
     *
     * @return array
     */
    public function getForumList()
    {
        isset(self::$_forums) || self::$_forums = $this->_getForum()->getForumList();

        return self::$_forums;
    }

    /**
     * 获取用户所有可以访问的版块列表.
     *
     * @param PwUserBo $user
     *
     * @return array
     */
    public function getAllowVisitForum(PwUserBo $user, $forums = null)
    {
        $forums === null && $forums = $this->getForumList();
        $fids = array();
        foreach ($forums as $key => $value) {
            if (!$value['allow_visit'] || $user->inGroup(explode(',', $value['allow_visit']))) {
                $fids[] = $value['fid'];
            }
        }

        return $fids;
    }

    /**
     * 获取用户所有禁止访问的版块列表.
     *
     * @param PwUserBo $user
     * @param array    $forums      版块列表
     * @param bool     $includeHide 是否包含隐藏版块
     *
     * @return array
     */
    public function getForbidVisitForum(PwUserBo $user, $forums = null, $includeHide = false)
    {
        $forums === null && $forums = $this->getForumList();
        $fids = array();
        foreach ($forums as $key => $value) {
            if ($value['allow_visit'] && !$user->inGroup(explode(',', $value['allow_visit']))) {
                $fids[] = $value['fid'];
            } elseif ($includeHide && $value['isshow'] == 0) {
                $fids[] = $value['fid'];
            }
        }

        return $fids;
    }

    /**
     * 获取指定版块的信息（使用全部版块缓存，如果没有这个前提，直接使用 forum.PwForum->fetchForum 接口比较合算）.
     *
     * @param array $fids;
     *
     * @return array
     */
    public function fetchForum($fids)
    {
        return Pw::subArray($this->getForumList(), $fids);
    }

    /**
     * 获取版块层级列表.
     *
     * @return array
     */
    public function getForumMap()
    {
        if (!isset(self::$_map)) {
            $forums = $this->getForumList();
            foreach ($forums as $key => $value) {
                self::$_map[$value['parentid']][] = $value;
            }
        }

        return self::$_map;
    }

    /**
     * 获取分类和版块列表（子版除外）.
     *
     * @return array
     */
    public function getCommonForumList($fetchmode = PwForum::FETCH_MAIN)
    {
        $forumdb = array(0 => array());
        $forumList = $this->_getForum()->getCommonForumList($fetchmode);
        foreach ($forumList as $forums) {
            if (!$forums['isshow']) {
                continue;
            }
            if ($forums['type'] === 'forum') {
                $forumdb[$forums['parentid']][$forums['fid']] = $forums;
            } elseif ($forums['type'] === 'category') {
                $forumdb[0][$forums['fid']] = $forums;
            }
        }

        return $forumdb;
    }

    /**
     * 根据层级列表，递归获取链级列表.
     *
     * @param int   $parentid 获取该版的所属
     * @param array $map      版块层级列表
     *
     * @return array
     */
    public function getForumsByLevel($parentid, $map)
    {
        if (!isset($map[$parentid])) {
            return array();
        }
        $length = count($map[$parentid]);
        $array = array();
        foreach ($map[$parentid] as $key => $value) {
            if ($key == $length - 1) {
                $value['isEnd'] = 1;
            }
            $array[] = $value;
            $array = array_merge($array, $this->getForumsByLevel($value['fid'], $map));
        }

        return $array;
    }

    /**
     * 根据层级列表，递归获取链级列表.
     *
     * @param int   $parentid 获取该版的所属
     * @param array $map      版块层级列表
     *
     * @return array
     */
    public function findOptionInMap($parentid, $map, $lang = array())
    {
        if (!isset($map[$parentid])) {
            return array();
        }
        $result = array();
        foreach ($map[$parentid] as $key => $value) {
            $result[$value['fid']] = $lang[$value['type']].$value['name'];
            $result += $this->findOptionInMap($value['fid'], $map, $lang);
        }

        return $result;
    }

    /**
     * 获取版块的select/option.
     *
     * @param mixed $selected 选中的fid序列
     *
     * @return string option的html
     */
    public function getForumOption($selected = array())
    {
        is_array($selected) || $selected = array($selected);
        $map = $this->getForumMap();
        $option_html = '';
        $option_arr = $this->findOptionInMap(0, $map, array(
            'category' => '&gt;&gt; ',
            'forum'    => ' &nbsp;|- ',
            'sub'      => ' &nbsp; &nbsp;|-  ',
            'sub2'     => '&nbsp;&nbsp; &nbsp; &nbsp;|-  ',
        ));
        foreach ($option_arr as $key => $value) {
            $option_html .= '<option value="'.$key.'"'.(in_array($key, $selected) ? ' selected' : '').'>'.strip_tags($value).'</option>';
        }

        return $option_html;
    }

    /**
     * 获取上级版块id序列.
     *
     * @param int $fid 版块id
     *
     * @return array
     */
    public function getParentFids($fid)
    {
        $forums = $this->getForumList();
        $upfids = array();
        $fid = $forums[$fid]['parentid'];
        while (in_array($forums[$fid]['type'], array('sub2', 'sub', 'forum'))) {
            $upfids[] = $fid;
            $fid = $forums[$fid]['parentid'];
        }

        return $upfids;
    }

    /**
     * 获取分类id.
     *
     * @param int $fid
     *
     * @return int
     */
    public function getCateId($fid)
    {
        $forum = $this->_getForum()->getForum($fid);
        if ($forum['type'] == 'category') {
            return $fid;
        }
        $array = explode(',', $forum['fup']);

        return array_pop($array);
    }

    /**
     * 获取用户加入的版块列表.
     *
     * @param int $uid
     *
     * @return array
     */
    public function getJoinForum($uid)
    {
        if ($result = Wekit::load('forum.PwForumUser')->getFroumByUid($uid)) {
            $array = array();
            $tmp = Wekit::load('forum.PwForum')->fetchForum(array_keys($result));
            foreach ($tmp as $key => $value) {
                $array[$value['fid']] = $value['name'];
            }

            return $array;
        }

        return array();
    }

    /**
     * 重新统计本版及上级版块的帖子统计数.
     *
     * @param mixed $forum (int fid | object PwForumBo)
     */
    public function updateForumStatistics($forum)
    {
        if (!$forum instanceof PwForumBo) {
            $forum = new PwForumBo($forum);
        }
        if (!$forum->isForum()) {
            return false;
        }
        $service = $this->_getForum();
        $service->updateForumStatistics($forum->fid);
        if ($fids = $forum->getParentFids()) {
            foreach ($fids as $fid) {
                $service->updateForumStatistics($fid);
            }
        }
    }

    /**
     * 更新版块帖子统计数.
     *
     * @param mixed $forum    int 版块fid | object PwForumBo
     * @param int   $topic    主题更新数
     * @param int   $replies  回复更新数
     * @param int   $tpost    今日发帖更新数
     * @param int   $lastinfo
     */
    public function updateStatistics($forum, $topic, $replies, $tpost = 0, $lastinfo = array())
    {
        if (!$forum instanceof PwForumBo) {
            $forum = new PwForumBo($forum);
        }
        if (!$forum->isForum()) {
            return false;
        }
        $article = $topic + $replies;
        $dm = new PwForumDm($forum->fid);
        $dm->addThreads($topic)->addPosts($replies)->addArticle($article)->addTodayPosts($tpost);
        if ($lastinfo) {
            !isset($lastinfo['time']) && $lastinfo['time'] = Pw::getTime();
            $dm->setLastpostInfo($lastinfo['tid'], Pw::substrs($lastinfo['subject'], 26, 0, true), $lastinfo['username'], $lastinfo['time']);
        }
        $service = $this->_getForum();
        $service->updateForum($dm, PwForum::FETCH_STATISTICS);

        if ($fids = $forum->getParentFids()) {
            $dm = new PwForumDm(true);
            $dm->addArticle($article)->addSubThreads($topic)->addTodayPosts($tpost);
            if ($lastinfo && $forum->isOpen()) {
                $dm->setLastpostInfo($lastinfo['tid'], Pw::substrs($lastinfo['subject'], 26, 0, true), $lastinfo['username'], $lastinfo['time']);
            }
            $service->batchUpdateForum($fids, $dm, PwForum::FETCH_STATISTICS);
        }

        return true;
    }

    protected function _getForum()
    {
        return Wekit::load('forum.PwForum');
    }
}
