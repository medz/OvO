<?php


/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>.
 *
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PortalController.php 24103 2013-01-21 10:15:47Z gao.wanggao $
 */
class PortalController extends PwBaseController
{
    public function beforeAction($handlerAdapter)
    {
        parent::beforeAction($handlerAdapter);
        if ($this->loginUser->uid < 1) {
            $this->showError('SPACE:user.not.login');
        }
        Wekit::load('design.PwDesignPermissions');
        $permissions = $this->_getPermissionsService()->getPermissionsForUserGroup($this->loginUser->uid);
        if ($permissions < PwDesignPermissions::IS_DESIGN) {
            $this->showError('DESIGN:permissions.fail');
        }
    }

    public function addAction()
    {
        //版块域名
        $domain_isopen = Wekit::C('domain', 'special.isopen');
        if ($domain_isopen) {
            $root = Wekit::C('domain', 'special.root');
            $this->setOutput($root, 'root');
        }
    }

    public function doaddAction()
    {
        $ds = $this->_getPortalDs();
        $title = $this->getInput('title', 'post');
        $coverfrom = (int) $this->getInput('coverfrom', 'post');
        $pagename = $this->getInput('pagename', 'post');
        $domain = $this->getInput('domain', 'post'); //TODO
        if (!$title) {
            $this->showError('DESIGN:title.is.empty');
        }
        if (!$pagename) {
            $this->showError('DESIGN:pagename.is.empty');
        }
        if (!$this->_validator($pagename)) {
            $this->showError('DESIGN:pagename.validator.fail');
        }
        if ($domain && !$this->_validator($domain)) {
            $this->showError('DESIGN:domain.validator.fail');
        }

        if ($ds->countPortalByPagename($pagename)) {
            $this->showError('DESIGN:pagename.already.exists');
        }
        Wind::import('SRV:design.dm.PwDesignPortalDm');
        $dm = new PwDesignPortalDm();
        $dm->setPageName($pagename)
            ->setTitle($title)
            ->setDomain($domain)
            ->setIsopen((int) $this->getInput('isopen', 'post'))
            ->setHeader((int) $this->getInput('isheader', 'post'))
            ->setNavigate((int) $this->getInput('isnavigate', 'post'))
            ->setFooter((int) $this->getInput('isfooter', 'post'))
            ->setKeywords($this->getInput('keywords', 'post'))
            ->setDescription($this->getInput('description', 'post'))

            //->setTemplate($this->getInput('isfooter', 'post'))
            ->setCreatedUid($this->loginUser->uid)
            ->setCreatedTime(Pw::getTime());
        $resource = $ds->addPortal($dm);
        if ($resource instanceof PwError) {
            $this->showError($resource->getError());
        }
        $id = (int) $resource;
        if ($coverfrom == 2) {
            $upload = $this->_upload($id);
            $cover = Pw::getPath($upload['path'].$upload['filename']);
        } else {
            $cover = $this->getInput('webcover', 'post');
            $cover = (preg_match("/^http:\/\/(.*)$/", $cover)) ? $cover : '';
        }
        if ($cover) {
            $dm = new PwDesignPortalDm($id);
            $dm->setCover($cover);
            $ds->updatePortal($dm);
        }

        //二级域名start
        list($domain, $root) = $this->getInput(array('domain', 'root'), 'post');
        if ($root) {
            if (!$domain) {
                Wekit::load('domain.PwDomain')->deleteByDomainKey("special/index/run?id=$id");
            } else {
                $r = Wekit::load('domain.srv.PwDomainService')->isDomainValid($domain, $root, "special/index/run?id=$id");
                if ($r instanceof PwError) {
                    $this->showError($r->getError());
                }
                Wind::import('SRV:domain.dm.PwDomainDm');
                $dm = new PwDomainDm();
                $dm->setDomain($domain)
                ->setDomainKey("special/index/run?id=$id")
                ->setDomainType('special')
                ->setRoot($root)
                ->setFirst($domain[0])
                ->setId($id);
                Wekit::load('domain.PwDomain')->replaceDomain($dm);
            }
            Wekit::load('domain.srv.PwDomainService')->flushAll();
        }
        //二级域名end

        //seo
        Wind::import('SRV:seo.dm.PwSeoDm');
        $dm = new PwSeoDm();
        $dm->setMod('area')
           ->setPage('custom')
           ->setParam($id)
           ->setTitle($title)
           ->setKeywords($this->getInput('keywords', 'post'))
           ->setDescription($this->getInput('description', 'post'));
        Wekit::load('seo.srv.PwSeoService')->batchReplaceSeoWithCache($dm);

        $this->showMessage('operate.success', 'special/index/run?id='.$resource, true);
    }

    public function editAction()
    {
        $id = (int) $this->getInput('id', 'get');
        $portal = $this->_getPortalDs()->getPortal($id);
        if (!$portal) {
            $this->showError('page.status.404');
        }

        //版块域名
        $domain_isopen = Wekit::C('domain', 'special.isopen');
        if ($domain_isopen) {
            $root = Wekit::C('domain', 'special.root');
            $result = Wekit::load('domain.PwDomain')->getByDomainKey("special/index/run?id=$id");
            $domain = isset($result['domain']) ? $result['domain'] : '';
            $this->setOutput($root, 'root');
            $this->setOutput($domain, 'domain');
        }

        //seo
        $seo = Wekit::load('seo.PwSeo')->getByModAndPageAndParam('area', 'custom', $id);
        $portal['title'] = $seo['title'];
        $portal['description'] = $seo['description'];
        $portal['keywords'] = $seo['keywords'];
        $this->setOutput($portal, 'portal');
    }

    public function doeditAction()
    {
        $id = (int) $this->getInput('portalid', 'post');
        $title = $this->getInput('title', 'post');
        $coverfrom = (int) $this->getInput('coverfrom', 'post');
        $pagename = $this->getInput('pagename', 'post');
        $keywords = $this->getInput('keywords', 'post');
        $description = $this->getInput('description', 'post');
        if (!$title) {
            $this->showError('DESIGN:title.is.empty');
        }
        if (!$pagename) {
            $this->showError('DESIGN:pagename.is.empty');
        }
        //二级域名start
        list($domain, $root) = $this->getInput(array('domain', 'root'), 'post');
        if ($root) {
            if (!$domain) {
                Wekit::load('domain.PwDomain')->deleteByDomainKey("special/index/run?id=$id");
            } else {
                $r = Wekit::load('domain.srv.PwDomainService')->isDomainValid($domain, $root, "special/index/run?id=$id");
                if ($r instanceof PwError) {
                    $this->showError($r->getError());
                }
                Wind::import('SRV:domain.dm.PwDomainDm');
                $dm = new PwDomainDm();
                $dm->setDomain($domain)
                ->setDomainKey("special/index/run?id=$id")
                ->setDomainType('special')
                ->setRoot($root)
                ->setFirst($domain[0])
                ->setId($id);
                Wekit::load('domain.PwDomain')->replaceDomain($dm);
            }
            Wekit::load('domain.srv.PwDomainService')->flushAll();
        }
        //二级域名end

        if (!$this->_validator($pagename)) {
            $this->showError('DESIGN:pagename.validator.fail');
        }
        $ds = $this->_getPortalDs();
        $portal = $ds->getPortal($id);
        if (!$portal) {
            $this->showError('operate.fail');
        }
        $count = $ds->countPortalByPagename($pagename);
        if ($portal['pagename'] != $pagename && $count >= 1) {
            $this->showError('DESIGN:pagename.already.exists');
        }

        if ($coverfrom == 2) {
            $cover = '';
            $upload = $this->_upload($id);
            if ($upload['filename']) {
                $cover = Pw::getPath($upload['path'].$upload['filename']);
            }
        } else {
            $cover = $this->getInput('webcover', 'post');
            $cover = (preg_match("/^http:\/\/(.*)$/", $cover)) ? $cover : '';
        }

        Wind::import('SRV:design.dm.PwDesignPortalDm');
        $dm = new PwDesignPortalDm($id);
        $dm->setPageName($pagename)
            ->setTitle($title)
            ->setCover($cover)
            ->setDomain($domain)
            ->setIsopen((int) $this->getInput('isopen', 'post'))
            ->setHeader((int) $this->getInput('isheader', 'post'))
            ->setNavigate((int) $this->getInput('isnavigate', 'post'))
            ->setFooter((int) $this->getInput('isfooter', 'post'))
            ->setKeywords($keywords)
            ->setDescription($description);
        $resource = $ds->updatePortal($dm);
        if ($resource instanceof PwError) {
            $this->showError($resource->getError());
        }
        $pageInfo = $this->_getPageDs()->getPageByTypeAndUnique(PwDesignPage::PORTAL, $id);
        //更新页面名称
        Wind::import('SRV:design.dm.PwDesignPageDm');
        $dm = new PwDesignPageDm($pageInfo['page_id']);
        $dm->setName($title);
        $this->_getPageDs()->updatePage($dm);

        //seo
        Wind::import('SRV:seo.dm.PwSeoDm');
        $dm = new PwSeoDm();
        $dm->setMod('area')
           ->setPage('custom')
           ->setParam($id)
           ->setTitle($title)
           ->setKeywords($keywords)
           ->setDescription($description);
        Wekit::load('seo.srv.PwSeoService')->batchReplaceSeoWithCache($dm);
        $this->showMessage('operate.success', 'special/index/run?id='.$id, true);
    }

    private function _validator($string)
    {
        if (preg_match('/^[\dA-Za-z\_]+$/', $string)) {
            return true;
        }

        return false;
    }

    private function _upload($portalId = 0)
    {
        Wind::import('SRV:upload.action.PwPortalUpload');

        $bhv = new PwPortalUpload($portalId);
        $upload = new PwUpload($bhv);
        if (($result = $upload->check()) === true) {
            $result = $upload->execute();
        }
        if ($result !== true) {
            $this->showError($result->getError());
        }

        return $bhv->getAttachInfo();
    }

    protected function _getPermissionsService()
    {
        return Wekit::load('design.srv.PwDesignPermissionsService');
    }

    private function _getPortalDs()
    {
        return Wekit::load('design.PwDesignPortal');
    }

    private function _getPageDs()
    {
        return Wekit::load('design.PwDesignPage');
    }
}
