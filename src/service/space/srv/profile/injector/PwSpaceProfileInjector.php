<?php

defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>.
 *
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwSpaceProfileInjector.php 6141 2012-03-17 09:51:29Z gao.wanggao $
 */
class PwSpaceProfileInjector extends PwBaseHookInjector
{
    public function extendWork()
    {
         

        return new PwSpaceProfileDoWork();
    }

    public function extendEducation()
    {
         

        return new PwSpaceProfileDoEducation();
    }
}
