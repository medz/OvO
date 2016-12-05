<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: jieyin $>.
 *
 * @author $Author: jieyin $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwCheckVerifyService.php 18618 2012-09-24 09:31:00Z jieyin $
 */
class PwCheckVerifyService
{
    public function checkVerify($inputCode)
    {
        Wind::import('SRV:verify.srv.PwVerifyService');
        $srv = new PwVerifyService('PwVerifyService_getVerifyType');
        $config = Wekit::C('verify');

        return $srv->checkVerify($config['type'], $inputCode);
    }
}
