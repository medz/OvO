<?php

defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * Enter description here ...
 *
 * @author jinlong.panjl <jinlong.panjl@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id$
 * @package wind
 */
class PwThreadDisplayDoWordInjector extends PwBaseHookInjector
{
    public function run()
    {
        Wind::import('SRV:forum.srv.threadDisplay.do.PwThreadDisplayDoWord');

        return new PwThreadDisplayDoWord();
    }
}
