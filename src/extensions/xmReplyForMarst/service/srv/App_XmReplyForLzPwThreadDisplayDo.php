<?php
defined('WEKIT_VERSION') or exit(403);
Wind::import('SRV:forum.srv.threadDisplay.do.PwThreadDisplayDoBase');

/**
 * 帖子内容展示
 *
 * @author 蝦米 <>
 * @copyright
 * @license
 */
class App_XmReplyForLzPwThreadDisplayDo extends PwThreadDisplayDoBase
{

    /**
     * @var PwThreadBo
     */
    private $thread;

    /*
     * @see PwThreadDisplayDoBase
    */
    public function __construct(PwThreadDisplay $display)
    {
        $this->thread = $display->thread;
    }

    public function bulidRead($read)
    {
        if ($this->thread->info['use_reply_for_lz'] //插件启用
            && $this->_getCurrentUserBo()->uid != $this->thread->authorid //当前用户非楼主
            && $read['created_userid'] != $this->_getCurrentUserBo()->uid //非当前楼层的作者
            && $read['pid'] > 0 //是回帖
        ) {
            $read['content'] = '<font color="gray">抱歉，本回复内容仅楼主可见</font>';
        }
        return $read;
    }

    private function _getCurrentUserBo()
    {
        return Wekit::getLoginUser();
    }
}

?>