<?php

defined('WEKIT_VERSION') || exit('Forbidden');



/**
 * 合并版块--移动附件
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwUniteForumDoMoveAttach.php 21318 2012-12-04 09:24:09Z jieyin $
 * @package forum
 */

class PwUniteForumDoMoveAttach extends iPwDoHookProcess
{
    public function run($ids)
    {
        Wekit::load('attach.PwThreadAttach')->updateFid($this->srv->fid, $this->srv->tofid);
    }
}
