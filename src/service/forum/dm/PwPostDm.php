<?php

defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 帖子数据模型(insert, update).
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwPostDm.php 21175 2012-11-29 12:25:29Z jieyin $
 */
abstract class PwPostDm extends PwBaseDm
{
    public $forum;
    public $user;
    protected $hide = 0;

    public function __construct(PwForumBo $forum = null, PwUserBo $user = null)
    {
        $this->forum = $forum;
        $this->user = $user;
    }

    /**
     * 设置帖子标题.
     *
     * @param string $title 帖子标题
     */
    public function setTitle($title)
    {
        $this->_data['subject'] = trim($title);

        return $this;
    }

    /**
     * 设置帖子内容.
     *
     * @param string $content 帖子内容
     */
    public function setContent($content)
    {
        $this->_data['content'] = rtrim($content);

        return $this;
    }

    /**
     * 设置帖子所属版块.
     *
     * @param int $fid 版块id
     */
    public function setFid($fid)
    {
        $this->_data['fid'] = intval($fid);

        return $this;
    }

    /**
     * 设置帖子作者信息.
     *
     * @param int    $uid      用户id
     * @param string $username 用户名
     * @param string $ip       用户ip
     */
    public function setAuthor($uid, $username, $ip)
    {
        $this->_data['created_userid'] = $uid;
        $this->_data['created_username'] = $username;
        $this->_data['created_ip'] = $ip;
        $this->_data['ipfrom'] = Wekit::load('LIB:utility.PwIptable')->getIpFrom($ip);

        return $this;
    }

    /**
     * 设置帖子编辑人信息.
     *
     * @param int    $uid      编辑人id
     * @param string $username 编辑人用户名
     * @param string $ip       编辑人ip
     * @param int    $time     时间戳
     */
    public function setModifyInfo($uid, $username, $ip, $time)
    {
        $this->_data['modified_userid'] = $uid;
        $this->_data['modified_username'] = $username;
        $this->_data['modified_ip'] = $ip;
        $this->_data['modified_time'] = $time;

        return $this;
    }

    /**
     * 设置帖子创建时间.
     *
     * @param int $time 时间戳
     */
    public function setCreatedTime($time)
    {
        $this->_data['created_time'] = $time;

        return $this;
    }

    /**
     * 设置帖子是否可用.
     *
     * @param int $disabled <0.可用 1.不可用/未审核 2.不可用/被删除>
     *
     * @return PwPostDm
     */
    public function setDisabled($disabled)
    {
        $this->_data['disabled'] = $disabled;
        if ($disabled == 0) {
            $this->_setIscheck(1);
        } elseif ($disabled == 1) {
            $this->_setIscheck(0);
        }

        return $this;
    }

    protected function _setIscheck($ischeck)
    {
        $this->_data['ischeck'] = $ischeck;

        return $this;
    }

    /**
     * 设置帖子附件数量.
     *
     * @param int $num 数量
     */
    public function setAids($num)
    {
        $this->_data['aids'] = $num;

        return $this;
    }

    /**
     * 设置帖子附件包含信息.
     *
     * @param int $ifupload 位运算存储值(1.是否包含图片 2.是否包含txt 3.是否包含zip)
     */
    public function setIfupload($ifupload)
    {
        $this->_data['ifupload'] = $ifupload;

        return $this;
    }

    public function setHasImage($bool)
    {
        $this->_bitData['ifupload'][1] = (bool) $bool;

        return $this;
    }

    public function setHasTxt($bool)
    {
        $this->_bitData['ifupload'][2] = (bool) $bool;

        return $this;
    }

    public function setHasZip($bool)
    {
        $this->_bitData['ifupload'][3] = (bool) $bool;

        return $this;
    }

    public function setHasAttach($type, $bool)
    {
        if ($type == 'img') {
            return $this->setHasImage($bool);
        }
        if ($type == 'txt') {
            return $this->setHasTxt($bool);
        }

        return $this->setHasZip($bool);
    }

    public function setReplyNotice($reply_notice)
    {
        $this->_data['reply_notice'] = intval($reply_notice);

        return $this;
    }

    public function setLikeCount($count)
    {
        $this->_data['like_count'] = intval($count);

        return $this;
    }

    public function setSellCount($count)
    {
        $this->_data['sell_count'] = intval($count);

        return $this;
    }

    public function setReminds($reminds)
    {
        $this->_data['reminds'] = $reminds;

        return $this;
    }

    public function setWordVersion($word_version)
    {
        $this->_data['word_version'] = intval($word_version);

        return $this;
    }

    public function setTags($tags)
    {
        $this->_data['tags'] = $tags;

        return $this;
    }

    public function setManageRemind($manage_remind)
    {
        $this->_data['manage_remind'] = $manage_remind;

        return $this;
    }

    public function addReplies($num)
    {
        $this->_increaseData['replies'] = intval($num);

        return $this;
    }

    public function addSellCount($count)
    {
        $this->_increaseData['sell_count'] = intval($count);

        return $this;
    }

    /**
     * 帖子是否回复可见
     */
    public function setHide($hide)
    {
        $this->hide = $hide;

        return $this;
    }

    public function seVerifiedWord($verifiedWord)
    {
        $this->_data['verifiedWord'] = (int) $verifiedWord;

        return $this;
    }

    public function getTitle()
    {
        return $this->_data['subject'];
    }

    public function getContent()
    {
        return $this->_data['content'];
    }

    public function getTopictype()
    {
        return $this->_data['topic_type'];
    }

    public function getIscheck()
    {
        return $this->_data['ischeck'];
    }

    protected function _setUseubb($isuse)
    {
        $this->_data['useubb'] = $isuse;

        return $this;
    }

    public function checkData()
    {
        if (empty($this->_data) && empty($this->_increaseData) && empty($this->_bitData)) {
            return new PwError('BBS:post.postdata.empty');
        }

        return true;
    }

    public function checkTitle()
    {
        $maxlen = Wekit::C('bbs', 'title.length.max');
        if ($maxlen > 0 && Pw::strlen($this->_data['subject']) > $maxlen) {
            return new PwError('BBS:post.subject.length.limit', array('{len}' => $maxlen));
        }

        return true;
    }

    public function checkContent()
    {
        if ($this->_data['content'] === '') {
            return new PwError('BBS:post.content.empty');
        }
        $len = Pw::strlen($this->_data['content']);
        $config = Wekit::C('bbs');
        if ($this->forum && $this->forum->forumset['minlengthofcontent']) {
            $config['content.length.min'] = $this->forum->forumset['minlengthofcontent'];
        }
        if ($len < $config['content.length.min']) {
            return new PwError('BBS:post.content.length.less', array('{min}' => $config['content.length.min']));
        }
        if ($len > $config['content.length.max']) {
            return new PwError('BBS:post.content.length.more', array('{max}' => $config['content.length.max']));
        }
        if ($this->forum && $this->user) {
            if ((!$this->forum->forumset['allowhide'] || !$this->user->getPermission('allow_thread_extend.hide')) && (PwUbbCode::hasTag($this->_data['content'], 'post') || PwUbbCode::hasTag($this->_data['content'], 'hide'))) {
                return new PwError('BBS:post.content.hide');
            }
            if ((!$this->forum->forumset['allowsell'] || !$this->user->getPermission('allow_thread_extend.sell')) && PwUbbCode::hasTag($this->_data['content'], 'sell')) {
                return new PwError('BBS:post.content.sell');
            }
        }

        return true;
    }

    protected function _dateFormat()
    {
        if (isset($this->_data['content'])) {
            $this->_data['content'] = PwUbbCode::autoUrl($this->_data['content'], true);
            $useubb = 0;
            if ($this->hide) {
                $this->_data['content'] = '[post]'.str_replace(array('[post]', '[/post]'), '', $this->_data['content']).'[/post]';
                $useubb = 1;
            }
            if ($this->user && preg_match('/\[sell=(\d+)(\,(\d+))?\].+?\[\/sell\]/is', $this->_data['content'], $matchs)) {
                $this->_data['content'] = $this->_formatSell($this->_data['content'], $matchs);
                $useubb = 1;
            }
            if ($this->_data['reminds']) {
                $useubb = 1;
            }
            if (!$useubb) {
                $useubb = ($this->_data['content'] == PwUbbCode::convert($this->_data['content'], new PwUbbCodeConvertThread())) ? 0 : 1;
            }
            $this->_setUseubb($useubb);
        }
    }

    protected function _formatSell($content, $matchs)
    {
        $cost = max($matchs[1], 1);
        $type = $matchs[3];
        $flag = 0;
        if (($max = $this->user->getPermission('sell_credit_range.maxprice')) > 0 && $cost > $max) {
            $cost = $max;
            $flag = 1;
        }
        if ($credits = $this->user->getPermission('sell_credits', false, array())) {
            if (!in_array($type, $credits)) {
                $type = current($credits);
                $flag = 1;
            }
        } else {
            Wind::import('SRV:credit.bo.PwCreditBo');
            $type = key(PwCreditBo::getInstance()->cType);
            $flag = 1;
        }
        if ($flag) {
            $content = str_replace('[sell='.$matchs[1].$matchs[2], '[sell='.$cost.','.$type, $content);
        }

        return $content;
    }

    protected function _beforeAdd()
    {
        ($result = $this->checkData()) === true
            && ($result = $this->checkTitle()) === true
            && ($result = $this->checkContent()) === true;
        $this->_dateFormat();

        return $result;
    }

    protected function _beforeUpdate()
    {
        ($result = $this->checkData()) === true
            && (!isset($this->_data['subject']) || ($result = $this->checkTitle()) === true)
            && (!isset($this->_data['content']) || ($result = $this->checkContent()) === true);
        $this->_dateFormat();

        return $result;
    }
}
