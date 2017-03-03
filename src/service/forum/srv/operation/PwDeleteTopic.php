<?php

/**
 * 删除帖子及其关联操作(扩展).
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwDeleteTopic.php 17512 2012-09-06 04:50:49Z xiaoxia.xuxx $
 */
class PwDeleteTopic extends PwGleanDoProcess
{
    public $data = array();
    public $tids = array();
    public $user;

    public $isRecycle = false;
    public $isDeductCredit = false;
    public $isDeleteFresh = true;
    public $reason;

    public function __construct(iPwDataSource $ds, PwUserBo $user)
    {
        $this->data = $ds->getData();
        $this->user = $user;
        parent::__construct();
    }

    /**
     * setting - 是否删除到回收站.
     *
     * @param bool $recycle 是否删除到回收站
     *
     * @return object $this
     */
    public function setRecycle($recycle)
    {
        $this->isRecycle = $recycle;

        return $this;
    }

    /**
     * setting - 是否扣除积分.
     *
     * @param bool $isDeductCredit 是否扣除积分
     *
     * @return object $this
     */
    public function setIsDeductCredit($isDeductCredit)
    {
        $this->isDeductCredit = $isDeductCredit;

        return $this;
    }

    /**
     * setting - 是否同步删除新鲜事.
     *
     * @param bool $isDeleteFresh 是否同步删除新鲜事
     *
     * @return object $this
     */
    public function setIsDeleteFresh($isDeleteFresh)
    {
        $this->isDeleteFresh = $isDeleteFresh;

        return $this;
    }

    /**
     * setting - 删除理由.
     *
     * @param string $reason
     *
     * @return object $this
     */
    public function setReason($reason)
    {
        $this->reason = $reason;

        return $this;
    }

    public function getData()
    {
        return $this->data;
    }

    protected function init()
    {
        if ($this->isRecycle) {
            $this->appendDo(new PwDeleteTopicDoVirtualDelete($this));
        } else {
            $this->appendDo(new PwDeleteTopicDoDirectDelete($this));
        }

        if ($this->isDeleteFresh) {
            $this->appendDo(new PwDeleteTopicDoFreshDelete($this));
        }
        //change: when delete topic then user's data about thread like postnum digests while be updated rather than credit
        $this->appendDo(new PwDeleteTopicDoUserUpdate($this));
        $this->appendDo(new PwDeleteArticleDoAttachDelete($this));
        $this->appendDo(new PwDeleteArticleDoForumUpdate($this));
        $this->appendDo(new PwDeleteTopicDoSpecialDelete($this));
        $this->appendDo(new PwDeleteTopicDoTagDelete($this));
        $this->appendDo(new PwDeleteTopicDoPollDelete($this));
        $this->appendDo(new PwDeleteTopicDoDigestDelete($this));
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
