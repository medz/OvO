<?php

defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 帖子发布 - 话题相关.
 *
 * @author jinlong.panjl <jinlong.panjl@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id$
 */
class PwPostDoTagInjector extends PwBaseHookInjector
{
    public function doadd()
    {
        $tagNames = (array) $this->getInput('tagnames', 'post');
        if (! is_array($tagNames) || ! count($tagNames)) {
            return;
        }

        return new PwPostDoTag($this->bp, $tagNames);
    }

    public function domodify()
    {
        $tagNames = (array) $this->getInput('tagnames', 'post');

        return new PwPostDoTag($this->bp, $tagNames);
    }
}
