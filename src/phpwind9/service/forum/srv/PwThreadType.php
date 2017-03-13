<?php

defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 可扩展的帖子类型.
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @license http://www.phpwind.com
 *
 * @version $Id: PwThreadType.php 22440 2012-12-24 09:17:41Z jieyin $
 */
class PwThreadType
{
    public $tType = [
        'default' => ['普通帖', '发布主题', true],
        'poll'    => ['投票帖', '发起投票', 'allow_add_vote'],
        //'2' => array('悬赏帖', '发起悬赏', true),
        //'3' => array('商品帖', '发布商品', true),
        //'4' => array('辩论帖', '发起辩论')
    ];

    public function __construct()
    {
        $this->tType = PwSimpleHook::getInstance('PwThreadType')->runWithFilters($this->tType);
    }

    public function getTtype()
    {
        return $this->tType;
    }

    public function has($special)
    {
        return isset($this->tType[$special]);
    }

    public function getName($special)
    {
        return $this->tType[$special][0];
    }
}
