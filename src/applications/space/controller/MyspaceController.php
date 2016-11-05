<?php

Wind::import('LIB:base.PwBaseController');
Wind::import('SRV:space.bo.PwSpaceBo');
/**
 * 我的空间
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: MyspaceController.php 28765 2013-05-23 03:05:46Z gao.wanggao $
 * @package
 */
class MyspaceController extends PwBaseController
{
    public $spaceBo;

    public function beforeAction($handlerAdapter)
    {
        parent::beforeAction($handlerAdapter);
        if ($this->loginUser->uid < 1) {
            $this->showError('SPACE:user.not.login');
        }
    }

    /**
     * 空间设置
     * @see wekit/wind/web/WindController::run()
     */
    public function run()
    {
        $perpage = 6;
        $page = 1;
        $this->spaceBo = new PwSpaceBo($this->loginUser->uid);
        $list = $this->_getStyleDs()->getAllStyle('space');
        $addons = Wekit::load('APPCENTER:service.srv.PwInstallApplication')->getConfig('style-type');

        //个性域名
        $domain_isopen = Wekit::C('domain', 'space.isopen');
        if ($domain_isopen) {
            $spaceroot = Wekit::C('domain', 'space.root');
            $domain = $this->_spaceDomainDs()->getDomainByUid($this->loginUser->uid);
            $this->setOutput($spaceroot, 'spaceroot');
            $this->setOutput($domain ? $domain : '', 'spacedomain');
        }

        $this->setOutput($list, 'list');
        $this->setOutput($perpage, 'perpage');
        $this->setOutput(ceil(count($list) / $perpage), 'totalpage');
        $this->setOutput(Wekit::url()->themes.'/'.$addons['space'][1], 'themeUrl');
        $this->setOutput($this->spaceBo, 'space');
    }

    /**
     * 判断域名是否可用
     * Enter description here ...
     */
    public function allowdomainAction()
    {
        list($domain, $root) = $this->getInput(array('domain', 'root'));
        if (!$domain) {
            return $this->showError('SPACE:domain.fail');
        }
        $uid = $this->_spaceDomainDs()->getUidByDomain($domain);
        if ($uid && $uid != $this->loginUser->uid) {
            $this->showError('REWRITE:domain.exist');
        }
        $this->showMessage('success');
    }


    /**
     * 空间基本信息处理
     * Enter description here ...
     */
    public function doEditSpaceAction()
    {
        $spaceName = $this->getInput('spacename', 'post');
        $descrip = $this->getInput('descrip', 'post');

        //个性域名
        list($domain, $spaceroot) = $this->getInput(array('domain', 'spaceroot'));
        if ($spaceroot) {
            if (!$domain) {
                $this->_spaceDomainDs()->delDomain($this->loginUser->uid);
            } else {
                $uid = $this->_spaceDomainDs()->getUidByDomain($domain);
                if ($uid && $uid != $this->loginUser->uid) {
                    $this->showError('REWRITE:domain.exist');
                }
                $r = $this->_spaceDomainDs()->getDomainByUid($this->loginUser->uid);
                if (!$r) {
                    $this->_spaceDomainDs()->addDomain($this->loginUser->uid, $domain);
                } else {
                    $this->_spaceDomainDs()->updateDomain($this->loginUser->uid, $domain);
                }
            }
        }

        Wind::import('SRV:word.srv.PwWordFilter');
        $word = PwWordFilter::getInstance();
        if ($word->filter($spaceName)) {
            $this->showError('SPACE:spacename.filter.fail');
        }
        if ($word->filter($descrip)) {
            $this->showError('SPACE:descrip.filter.fail');
        }

        Wind::import('SRV:space.dm.PwSpaceDm');
        $dm = new PwSpaceDm($this->loginUser->uid);
        $dm->setSpaceName($spaceName)
            ->setSpaceDescrip($descrip)
            ->setSpaceDomain($domain);
        $resource = $this->_getSpaceDs()->updateInfo($dm);
        if ($resource instanceof PwError) {
            $this->showError($resource->getError());
        }
        $this->showMessage('MEDAL:success');
    }

    /**
     * 空间风格设置
     * Enter description here ...
     */
    public function doEditStyleAction()
    {
        $styleid = $this->getInput('id', 'post');
        $style = $this->_getStyleDs()->getStyle($styleid);
        if (!$style) {
            $this->showError('SPACE:fail');
        }
        Wind::import('SRV:space.dm.PwSpaceDm');
        $dm = new PwSpaceDm($this->loginUser->uid);
        $dm->setSpaceStyle($style['alias']);
        $resource = $this->_getSpaceDs()->updateInfo($dm);
        if ($resource instanceof PwError) {
            $this->showError($resource->getError());
        }
        $this->showMessage('MEDAL:success');
    }


    /**
     * 空间背景设置
     * Enter description here ...
     */
    public function doEditBackgroundAction()
    {
        $repeat = $this->getInput('repeat', 'post');
        $fixed = $this->getInput('fixed', 'post');
        $align = $this->getInput('align', 'post');
        $background = $this->getInput('background', 'post');
        $upload = $this->_uploadImage();
        $image = isset($upload['path']) ? $upload['path'] : '';
        $this->spaceBo = new PwSpaceBo($this->loginUser->uid);
        if (!$image) {
            //list($image, $_repeat, $_fixed, $_align) = $this->spaceBo->space['back_image'];
            if (!$background) {
                $image = $repeat = $fixed = $align = '';
            } else {
                $image = $background;
            }
        }
        if (!in_array($repeat, array('no-repeat', 'repeat'))) {
            $repeat = 'no-repeat';
        }
        if (!in_array($fixed, array('fixed', 'scroll'))) {
            $fixed = 'scroll';
        }
        if (!in_array($align, array('left', 'right', 'center'))) {
            $align = 'left';
        }

        Wind::import('SRV:space.dm.PwSpaceDm');
        $dm = new PwSpaceDm($this->loginUser->uid);
        $dm->setBackImage($image, $repeat, $fixed, $align);
        $resource = $this->_getSpaceDs()->updateInfo($dm);
        if ($resource instanceof PwError) {
            $this->showError($resource->getError());
        }
        $this->showMessage('MEDAL:success');
    }

    public function delbackground()
    {
    }


    public function doreplyAction()
    {
        $id = (int) $this->getInput('id', 'post');
        $content = $this->getInput('content', 'post');
        $transmit = $this->getInput('transmit', 'post');

        Wind::import('SRV:attention.srv.PwFreshReplyPost');
        $reply = new PwFreshReplyPost($id, $this->loginUser);

        if (($result = $reply->check()) !== true) {
            $this->showMessage($result->getError());
        }
        $reply->setContent($content);
        $reply->setIsTransmit($transmit);

        if (($result = $reply->execute()) instanceof PwError) {
            $this->showMessage($result->getError());
        }
        if (!$reply->getIscheck()) {
            $this->showError('BBS:post.reply.ischeck');
        }
        $content = Wekit::load('forum.srv.PwThreadService')->displayContent($content, $reply->getIsuseubb(), $reply->getRemindUser());
        $this->setOutPut(Pw::getTime(), 'timestamp');
        $this->setOutPut($content, 'content');
        $this->setOutPut($this->loginUser->username, 'username');
    }

    private function _uploadImage()
    {
        Wind::import('SRV:upload.action.PwSpaceUpload');
        Wind::import('LIB:upload.PwUpload');
        $bhv = new PwSpaceUpload($this->loginUser->uid);
        $upload = new PwUpload($bhv);
        if (($result = $upload->check()) === true) {
            $result = $upload->execute();
        }
        if ($result !== true) {
            $this->showError($result->getError());
        }

        return $bhv->getAttachInfo();
    }

    private function _getSpaceDs()
    {
        return Wekit::load('SRV:space.PwSpace');
    }

    /**
     * @return PwStyle
     */
    private function _getStyleDs()
    {
        return Wekit::load('APPCENTER:service.PwStyle');
    }

    /**
     * @return PwSpaceDomain
     */
    private function _spaceDomainDs()
    {
        return Wekit::load('domain.PwSpaceDomain');
    }
}
