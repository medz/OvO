<?php

defined('WEKIT_VERSION') || exit('Forbidden');



/**
 * 单个版块的业务模型
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @license http://www.phpwind.com
 * @version $Id: PwForumBo.php 25814 2013-03-25 05:42:52Z jieyin $
 * @package forum
 */

class PwForumBo
{
    public $fid;
    public $foruminfo = array();
    public $forumset = array();

    public function __construct($fid, $fetchAll = false)
    {
        $this->fid = intval($fid);
        $forumService = $this->_getForumService();
        $this->foruminfo = $forumService->getForum($fid, $fetchAll ? PwForum::FETCH_ALL : (PwForum::FETCH_MAIN | PwForum::FETCH_EXTRA));
        $this->foruminfo['settings_basic'] && $this->forumset = unserialize($this->foruminfo['settings_basic']);
        if (!is_array($this->forumset['allowtype'])) {
            $this->forumset['allowtype'] = array();
        }
    }

    /**
     * 检测是否为正常的版块
     *
     * @return bool
     */
    public function isForum($allowcate = false)
    {
        if (empty($this->foruminfo) || !$allowcate && $this->foruminfo['type'] == 'category') {
            return false;
        }

        return true;
    }

    /**
     * 检测是否为开放版块
     *
     * @return bool
     */
    public function isOpen()
    {
        return !$this->foruminfo['allow_visit'] && !$this->foruminfo['allow_read'] && !$this->foruminfo['password'];
    }

    /**
     * 检测是否为子版块
     *
     * @return bool
     */
    public function isSub()
    {
        return $this->foruminfo['type'] == 'sub' || $this->foruminfo['type'] == 'sub2';
    }

    /**
     * 检测用户是否加入该版块
     *
     * @param  int  $uid 用户id
     * @return bool
     */
    public function isJoin($uid)
    {
        return !!Wekit::load('forum.PwForumUser')->get($uid, $this->fid);
    }

    /**
     * 检测用户是否是该版块的版主
     *
     * @param  string $username 用户名
     * @return bool
     */
    public function isBM($username)
    {
        if (!$username) {
            return false;
        }
        if ($this->foruminfo['manager'] && strpos($this->foruminfo['manager'], ",$username,") !== false) {
            return true;
        }
        if ($this->foruminfo['uppermanager'] && strpos($this->foruminfo['uppermanager'], ",$username,") !== false) {
            return true;
        }

        return false;
    }

    /**
     * 检测用户版块访问权限
     *
     * @param  object $user 用户
     * @return bool
     */
    public function allowVisit(PwUserBo $user)
    {
        if (!$this->foruminfo['allow_visit']) {
            return true;
        }

        return $user->inGroup(explode(',', $this->foruminfo['allow_visit']));
    }

    /**
     * 检测用户版块帖子阅读权限
     *
     * @param  object $user 用户
     * @return bool
     */
    public function allowRead(PwUserBo $user)
    {
        if (!$this->foruminfo['allow_read']) {
            return true;
        }

        return $user->inGroup(explode(',', $this->foruminfo['allow_read']));
    }

    /**
     * 检测用户版块发表主题权限
     *
     * @param  object $user 用户
     * @return bool
     */
    public function allowPost(PwUserBo $user)
    {
        if (!$this->foruminfo['allow_post']) {
            return true;
        }

        return $user->inGroup(explode(',', $this->foruminfo['allow_post']));
    }

    /**
     * 检测用户版块发表回复权限
     *
     * @param  object $user 用户
     * @return bool
     */
    public function allowReply(PwUserBo $user)
    {
        if (!$this->foruminfo['allow_reply']) {
            return true;
        }

        return $user->inGroup(explode(',', $this->foruminfo['allow_reply']));
    }

    /**
     * 检测用户版块上传权限
     *
     * @param  object $user 用户
     * @return bool
     */
    public function allowUpload(PwUserBo $user)
    {
        if (!$this->foruminfo['allow_upload']) {
            return true;
        }

        return $user->inGroup(explode(',', $this->foruminfo['allow_upload']));
    }

    /**
     * 检测用户版块下载权限
     *
     * @param  object $user 用户
     * @return bool
     */
    public function allowDownload(PwUserBo $user)
    {
        if (!$this->foruminfo['allow_download']) {
            return true;
        }

        return $user->inGroup(explode(',', $this->foruminfo['allow_download']));
    }

    /**
     * 获取上级版块链
     *
     * @return array
     */
    public function getForumChain()
    {
        $guide = array();
        if ($this->foruminfo['type'] == 'category') {
            $guide[] = array(strip_tags($this->foruminfo['name']), WindUrlHelper::createUrl('bbs/cate/run', array('fid' => $this->fid)));

            return $guide;
        }
        $guide[] = array(strip_tags($this->foruminfo['name']), WindUrlHelper::createUrl('bbs/thread/run', array('fid' => $this->fid)));
        $info = $this->getParentForums();
        $count = count($info);
        $i = 0;
        foreach ($info as $fid => $value) {
            array_unshift($guide, array($value, WindUrlHelper::createUrl('bbs/'.(++$i < $count ? 'thread' : 'cate').'/run', array('fid' => $fid))));
        }

        return $guide;
    }

    /**
     * 版块导航条信息
     *
     * @return string
     */
    public function headguide()
    {
        $bbsname = Wekit::C('site', 'info.name');
        $headguide = '<a href="'.WindUrlHelper::createUrl('').'" title="'.$bbsname.'" class="home">首页</a>';
        $guide = $this->getForumChain();
        foreach ($guide as $key => $value) {
            $headguide .= $this->bulidGuide($value);
        }

        return $headguide;
    }

    /**
     * 生成导航条节点信息
     *
     * @param  array  $guide 节点信息
     * @return string
     */
    public function bulidGuide($guide)
    {
        if ($guide[1]) {
            return '<em>&gt;</em><a href="'.$guide[1].'">'.WindSecurity::escapeHTML($guide[0]).'</a>';
        }

        return '<em>&gt;</em>'.WindSecurity::escapeHTML($guide[0]);
    }

    /**
     * 获取子版列表
     *
     * @return array
     */
    public function getSubForums($isshow = 0, $withData = false)
    {
        $result = $this->_getForumService()->getSubForums($this->fid);
        if ($isshow) {
            foreach ($result as $key => $value) {
                if (!$value['isshow']) {
                    unset($result[$key]);
                }
            }
        }
        if ($withData) {
            $ids = array_keys($result);
            $datas = $this->_getForumService()->fetchForum($ids, PwForum::FETCH_STATISTICS);
            foreach ($result as $key => $value) {
                $result[$key] = array_merge($value, isset($datas[$key]) ? $datas[$key] : array());
                $_manager = array_unique(explode(',', $value['manager']));
                $result[$key]['manager'] = array();
                foreach ($_manager as $_v) {
                    if ($_v) {
                        $result[$key]['manager'][] = $_v;
                    }
                }
            }
        }

        return $result;
    }

    /**
     * 获取版主列表
     *
     * @return array
     */
    public function getManager()
    {
        if (!$this->foruminfo['manager']) {
            return array();
        }

        return explode(',', trim($this->foruminfo['manager'], ','));
    }

    /**
     * 获取所有上级分类id
     *
     * @return array
     */
    public function getParentFids()
    {
        return $this->foruminfo['fup'] ? explode(',', $this->foruminfo['fup']) : array();
    }

    /**
     * 获取所有上级版块列表
     *
     * @return array
     */
    public function getParentForums()
    {
        if (!$fids = $this->getParentFids()) {
            return array();
        }
        $forums = explode("\t", $this->foruminfo['fupname']);
        $result = array();
        foreach ($fids as $key => $fid) {
            $result[$fid] = $forums[$key];
        }

        return $result;
    }

    /**
     * 获取分类id
     *
     * @return int
     */
    public function getCateId()
    {
        if ($this->foruminfo['type'] == 'category') {
            return $this->fid;
        }
        $array = explode(',', $this->foruminfo['fup']);

        return array_pop($array);
    }

    /**
     * 获取帖子类型
     *
     * @param  PwUserBo $user
     * @return array
     */
    public function getThreadType(PwUserBo $user)
    {
        if (!is_array($this->forumset['typeorder'])) {
            return array();
        }
        asort($this->forumset['typeorder']);
        $array = array();
        $tType = Wekit::load('forum.srv.PwThreadType')->getTtype();
        foreach ($this->forumset['typeorder'] as $key => $value) {
            if (isset($tType[$key]) && in_array($key, $this->forumset['allowtype']) && ($tType[$key][2] === true || $user->getPermission($tType[$key][2]))) {
                $array[$key] = $tType[$key];
            }
        }

        return $array;
    }

    /**
     * 获取本版积分设置
     */
    public function getCreditSet($operate = '')
    {
        $creditset = $this->foruminfo['settings_credit'] ? unserialize($this->foruminfo['settings_credit']) : array();
        if ($operate) {
            return isset($creditset[$operate]) ? $creditset[$operate] : array();
        }

        return $creditset;
    }

    /**
     * 增加一个主题时，更新版块信息
     *
     * @param  int    $tid      帖子id
     * @param  string $username 用户名
     * @param  string $subject  帖子标题
     * @return bool
     */
    public function addThread($tid, $username, $subject)
    {
        return Wekit::load('forum.srv.PwForumService')->updateStatistics($this, 1, 0, 1, array(
            'tid' => $tid, 'username' => $username, 'subject' => $subject,
        ));
    }

    /**
     * 增加一个回复时，更新版块信息
     *
     * @param int    $tid      帖子id
     * @param string $username 用户名
     * @param string $subject  帖子标题
     *                         return bool
     */
    public function addPost($tid, $username, $subject)
    {
        return Wekit::load('forum.srv.PwForumService')->updateStatistics($this, 0, 1, 1, array(
            'tid' => $tid, 'username' => $username, 'subject' => $subject,
        ));
    }

    /**
     * Enter description here ...
     *
     * @return PwForum
     */
    protected function _getForumService()
    {
        return Wekit::load('forum.PwForum');
    }
}
