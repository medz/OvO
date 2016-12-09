<?php

defined('WEKIT_VERSION') || exit('Forbidden');
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>.
 *
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwSpaceProfile.php 6183 2012-03-19 05:18:47Z gao.wanggao $
 */
class PwSpaceProfile extends PwBaseHookService
{
    public function __construct()
    {
        parent::__construct();
    }

    public function displayHtml($spaceBo)
    {
        $this->runDo('createHtml', $spaceBo);
    }

    protected function _getInterfaceName()
    {
        return 'PwSpaceProfileDoInterface';
    }
}
