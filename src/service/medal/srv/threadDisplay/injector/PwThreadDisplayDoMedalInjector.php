<?php

defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * the last known user to change this file in the repository  <$LastChangedBy: jieyin $>.
 *
 * @author $Author: jieyin $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwThreadDisplayDoMedalInjector.php 19692 2012-10-17 05:16:40Z jieyin $
 */
class PwThreadDisplayDoMedalInjector extends PwBaseHookInjector
{
    public function run()
    {
         

        return new PwThreadDisplayDoMedal();
    }
}
