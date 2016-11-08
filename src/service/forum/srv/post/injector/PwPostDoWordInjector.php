<?php

defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 帖子发布 - 话题相关
 *
 * @author jinlong.panjl <jinlong.panjl@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id$
 * @package wind
 */
class PwPostDoWordInjector extends PwBaseHookInjector
{
    public function doadd()
    {
        $verifiedWord = $this->getInput('verifiedWord');
        $tagnames = $this->getInput('tagnames');
        Wind::import('SRV:forum.srv.post.do.PwPostDoWord');

        return new PwPostDoWord($this->bp, $verifiedWord, $tagnames);
    }

    public function domodify()
    {
        $verifiedWord = $this->getInput('verifiedWord');
        $tagnames = $this->getInput('tagnames');
        Wind::import('SRV:forum.srv.post.do.PwPostDoWord');

        return new PwPostDoWord($this->bp, $verifiedWord, $tagnames);
    }
}
