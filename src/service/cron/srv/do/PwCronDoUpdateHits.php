<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>.
 *
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwCronDoUpdateHits.php 19367 2012-10-13 07:59:36Z gao.wanggao $
 */
 

class PwCronDoUpdateHits extends AbstractCronBase
{
    public function run($cronId)
    {
        $srv = Wekit::load('forum.PwThread');
        $srv->syncHits();
    }
}
