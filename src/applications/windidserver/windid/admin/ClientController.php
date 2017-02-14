<?php

defined('WEKIT_VERSION') || exit('Forbidden');
Wind::import('APPS:windid.admin.WindidBaseController');

/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>.
 *
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: ClientController.php 29741 2013-06-28 07:54:24Z gao.wanggao $
 */
class ClientController extends WindidBaseController
{
    public function run()
    {
        $list = $this->_getAppDs()->getList();
        $data = $urls = array();
        $time = Pw::getTime();
        $this->setOutput($list, 'list');
    }

    public function clientTestAction()
    {
        $clientid = $this->getInput('clientid');
        $client = $this->_getAppDs()->getApp($clientid);
        if (!$client) {
            $this->showError('WINDID:fail');
        }
        $time = Pw::getTime();
        $array = array(
            'windidkey' => WindidUtility::appKey($client['id'], $time, $client['secretkey'], array('operation' => 999), array()),
            'operation' => 999,
            'clientid'  => $client['id'],
            'time'      => $time,
        );
        $post = array('testdata' => 1);
        $url = WindidUtility::buildClientUrl($client['siteurl'], $client['apifile']).http_build_query($array);

        $client = new \Guzzle\Http\Client();
        $request = $client->post($url, null, $post);
        $response = $client->send($request);
        $result = $response->getBody(true);

        if ($result === 'success') {
            $this->showMessage('WINDID:success');
        }
        $this->showError('WINDID:fail');
    }

    public function addAction()
    {
        $rand = WindUtility::generateRandStr(10);
        $this->setOutput(md5($rand), 'rand');
        $this->setOutput('windid.php', 'apifile');
    }

    public function doaddAction()
    {
        $apifile = $this->getInput('apifile', 'post');
        if (!$apifile) {
            $apifile = 'windid.php';
        }
        Wind::import('WSRV:app.dm.WindidAppDm');
        $dm = new WindidAppDm();
        $dm->setApiFile($apifile)
            ->setIsNotify($this->getInput('isnotify', 'post'))
            ->setIsSyn($this->getInput('issyn', 'post'))
            ->setAppName($this->getInput('appname', 'post'))
            ->setSecretkey($this->getInput('appkey', 'post'))
            ->setAppUrl($this->getInput('appurl', 'post'))
            ->setAppIp($this->getInput('appip', 'post'))
            ->setCharset($this->getInput('charset', 'post'));
        $result = $this->_getAppDs()->addApp($dm);
        if ($result instanceof WindidError) {
            $this->showError('WINDID:fail');
        }
        $this->showMessage('WINDID:success');
    }

    public function editAction()
    {
        $app = $this->_getAppDs()->getApp(intval($this->getInput('id', 'get')));
        if (!$app) {
            $this->showMessage('WINDID:fail');
        }
        $this->setOutput($app, 'app');
    }

    public function doeditAction()
    {
        Wind::import('WSRV:app.dm.WindidAppDm');
        $dm = new WindidAppDm(intval($this->getInput('id', 'post')));
        $dm->setApiFile($this->getInput('apifile', 'post'))
            ->setIsNotify($this->getInput('isnotify', 'post'))
            ->setIsSyn($this->getInput('issyn', 'post'))
            ->setAppName($this->getInput('appname', 'post'))
            ->setSecretkey($this->getInput('appkey', 'post'))
            ->setAppUrl($this->getInput('appurl', 'post'))
            ->setAppIp($this->getInput('appip', 'post'))
            ->setCharset($this->getInput('charset', 'post'));
        $result = $this->_getAppDs()->editApp($dm);
        if ($result instanceof WindidError) {
            $this->showError('ADMIN:fail');
        }
        $this->showMessage('WINDID:success');
    }

    public function deleteAction()
    {
        $result = $this->_getAppDs()->delApp(intval($this->getInput('id', 'get')));
        if ($result instanceof WindidError) {
            $this->showError('WINDID:fail');
        }
        $this->showMessage('WINDID:success');
    }

    private function _getAppDs()
    {
        return Wekit::load('WSRV:app.WindidApp');
    }
}
