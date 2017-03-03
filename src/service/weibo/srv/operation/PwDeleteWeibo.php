<?php

/**
 * 删除新鲜事及其关联操作(扩展).
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwDeleteWeibo.php 8959 2012-04-28 09:06:05Z jieyin $
 */
class PwDeleteWeibo extends PwGleanDoProcess
{
    public $data = array();
    public $ids = array();
    public $user;

    public $isDeductCredit = false;

    public function __construct(iPwDataSource $ds, PwUserBo $user)
    {
        $this->data = $ds->getData();
        $this->user = $user;
        parent::__construct();
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

    public function getData()
    {
        return $this->data;
    }

    protected function init()
    {
        $this->appendDo(new PwDeleteWeiboDoCommentDelete($this));
    }

    protected function gleanData($value)
    {
        $this->ids[] = $value['weibo_id'];
    }

    public function getIds()
    {
        return $this->ids;
    }

    protected function run()
    {
        Wekit::load('weibo.PwWeibo')->batchDeleteWeibo($this->ids);

        return true;
    }
}
