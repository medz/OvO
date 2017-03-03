<?php

defined('WEKIT_VERSION') || exit('Forbidden');

 
 

/**
 * the last known user to change this file in the repository  <$LastChangedBy: xiaoxia.xuxx $>.
 *
 * @author gao.wanggao Foxsee@aliyun.com
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwSpaceProfileDoEducation.php 9091 2012-05-03 03:42:01Z xiaoxia.xuxx $
 */
class PwSpaceProfileDoEducation implements PwSpaceProfileDoInterface
{
    public function createHtml($spaceBo)
    {
        if (!$spaceBo->allowView('education')) {
            return false;
        }
        $educations = Wekit::load('education.srv.PwEducationService')->getEducationByUid($spaceBo->spaceUid, 100);
        $spaceBo->spaceUser['education'] = $educations;
        PwHook::template('education', 'TPL:space.profile_extend', true, $spaceBo);
    }
}
