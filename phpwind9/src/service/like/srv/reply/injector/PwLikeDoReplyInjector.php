<?php

defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * the last known user to change this file in the repository  <$LastChangedBy$>.
 *
 * @author $Author$ Foxsee@aliyun.com
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id$
 */
class PwLikeDoReplyInjector extends PwBaseHookInjector
{
    public function run()
    {
        $from = $this->getInput('from_type', 'post');
        $pid = (int) $this->getInput('pid', 'post');
        $tid = (int) $this->getInput('tid', 'post');
        if ($from != 'like') {
            return true;
        }
        if ($pid < 1 && $tid < 1) {
            return true;
        }
        $ds = Wekit::load('like.PwLikeContent');
        if ($pid) {
            $info = $ds->getInfoByTypeidFromid(PwLikeContent::POST, $pid);
        } else {
            $info = $ds->getInfoByTypeidFromid(PwLikeContent::THREAD, $tid);
        }
        if (! isset($info['likeid'])) {
            return true;
        }

        return new PwLikeDoReply($info['likeid']);
    }
}
