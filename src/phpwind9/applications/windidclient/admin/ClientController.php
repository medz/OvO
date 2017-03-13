<?php

defined('WEKIT_VERSION') || exit('Forbidden');
Wind::import('ADMIN:library.AdminBaseController');

/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>.
 *
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: ClientController.php 29745 2013-06-28 09:07:39Z gao.wanggao $
 */
class ClientController extends AdminBaseController
{
    public function run()
    {
        $list = $this->_getAppDs()->getList();
        $data = $urls = [];
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
        $array = [
            'windidkey' => WindidUtility::appKey(
                $client['id'],
                $time,
                $client['secretkey'],
                [
                    'operation' => 999,
                ],
                [
                    'testdata' => 1,
                ]
            ),
            'operation' => 999,
            'clientid'  => $client['id'],
            'time'      => $time,
        ];
        $post = ['testdata' => 1];
        $url = WindidUtility::buildClientUrl($client['siteurl'], $client['apifile']).http_build_query($array);

        $client = new \GuzzleHttp\Client();
        $response = $client->request('POST', $url, [
            'form_params' => $post,
        ]);
        $result = $response->getBody();

        if (trim($result) === 'success') {
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
        Wind::import('WINDID:service.app.dm.WindidAppDm');
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
        Wind::import('WINDID:service.app.dm.WindidAppDm');
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
        $id = intval($this->getInput('id', 'post'));
        if (!$id) {
            $this->showError('operate.fail');
        }
        $result = $this->_getAppDs()->delApp($id);
        if ($result instanceof WindidError) {
            $this->showError('WINDID:fail');
        }
        $this->showMessage('WINDID:success');
    }

    private function _getAppDs()
    {
        return WindidApi::api('app');
    }
}
