<?php

defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('SRV:attention.srv.reply.weibo.PwWeiboDoBase');
/**
 * 微博 - 敏感词.
 *
 * @author jinlong.panjl <jinlong.panjl@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id$
 */
class PwWeiboDoWord extends PwWeiboDoBase
{
    public function __construct($pwWeiboCommnetDm)
    {
    }

    public function check($pwWeiboCommnetDm)
    {
        $content = $pwWeiboCommnetDm->getField('content');
        $wordFilter = Wekit::load('SRV:word.srv.PwWordFilter');

        list($type, $words) = $wordFilter->filterWord($content);
        $words = $words ? $words : array();
        if (!$type) {
            return true;
        }
        switch ($type) {
            case 1:
                return new PwError('WORD:content.error.tip', array('{wordstr}' => implode(',', $words)));
            case 2:
                return new PwError('WORD:content.error.tip', array('{wordstr}' => implode(',', $words)));
            case 3:
            default:
                return true;
        }

        return true;
    }
}
