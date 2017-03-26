<?php

defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>.
 *
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwThreadDisplayDoLikeInjector.php 4350 2012-02-16 02:19:16Z gao.wanggao $
 */
class PwThreadDisplayDoLikeInjector extends PwBaseHookInjector
{
    public function run()
    {
        return new PwThreadDisplayDoLike();
    }
}
