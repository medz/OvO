<?php

defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('SRV:forum.srv.post.do.PwPostDoPoll');

/**
 * 帖子发布-投票帖 相关服务
 *
 * @author MingXing Sun <mingxing.sun@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwPostDoPollInjector.php 22440 2012-12-24 09:17:41Z jieyin $
 */
class PwPostDoPollInjector extends PwBaseHookInjector
{
    public function run()
    {
        $poll = new PwPostDoPoll($this->bp);

        return $poll;
    }

    /**
     * 注入器处理编辑投票展示页.
     *
     * @return object
     */
    public function modify()
    {
        $poll = new PwPostDoPoll($this->bp, $this->bp->action->tid);
        $poll->info = $poll->getThreadPollBo()->info;

        return $poll;
    }

    /**
     * 注入器处理增加投票.
     *
     * return object
     */
    public function doadd()
    {
        $poll = array(
            'option' => $this->getInput('option', 'post'),
            'poll'   => $this->getInput('poll', 'post'),
        );

        return new PwPostDoPoll($this->bp, 0, $poll);
    }

    /**
     * 注入器处理编辑投票动作.
     *
     * return object
     */
    public function domodify()
    {
        $poll = array(
            'option'    => (array) $this->getInput('option', 'post'),
            'newoption' => (array) $this->getInput('newoption', 'post'),
            'poll'      => $this->getInput('poll', 'post'),
        );

        return new PwPostDoPoll($this->bp, $this->bp->action->tid, $poll);
    }
}
