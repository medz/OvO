<?php

Wind::import('APPS:api.controller.OpenBaseController');

/**
 * the last known user to change this file in the repository  <$LastChangedBy: jieyin $>.
 *
 * @author $Author: jieyin $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: ConfigController.php 24719 2013-02-17 06:50:42Z jieyin $
 */
class ConfigController extends OpenBaseController
{
    public function getAction()
    {
        $name = $this->getInput('name', 'get');
        $key = '';
        if (strpos($name, ':') !== false) {
            list($namespace, $key) = explode(':', $name);
        } else {
            $namespace = $name;
        }
        $config = $this->_getConfigDs()->getValues($namespace);
        $result = $key ? $config[$key] : $config;
        $this->output($result);
    }

    public function getConfigAction()
    {
        $namespace = $this->getInput('namespace', 'get');
        $result = $this->_getConfigDs()->getConfig($namespace);
        $this->output($result);
    }

    public function fetchConfigAction()
    {
        $namespace = $this->getInput('namespace', 'get');
        $result = $this->_getConfigDs()->fetchConfig($namespace);
        $this->output($result);
    }

    public function getConfigByNameAction($namespace, $name)
    {
        list($namespace, $name) = $this->getInput(array('namespace', 'name'), 'get');
        $result = $this->_getConfigDs()->getConfigByName($namespace, $name);
        $this->output($result);
    }

    public function getValuesAction()
    {
        $namespace = $this->getInput('namespace', 'get');
        $result = $this->_getConfigDs()->getValues($namespace);
        $this->output($result);
    }

    public function setConfigAction()
    {
        list($namespace, $key, $value) = $this->getInput(array('namespace', 'key', 'value'), 'post');
        $result = $this->_getConfigDs()->setConfig($namespace, $key, $value);
        $this->output(WindidUtility::result(true));
    }

    public function setConfigsAction()
    {
        list($namespace, $data) = $this->getInput(array('namespace', 'data'), 'post');
        $result = $this->_getConfigDs()->setConfigs($namespace, $data);
        $this->output(WindidUtility::result(true));
    }

    public function deleteConfigAction()
    {
        $namespace = $this->getInput('namespace', 'post');
        $result = $this->_getConfigDs()->deleteConfig($namespace);
        $this->output(WindidUtility::result(true));
    }

    public function deleteConfigByNameAction()
    {
        list($namespace, $name) = $this->getInput(array('namespace', 'name'), 'post');
        $result = $this->_getConfigDs()->deleteConfigByName($namespace, $name);
        $this->output(WindidUtility::result(true));
    }

    public function setCreditsAction()
    {
        $credits = $this->getInput('credits', 'post');
        $this->_getConfigService()->setLocalCredits($credits);
        $this->_getNotifyService()->send('setCredits', array(), $this->appid);
        $this->output(WindidUtility::result(true));
    }

    protected function _getConfigDs()
    {
        return Wekit::load('WSRV:config.WindidConfig');
    }

    private function _getConfigService()
    {
        return Wekit::load('WSRV:config.srv.WindidCreditSetService');
    }

    private function _getNotifyService()
    {
        return Wekit::load('WSRV:notify.srv.WindidNotifyService');
    }
}
