<?php

defined('WEKIT_VERSION') || exit('Forbidden');


class PwPollDisplay
{
    public $pollData = array();

    private $_instance = array();
    private $_typeMap = array(0 => 'PwPollThread');

    public function __construct(iPwDataSource $dataSource)
    {
        $this->pollData = $dataSource->getData();
        $this->_init();
    }

    protected function _init()
    {
        $this->_buildInstance();

        foreach ($this->_typeMap as $key => $value) {
            if ($this->_instance[$key]) {
                $this->_instance[$key]->init();
            }
        }

        return true;
    }

    private function _buildInstance()
    {
        $_data = array();
        foreach ($this->pollData as $key => $value) {
            $_data[$value['app_type']][] = $value['poll_id'];
        }

        foreach ($_data as $key => $ids) {
            $this->_instance[$key] = new $this->_typeMap[$key]($ids);
        }

        return $this->_instance;
    }

    /**
     * 聚合内容，投票扩展关联
     *
     * @return array
     */
    public function gather()
    {
        $poll = $uids = $userInfos = $pollids = $votedPollids = array();

        foreach ($this->pollData as $value) {
            $uids[] = $value['created_userid'];
            $pollids[] = $value['poll_id'];
        }

        $userInfo = $this->_buildUser($this->_getUserDS()->fetchUserByUid(array_unique($uids)));
        list($option, $votedTotal) = $this->_buildOption($this->_getPollOptionDS()->fetchByPollid($pollids));

        $result = array();
        foreach ($this->pollData as $value) {
            $_pollid = $value['poll_id'];
            $value['ismultiple'] = $value['option_limit'] > 1 ? true : false;
            $value['isexpired'] = ($value['expired_time'] && $value['expired_time'] < Pw::getTime()) ? true : false;
            $value['option'] = isset($option[$_pollid]) ? $option[$_pollid] : array();
            $value['votedtotal'] = isset($votedTotal[$_pollid]) ? $votedTotal[$_pollid] : 0;

            $value += $this->_instance[$value['app_type']]->offer($_pollid);
            $value += isset($userInfo[$value['created_userid']]) ? $userInfo[$value['created_userid']] : array();
            $value['content'] = $this->_buildContent($value['content']);
            $result[] = $value;
        }

        return $result;
    }

    private function _getUserDS()
    {
        return Wekit::load('user.PwUser');
    }

    private function _getPollOptionDS()
    {
        return Wekit::load('poll.PwPollOption');
    }

    public function _buildContent($content)
    {
        $content = strip_tags($content);
        $content = str_replace(array("\r", "\n", "\t"), '', $content);

        return Pw::substrs(Pw::stripWindCode($content), 128);
    }

    private function _buildUser($data)
    {
        if (empty($data) || !count($data)) {
            return array();
        }

        $result = array();
        foreach ($data as $key => $value) {
            $t = array();
            $t['created_uid'] = $value['uid'];
            $t['created_username'] = $value['username'];
            $result[$key] = $t;
        }

        return $result;
    }

    private function _buildOption($data)
    {
        if (empty($data) || !count($data)) {
            return array();
        }

        $option = $votedNum = $total = array();
        foreach ($data as $key => $value) {
            $votedNum[$value['poll_id']][] = $value['voted_num'];
        }

        foreach ($data as $key => $value) {
            $_pollid = $value['poll_id'];
            $total[$_pollid] = isset($votedNum[$_pollid]) ? array_sum($votedNum[$_pollid]) : 0;
            $option[$_pollid][] = $value;
        }

        return array($option, $total);
    }
}

class PwPollThread
{
    public $loginUser = array();

    private $_threadInfo = array();
    private $_forumInfo = array();

    private $_pollids = array();
    private $_tids = array();


    public function __construct($pollids)
    {
        $this->_pollids = $pollids;
        $this->_tids = $this->getRelationids();
        $this->loginUser = Wekit::getLoginUser();
    }

    public function init()
    {
        $this->_threadInfo = $this->_getThreadDs()->fetchThread($this->_tids, 3);
        $this->_forumInfo = $this->buildForumInfo($this->_threadInfo);

        return true;
    }

    public function allowVisit($forum, PwUserBo $user)
    {
        if (!$forum['allow_visit']) {
            return true;
        }

        return $user->inGroup(explode(',', $forum['allow_visit']));
    }

    public function allowRead($forum, PwUserBo $user)
    {
        if (!$forum['allow_read']) {
            return true;
        }

        return $user->inGroup(explode(',', $forum['allow_read']));
    }

    public function buildForumInfo($threadInfo)
    {
        if (!$threadInfo) {
            return array();
        }

        $forumids = array();
        foreach ($threadInfo as $value) {
            if (!$value['fid']) {
                continue;
            }
            $forumids[] = $value['fid'];
        }

        $forumids = array_unique($forumids);
        $forumInfo = $this->_getForumDs()->fetchForum($forumids);

        return $forumInfo;
    }

    public function offer($pollid)
    {
        $tid = $this->_tids[$pollid];
        $thread = isset($this->_threadInfo[$tid]) ? $this->_threadInfo[$tid] : array();

        $result = array();

        $fid = $thread['fid'];
        $forum = isset($this->_forumInfo[$fid]) ? $this->_forumInfo[$fid] : array();
        $result['allow_visit'] = $this->allowVisit($forum, $this->loginUser);
        $result['allow_read'] = $this->allowRead($forum, $this->loginUser);

        $result['typeid'] = $thread['tid'];
        $result['url'] = WindUrlHelper::createUrl('bbs/read/run', array('tid' => $thread['tid'], 'fid' => $thread['fid']));
        $result['title'] = $thread['subject'];
        $result['content'] = $thread['content'] ? $thread['content'] : '';

        return $result;
    }

    public function getRelationids()
    {
        $threadPoll = $this->_getThreadPollDs()->fetchByPollid($this->_pollids);
        if (!$threadPoll) {
            return array();
        }

        foreach ($threadPoll as $value) {
            $tids[$value['poll_id']] = $value['tid'];
        }

        return $tids;
    }

    private function _getThreadPollDS()
    {
        return Wekit::load('poll.PwThreadPoll');
    }

    private function _getThreadDs()
    {
        return Wekit::load('forum.PwThread');
    }

    private function _getForumDs()
    {
        return Wekit::load('forum.PwForum');
    }
}
