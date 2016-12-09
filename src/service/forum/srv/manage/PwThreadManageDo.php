<?php

/**
 * 帖子发布流程.
 *
 * -> 1.check 检查帖子发布运行环境
 * -> 2.appendDo(*) 增加帖子发布时的行为动作,例:投票、附件等(可选)
 * -> 3.execute 发布
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwThreadManageDo.php 21170 2012-11-29 12:05:09Z xiaoxia.xuxx $
 */
abstract class PwThreadManageDo
{
    public $srv;
    protected $_reason = '';

    public function __construct(PwThreadManage $srv)
    {
        $this->srv = $srv;
    }

    abstract public function check($permission);

    abstract public function gleanData($value);

    abstract public function run();

    /**
     * 设置操作原因--操作日志中需要
     *
     * @param string $reason
     *
     * @return PwThreadManageDo
     */
    public function setReason($reason)
    {
        $this->_reason = $reason;

        return $this;
    }
}
