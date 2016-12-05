<?php

Wind::import('SRV:forum.srv.post.do.PwPostDoBase');

/**
 * Enter description here ...
 *
 * @author jinlong.panjl <jinlong.panjl@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id$
 */
class PwNoticeDoReply extends PwPostDoBase
{
    private $user;
    private $postDm;
    private $postInfo;
    private $content;

    public function __construct(PwPost $pwpost, $postInfo, $content)
    {
        $this->user = $pwpost->user;
        $this->postDm = $pwpost->getDm();
        $this->postInfo = $postInfo;
        $this->content = $content;
    }

    public function addPost($pid, $tid)
    {
        $params = array();
        $params['replyContent'] = $this->content;
        $params['replyUserid'] = $this->user->uid;
        $params['replyUsername'] = $this->user->username;
        $params['postTitle'] = Pw::substrs($this->postInfo['content'], 30);
        $params['postId'] = $this->postInfo['pid'];
        $params['postUserid'] = $this->postInfo['created_userid'];
        $params['replies'] = $this->postInfo['replies'];

        return $this->_getNoticeService()->sendNotice($this->postInfo['created_userid'], 'postreply', $this->postInfo['pid'], $params);
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
