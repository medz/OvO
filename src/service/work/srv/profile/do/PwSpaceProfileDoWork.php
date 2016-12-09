<?php

defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('SRV:space.srv.profile.do.PwSpaceProfileDoInterface');

/**
 * the last known user to change this file in the repository  <$LastChangedBy: jieyin $>.
 *
 * @author $Author: jieyin $ Foxsee@aliyun.com
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwSpaceProfileDoWork.php 28929 2013-05-31 02:33:45Z jieyin $
 */
class PwSpaceProfileDoWork implements PwSpaceProfileDoInterface
{
    public function createHtml($spaceBo)
    {
        if (!$spaceBo->allowView('work')) {
            return false;
        }
        $spaceBo->spaceUser['work'] = Wekit::load('work.PwWork')->getByUid($spaceBo->spaceUid, 100);
        PwHook::template('work', 'TPL:space.profile_extend', true, $spaceBo);
    }
}
