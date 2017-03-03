<?php

 

/**
 * Enter description here ...
 *
 * @author jinlong.panjl <jinlong.panjl@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id$
 */
class PwReplyDoNotice extends PwPostDoBase
{
    public $user;
    private $_content;
    private $_rpid;
    private $info;

    public function __construct(PwPost $pwpost)
    {
        $this->user = $pwpost->user;
        $this->info = $pwpost->action->getInfo();
    }

    public function dataProcessing($postDm)
    {
        $atc_content = $postDm->getField('content');
        $this->_rpid = $postDm->getField('rpid');
        $atc_content = preg_replace('/\[quote(=.+?\,\d+)?\].*?\[\/quote\]/is', '', $atc_content);
        $this->_content = Pw::substrs(Pw::stripWindCode($atc_content, true), 30);

        return $postDm;
    }

    public function addPost($pid, $tid)
    {
        if ($this->_rpid) {
            $this->_sendReplyNotice($pid, $tid);
            $this->_sendThreadNotice($tid, $pid);
        } else {
            $this->_sendThreadNotice($tid, $pid);
        }

        return true;
    }

    private function _sendThreadNotice($tid, $pid)
    {
        $info = Wekit::load('thread.PwThread')->getThread($tid, PwThread::FETCH_MAIN);
        $param = $info['tid'];
        $params = array();
        $params['replyUserid'] = $this->user->uid;
        $params['replyUsername'] = $this->user->username;
        $params['threadTitle'] = Pw::substrs(Pw::stripWindCode($info['subject']), 30);
        $params['threadId'] = $info['tid'];
        $params['pid'] = $pid;
        $params['threadUserid'] = $info['created_userid'];
        $type = 'threadreply';
        if (!$info['reply_notice'] || $this->user->uid == $info['created_userid']) {
            return false;
        }
        $blackUid = Wekit::load('user.PwUserBlack')->checkUserBlack($this->user->uid, $info['created_userid']);
        if ($blackUid) {
            return false;
        }

        return $this->_getNoticeService()->sendNotice($info['created_userid'], $type, $param, $params);
    }

    private function _sendReplyNotice($pid, $tid)
    {
        $info = Wekit::load('thread.PwThread')->getPost($this->_rpid);
        $param = $info['pid'];
        $params = array();
        $params['replyUserid'] = $this->user->uid;
        $params['replyUsername'] = $this->user->username;
        $info['content'] = preg_replace('/\[quote(=.+?\,\d+)?\].*?\[\/quote\]/is', '', $info['content']);
        $params['postTitle'] = Pw::stripWindCode($info['content']);
        $params['postTitle'] = $params['postTitle'] ? $params['postTitle'] : 'Re:'.$this->info['subject'];
        $params['postTitle'] = Pw::substrs($params['postTitle'], 30);
        $params['threadId'] = $info['tid'];
        $params['pid'] = $pid;
        $params['postUserid'] = $info['created_userid'];
        $type = 'postreply';
        if (!$info['reply_notice'] || $this->user->uid == $info['created_userid']) {
            return false;
        }
        $blackUid = Wekit::load('user.PwUserBlack')->checkUserBlack($this->user->uid, $info['created_userid']);
        if ($blackUid) {
            return false;
        }

        return $this->_getNoticeService()->sendNotice($info['created_userid'], $type, $param, $params);
    }

    /**
     * Enter description here ...
     *
     * @return PwNoticeService
     */
    protected function _getNoticeService()
    {
        return Wekit::load('message.srv.PwNoticeService');
    }
}
