<?php

Wind::import('APPS:api.controller.OpenBaseController');

/**
 * the last known user to change this file in the repository  <$LastChangedBy: jieyin $>.
 *
 * @author $Author: jieyin $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: AppController.php 24191 2013-01-22 14:05:09Z jieyin $
 */
class AppController extends OpenBaseController
{
    public function listAction()
    {
        $result = $this->_getAppDs()->getList();
        $this->output($result);
    }

    public function getAction()
    {
        $id = $this->getInput('id');
        $result = $this->_getAppDs()->getApp($id);
        $this->output($result);
    }

    public function addAction()
    {
        list($name, $siteip, $siteurl, $secretkey, $charset, $apifile, $issyn, $isnotify) = $this->getInput(['name', 'siteip', 'siteurl', 'secretkey', 'charset', 'apifile', 'issyn', 'isnotify']);
        Wind::import('WSRV:app.dm.WindidAppDm');
        $dm = new WindidAppDm();
        $dm->setAppName($name)
            ->setAppIp($siteip)
            ->setAppUrl($siteurl)
            ->setSecretkey($secretkey)
            ->setCharset($charset)
            ->setApiFile($apifile)
            ->setIsSyn($issyn)
            ->setIsNotify($isnotify);

        $result = $this->_getAppDs()->addApp($dm);
        $this->output($result);
    }

    public function deleteAction()
    {
        $id = $this->getInput('id');
        $result = $this->_getAppDs()->delApp($id);
        $this->output($result);
    }

    public function editApp()
    {
        list($id, $name, $siteip, $siteurl, $secretkey, $charset, $apifile, $issyn, $isnotify) = $this->getInput(['id', 'name', 'siteip', 'siteurl', 'secretkey', 'charset', 'apifile', 'issyn', 'isnotify']);
        $dm = new WindidAppDm($id);
        isset($name) && $dm->setAppName($name);
        isset($siteip) && $dm->setAppIp($siteip);
        isset($siteurl) && $dm->setAppUrl($siteurl);
        isset($secretkey) && $dm->setSecretkey($secretkey);
        isset($charset) && $dm->setCharset($charset);
        isset($apifile) && $dm->setApiFile($apifile);
        isset($issyn) && $dm->setIsSyn($issyn);
        isset($isnotify) && $dm->setIsNotify($isnotify);

        $result = $this->_getAppDs()->editApp($dm);
        $this->output($result);
    }

    private function _getAppDs()
    {
        return Wekit::load('WSRV:app.WindidApp');
    }
}
