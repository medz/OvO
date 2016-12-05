<?php

defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('HOOK:PwRevertTopic.PwRevertTopicDoMain');
//Wind::import('SRV:forum.srv.operation.do.PwDeleteTopicDoDirectDelete');
//Wind::import('SRV:forum.srv.operation.do.PwDeleteArticleDoAttachDelete');
//Wind::import('SRV:forum.srv.operation.do.PwDeleteArticleDoForumUpdate');
//Wind::import('SRV:forum.srv.operation.do.PwDeleteTopicDoFreshDelete');

/**
 * 回复回收站帖子及其关联操作(扩展).
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwRevertTopic.php 13278 2012-07-05 02:08:39Z jieyin $
 */
class PwRevertTopic extends PwGleanDoProcess
{
    public $data = array();
    public $tids = array();
    public $user;

    public function __construct($tids, PwUserBo $user)
    {
        $this->data = $this->_initData($tids);
        $this->user = $user;
        parent::__construct();
    }

    protected function _initData($tids)
    {
        $data = Wekit::load('forum.PwThread')->fetchThread($tids);

        return $data;
    }

    public function getData()
    {
        return $this->data;
    }

    protected function init()
    {
        $this->appendDo(new PwRevertTopicDoMain($this));
        //$this->appendDo(new PwDeleteTopicDoVirtualDelete($this));
        //$this->appendDo(new PwDeleteTopicDoDirectDelete($this));
        //$this->appendDo(new PwDeleteArticleDoAttachDelete($this));
        //$this->appendDo(new PwDeleteArticleDoForumUpdate($this));
        //$this->appendDo(new PwDeleteTopicDoFreshDelete($this));
    }

    protected function gleanData($value)
    {
        $this->tids[] = $value['tid'];
    }

    public function getIds()
    {
        return $this->tids;
    }

    protected function run()
    {
        return true;
    }
}
