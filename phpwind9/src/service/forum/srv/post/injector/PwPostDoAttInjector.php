<?php

defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 帖子发布-投票帖 相关服务
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwPostDoAttInjector.php 17614 2012-09-07 03:14:46Z yanchixia $
 */
class PwPostDoAttInjector extends PwBaseHookInjector
{
    public function run()
    {
        $flashatt = $this->getInput('flashatt', 'post');
        if (empty($_FILES) && empty($flashatt)) {
            return null;
        }

        return new PwPostDoAtt($this->bp, $flashatt);
    }

    public function domodify()
    {
        $flashatt = $this->getInput('flashatt', 'post');

        $postAtt = new PwPostDoAtt($this->bp, $flashatt);
        if ($postAtt->hasAttach()) {
            $oldatt_desc = $this->getInput('oldatt_desc', 'post');
            $oldatt_needrvrc = $this->getInput('oldatt_needrvrc', 'post');
            $oldatt_ctype = $this->getInput('oldatt_ctype', 'post');
            $postAtt->editAttachs($oldatt_desc, $oldatt_needrvrc, $oldatt_ctype);
        } elseif (! $flashatt) {
            return null;
        }

        return $postAtt;
    }
}
