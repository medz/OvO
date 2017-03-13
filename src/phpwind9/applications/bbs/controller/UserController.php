<?php

defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 版块会员.
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @license http://www.phpwind.com
 *
 * @version $Id: UserController.php 23994 2013-01-18 03:51:46Z long.shi $
 */
class UserController extends PwBaseController
{
    public function run()
    {
        $fid = $this->getInput('fid');
        $type = intval($this->getInput('type', 'get')); // 主题分类ID
        $page = intval($this->getInput('page', 'get'));
        $page < 1 && $page = 1;
        $perpage = Wekit::C('bbs', 'thread.perpage');
        $pwforum = new PwForumBo($fid, true);

        if (!$pwforum->isForum(true)) {
            $this->showError('BBS:forum.exists.not');
        }
        if (($result = $pwforum->allowVisit($this->loginUser)) !== true) {
            $this->showError($result->getError());
        }

        $totalJoin = Wekit::load('forum.PwForumUser')->countUserByFid($fid);
        $joinUser = Wekit::load('forum.PwForumUser')->getUserByFid($fid, 15);
        $activeUser = Wekit::load('forum.srv.PwForumUserService')->getActiveUser($fid, 7, 50);
        $uids = array_merge(array_keys($joinUser), array_keys($activeUser));
        $users = Wekit::load('user.PwUser')->fetchUserByUid($uids);

        $guide = $pwforum->headguide();
        $guide .= $this->buildBread('会员', 'bbs/user/run?fid='.$fid);
        $this->setOutput($fid, 'fid');
        $this->setOutput($pwforum, 'pwforum');
        $this->setOutput($guide, 'headguide');

        $this->setOutput($totalJoin, 'totalJoin');
        $this->setOutput($joinUser, 'joinUser');
        $this->setOutput($activeUser, 'activeUser');
        $this->setOutput($users, 'users');

        //版块风格
        //版块风格
        if ($pwforum->foruminfo['style']) {
            $this->setTheme('forum', $pwforum->foruminfo['style']);
            //$this->addCompileDir($pwforum->foruminfo['style']);
        }

        // seo设置

        $seoBo = PwSeoBo::getInstance();
        $lang = Wind::getComponent('i18n');
        $seoBo->setCustomSeo(
            $lang->getMessage('SEO:bbs.user.run.title', [$pwforum->foruminfo['name']]), '',
            $lang->getMessage('SEO:bbs.user.run.description', [$pwforum->foruminfo['name']]));
        Wekit::setV('seo', $seoBo);
    }
}
