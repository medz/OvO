<?php

defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('LIB:process.PwGleanDoProcess');
Wind::import('SRV:attention.PwFresh');
Wind::import('HOOK:PwDeleteFresh.PwDeleteFreshDoTopicDelete');
Wind::import('HOOK:PwDeleteFresh.PwDeleteFreshDoReplyDelete');
Wind::import('HOOK:PwDeleteFresh.PwDeleteFreshDoWeiboDelete');

/**
 * 删除新鲜事及其关联操作(扩展)
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwDeleteFresh.php 8959 2012-04-28 09:06:05Z jieyin $
 * @package forum
 */

class PwDeleteFresh extends PwGleanDoProcess
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
     * setting - 是否扣除积分
     *
     * @param  bool   $isDeductCredit 是否扣除积分
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
        $this->appendDo(new PwDeleteFreshDoTopicDelete($this));
        $this->appendDo(new PwDeleteFreshDoReplyDelete($this));
        $this->appendDo(new PwDeleteFreshDoWeiboDelete($this));
    }

    protected function gleanData($value)
    {
        $this->ids[] = $value['id'];
    }

    public function getIds()
    {
        return $this->ids;
    }

    protected function run()
    {
        Wekit::load('attention.PwFresh')->batchDelete($this->ids);

        return true;
    }
}
