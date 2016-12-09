<?php

defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 版块数据模型.
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @license http://www.phpwind.com
 *
 * @version $Id: PwForumDm.php 18802 2012-09-27 10:17:30Z jieyin $
 */
class PwForumDm extends PwBaseDm
{
    public $fid;

    public function __construct($fid = 0)
    {
        $this->fid = $fid;
    }

    public function setName($name)
    {
        $this->_data['name'] = $name;

        return $this;
    }

    public function setParentid($parentid = 0)
    {
        $this->_data['parentid'] = intval($parentid);

        return $this;
    }

    public function setDescrip($descrip)
    {
        $this->_data['descrip'] = $descrip;

        return $this;
    }

    public function setVieworder($vieworder)
    {
        $this->_data['vieworder'] = intval($vieworder);

        return $this;
    }

    public function setManager($manager)
    {
        $array = array();
        is_array($manager) || $manager = explode(',', $manager);
        foreach ($manager as $key => $value) {
            if ($value) {
                $array[] = $value;
            }
        }
        $this->_data['manager'] = $array;

        return $this;
    }

    public function setUpperManager($manager)
    {
        $array = array();
        is_array($manager) || $manager = explode(',', $manager);
        foreach ($manager as $key => $value) {
            if ($value) {
                $array[] = $value;
            }
        }
        $this->_data['uppermanager'] = $array;

        return $this;
    }

    public function setIcon($icon)
    {
        $this->_data['icon'] = $icon;

        return $this;
    }

    public function setlogo($logo)
    {
        $this->_data['logo'] = $logo;

        return $this;
    }

    /**
     * 该方法在 PwForumMiscService.correctData() 中调用,其他情况下不能随意使用.
     */
    public function setFup($fup)
    {
        $this->_data['fup'] = $fup;

        return $this;
    }

    public function setFupname($fupname)
    {
        $this->_data['fupname'] = $fupname;

        return $this;
    }

    public function setIsshow($var)
    {
        $this->_data['isshow'] = intval($var);

        return $this;
    }

    public function setAcross($across)
    {
        $this->_data['across'] = intval($across);

        return $this;
    }

    public function setIsshowsub($var)
    {
        $this->_data['isshowsub'] = intval($var);

        return $this;
    }

    public function setHassub($var)
    {
        $this->_data['hassub'] = intval($var);

        return $this;
    }

    public function setNewtime($minute)
    {
        $this->_data['newtime'] = intval($minute);

        return $this;
    }

    public function setPassword($password)
    {
        $this->_data['password'] = $password ? md5($password) : '';

        return $this;
    }

    //设置加密过的密码 版块复制用
    public function setEncryptPassword($password)
    {
        $this->_data['password'] = $password ? $password : '';

        return $this;
    }

    public function setAllowVisit($groups)
    {
        $this->_data['allow_visit'] = $groups;

        return $this;
    }

    public function setAllowRead($groups)
    {
        $this->_data['allow_read'] = $groups;

        return $this;
    }

    public function setAllowPost($groups)
    {
        $this->_data['allow_post'] = $groups;

        return $this;
    }

    public function setAllowReply($groups)
    {
        $this->_data['allow_reply'] = $groups;

        return $this;
    }

    public function setAllowUpload($groups)
    {
        $this->_data['allow_upload'] = $groups;

        return $this;
    }

    public function setAllowDownload($groups)
    {
        $this->_data['allow_download'] = $groups;

        return $this;
    }

    public function setCreatedUser($uid, $username)
    {
        $this->_data['created_userid'] = $uid;
        $this->_data['created_username'] = $username;

        return $this;
    }

    public function setLastpostInfo($tid, $info, $username, $time)
    {
        $this->_data['lastpost_tid'] = $tid;
        $this->_data['lastpost_info'] = $info;
        $this->_data['lastpost_username'] = $username;
        $this->_data['lastpost_time'] = $time;

        return $this;
    }

    public function setSeoTitle($seo_title)
    {
        $this->_data['seo_title'] = $seo_title;

        return $this;
    }

    public function setSeoKeywords($seo_keywords)
    {
        $this->_data['seo_keywords'] = $seo_keywords;

        return $this;
    }

    public function setSeoDescription($seo_description)
    {
        $this->_data['seo_description'] = $seo_description;

        return $this;
    }

    public function setBasicSetting($settings_basic)
    {
        $this->_data['settings_basic'] = $settings_basic;

        return $this;
    }

    public function setCreditSetting($settings_credit)
    {
        $this->_data['settings_credit'] = $settings_credit;

        return $this;
    }

    public function setArticle($num)
    {
        $this->_data['article'] = intval($num);

        return $this;
    }

    public function addArticle($num)
    {
        $this->_increaseData['article'] = intval($num);

        return $this;
    }

    public function setThreads($num)
    {
        $this->_data['threads'] = intval($num);

        return $this;
    }

    public function addThreads($num)
    {
        $this->_increaseData['threads'] = intval($num);

        return $this;
    }

    public function setSubThreads($num)
    {
        $this->_data['subthreads'] = intval($num);

        return $this;
    }

    public function addSubThreads($num)
    {
        $this->_increaseData['subthreads'] = intval($num);

        return $this;
    }

    public function setPosts($num)
    {
        $this->_data['posts'] = intval($num);

        return $this;
    }

    public function addPosts($num)
    {
        $this->_increaseData['posts'] = intval($num);

        return $this;
    }

    public function addTodayThreads($num)
    {
        $this->_increaseData['todaythreads'] = intval($num);

        return $this;
    }

    public function setTodayPosts($num)
    {
        $this->_data['todayposts'] = intval($num);

        return $this;
    }

    public function addTodayPosts($num)
    {
        $this->_increaseData['todayposts'] = intval($num);

        return $this;
    }

    public function setStyle($style)
    {
        $this->_data['style'] = $style;

        return $this;
    }

    protected function _beforeAdd()
    {
        if (empty($this->_data['name'])) {
            return new PwError('BBS:forum.forumname.empty');
        }
        if (($result = $this->_checkParentid()) !== true) {
            return $result;
        }
        if (isset($this->_data['manager']) && ($result = $this->_checkManager()) !== true) {
            return $result;
        }
        $this->_formatData();

        return true;
    }

    protected function _beforeUpdate()
    {
        if (empty($this->fid)) {
            return new PwError('BBS:forum.fid.empty');
        }
        if (isset($this->_data['name']) && empty($this->_data['name'])) {
            return new PwError('BBS:forum.forumname.empty');
        }
        if (isset($this->_data['parentid']) && ($result = $this->_checkParentid()) !== true) {
            return $result;
        }
        if (isset($this->_data['manager']) && ($result = $this->_checkManager()) !== true) {
            return $result;
        }
        $this->_formatData();

        return true;
    }

    protected function _checkParentid()
    {
        $parentid = isset($this->_data['parentid']) ? $this->_data['parentid'] : 0;
        if ($this->fid && $parentid == $this->fid) {
            return new PwError('BBS:forum.parentid.same');
        }
        if ($parentid) {
            $forum = $this->_getForumService()->getForum($parentid);
            if (!$forum) {
                return new PwError('BBS:forum.parentid.exists.not');
            }
            if ($forum['type'] == 'sub2') {
                return new PwError('BBS:forum.parentid.issub2');
            }
            $this->_data['type'] = $this->_lowerType($forum['type']);
        } else {
            $this->_data['type'] = 'category';
        }
        $this->_data['issub'] = in_array($this->_data['type'], array('category', 'forum')) ? 0 : 1;

        return true;
    }

    protected function _checkManager()
    {
        if (!$this->_data['manager']) {
            return true;
        }
        $users = Wekit::load('user.PwUser')->fetchUserByName($this->_data['manager']);
        if (count($this->_data['manager']) != count($users)) {
            $array = array();
            foreach ($users as $key => $value) {
                $array[] = $value['username'];
            }
            if ($diff = array_diff($this->_data['manager'], $array)) {
                return new PwError('USER:exists.not', array('{username}' => implode('、', $diff)));
            }
        }

        return true;
    }

    protected function _formatData()
    {
        foreach (array('settings_basic', 'settings_credit') as $key => $value) {
            isset($this->_data[$value]) && $this->_data[$value] = serialize($this->_data[$value]);
        }
        foreach (array('manager', 'uppermanager') as $key => $value) {
            isset($this->_data[$value]) && $this->_data[$value] = $this->_data[$value] ? ','.implode(',', $this->_data[$value]).',' : '';
        }
        foreach (array('allow_visit', 'allow_read', 'allow_post', 'allow_reply', 'allow_upload', 'allow_download') as $key => $value) {
            isset($this->_data[$value]) && $this->_data[$value] = $this->_data[$value] ? implode(',', $this->_data[$value]) : '';
        }
    }

    protected function _lowerType($type)
    {
        $array = array('category', 'forum', 'sub', 'sub2');
        $index = array_search($type, $array);

        return $array[++$index];
    }

    protected function _getForumService()
    {
        return Wekit::load('forum.PwForum');
    }
}
