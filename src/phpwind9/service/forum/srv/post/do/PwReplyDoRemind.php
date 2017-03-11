<?php

defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 帖子发布 - 话题.
 *
 * @author jinlong.panjl <jinlong.panjl@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id$
 */
class PwReplyDoRemind extends PwPostDoBase
{
    private $loginUser;
    private $_reminds = array();
    private $_atc_title;
    private $_maxNum;

    public function __construct(PwPost $pwpost)
    {
        $this->loginUser = $pwpost->user;
        $this->_maxNum = $this->loginUser->getPermission('remind_max_num');
    }

    public function addPost($pid, $tid)
    {
        $this->_addRemind($pid, $tid);
    }

    public function updatePost($pid, $tid)
    {
        $this->_addRemind($pid, $tid);
    }

    public function dataProcessing($postDm)
    {
        if ($this->_check() !== true) {
            return $postDm;
        }
        $atc_content = $postDm->getField('content');
        $atc_content = preg_replace('/\[quote(=.+?\,\d+)?\].*?\[\/quote\]/is', '', $atc_content);
        $this->_atc_title = Pw::substrs(trim(Pw::stripWindCode($atc_content, true)), 20);

        $reminds = $this->_getRemindService()->bulidRemind($atc_content);
        $this->_reminds = $this->_getRemindService()->buildUsers($this->loginUser->uid, $reminds, $this->_maxNum);
        $reminds = $this->_getRemindService()->formatReminds($this->_reminds);
        $postDm->setReminds($reminds);

        return $postDm;
    }

    private function _addRemind($pid, $tid)
    {
        if ($this->_check() !== true) {
            return false;
        }
        if (!$this->_reminds) {
            return false;
        }
        $reminds = ($this->_maxNum && count($this->_reminds) > $this->_maxNum) ? array_slice($this->_reminds, 0, $this->_maxNum) : $this->_reminds;
        $remindUids = array_keys($reminds);
        $this->_getRemindService()->addRemind($this->loginUser->uid, $remindUids);

        //发送通知
        $extendParams = array(
            'remindUid'      => $this->loginUser->uid,
            'title'          => $this->_atc_title,
            'remindUsername' => $this->loginUser->username,
            'notice'         => '在回帖 <a href="'.WindUrlHelper::createUrl('bbs/read/run', array('tid' => $tid), $pid).'" target="_blank">'.$this->_atc_title.'</a> @了您',
        );
        // 是否黑名单
        $remindUids = $this->_checkBlack($remindUids);
        foreach ($remindUids as $uid) {
            $this->_getPwNoticeService()->sendNotice($uid, 'remind', $pid, $extendParams);
        }
    }

    private function _check()
    {
        if ($this->loginUser->getPermission('remind_open') < 1) {
            return new PwError('bbs:remind.remind_open.error');
        }

        return true;
    }

    /**
     * 是否开启权限.
     *
     * @param array $remindUids
     *
     * @return bool
     */
    private function _checkBlack($remindUids)
    {
        $result = Wekit::load('user.PwUserBlack')->checkUserBlack($this->loginUser->uid, $remindUids);
        if ($result) {
            $remindUids = array_diff($remindUids, $result);
        }

        return $remindUids;
    }

    /**
     * @return PwNoticeService
     */
    protected function _getPwNoticeService()
    {
        return Wekit::load('message.srv.PwNoticeService');
    }

    /**
     * PwRemindService.
     *
     * @return PwRemindService
     */
    private function _getRemindService()
    {
        return Wekit::load('remind.srv.PwRemindService');
    }
}
