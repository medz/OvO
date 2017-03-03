<?php

defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>.
 *
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwLikeDoFreshInjector.php 7776 2012-04-11 12:27:20Z gao.wanggao $
 */
class PwLikeDoFreshInjector extends PwBaseHookInjector
{
    public function run()
    {
        $isfresh = $this->getInput('isfresh', 'post');
        $content = $this->getInput('atc_content', 'post');
         

        return  new PwLikeDoFresh($this->bp, $content);
    }
}
