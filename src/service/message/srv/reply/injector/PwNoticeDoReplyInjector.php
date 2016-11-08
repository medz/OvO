<?php

Wind::import('SRV:message.srv.reply.do.PwNoticeDoReply');

/**
 * Enter description here ...
 *
 * @author jinlong.panjl <jinlong.panjl@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id$
 * @package wind
 */
class PwNoticeDoReplyInjector extends PwBaseHookInjector
{
    public function run()
    {
        $pid = (int) $this->getInput('pid', 'post');
        $content = $this->getInput('atc_content', 'post');
        $post = Wekit::load('forum.PwThread')->getPost($pid);
        if (!$post['reply_notice']) {
            return false;
        }

        return new PwNoticeDoReply($this->bp, $post, $content);
    }
}
