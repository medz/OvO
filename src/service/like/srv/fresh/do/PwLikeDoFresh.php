<?php

defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('SRV:forum.srv.post.do.PwPostDoBase');
/**
 * the last known user to change this file in the repository  <$LastChangedBy: jieyin $>.
 *
 * @author $Author: jieyin $ Foxsee@aliyun.com
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwLikeDoFresh.php 22678 2012-12-26 09:22:23Z jieyin $
 */
class PwLikeDoFresh extends PwPostDoBase
{
    private $info = array();
    private $content = '';
    private $userBo;

    public function __construct(PwPost $pwpost, $content = '')
    {
        $this->info = $pwpost->action->getInfo();
        $this->content = $this->info['subject'] ? $this->info['subject'] : $content;
        $this->userBo = $pwpost->user;
    }

    public function addPost($pid, $tid)
    {
        $url = WindUrlHelper::createUrl('bbs/read/run', array('tid' => $this->info['tid'], 'fid' => $this->info['fid']));
        $lang = Wind::getComponent('i18n');
        $content = $lang->getMessage('BBS:like.like.flesh').'[url='.$url.']'.$this->content.'[/url]';
        Wind::import('SRV:weibo.dm.PwWeiboDm');
        Wind::import('SRV:weibo.srv.PwSendWeibo');
        Wind::import('SRV:weibo.PwWeibo');
        $dm = new PwWeiboDm();
        $dm->setContent($content)
              ->setType(PwWeibo::TYPE_LIKE);
        $sendweibo = new PwSendWeibo($this->userBo);
        $sendweibo->send($dm);

        return true;
    }
}
