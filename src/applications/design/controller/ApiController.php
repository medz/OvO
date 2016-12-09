<?php


/**
 * the last known user to change this file in the repository  <$LastChangedBy: yetianshi $>.
 *
 * @author $Author: yetianshi $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: ApiController.php 28830 2013-05-27 03:37:39Z yetianshi $
 */
class ApiController extends PwBaseController
{
    public function run()
    {
        header('Cache-control: max-age=60');
        $moduleId = (int) $this->getInput('id', 'get');
        $token = $this->getInput('token', 'get');
        $out = $this->getInput('format', 'get');
        $script = $this->_getScriptDs()->getScript($moduleId);
        if (!$token || !$script || $script['token'] != $token) {
            exit('fail');
        }
        !$out && $out = 'script';
        if (!in_array($out, array('script', 'json', 'xml'))) {
            exit('fail');
        }
        $method = $out.'Format';

        return $this->$method($moduleId);
    }

    protected function scriptFormat($moduleId)
    {
        Wind::import('SRV:design.bo.PwDesignModuleBo');
        $bo = new PwDesignModuleBo($moduleId);
        $module = $bo->getModule();
        if ($module['module_type'] != PwDesignModule::TYPE_SCRIPT) {
            exit('fail');
        }
        //$bo->setStdId();
        PwDesignModuleBo::$stdId = $moduleId;
        $key = Wekit::load('design.srv.display.PwDesignDisplay')->bindDataKey($moduleId);
        $data[$key] = $bo->getData(true);
        $this->forward->getWindView()->compileDir = 'DATA:compile.design.script.'.$moduleId;
        $this->setOutput($data, '__design_data');
        $this->setTemplate('TPL:design.api_script');
    }

    protected function jsonFormat($moduleId)
    {
        $_data = array();
        Wind::import('SRV:design.bo.PwDesignModuleBo');
        $bo = new PwDesignModuleBo($moduleId);
        $module = $bo->getModule();
        if ($module['module_type'] != PwDesignModule::TYPE_SCRIPT) {
            exit('fail');
        }
        $data = $bo->getData(true);
        header('Content-type: application/json');
        echo Pw::jsonEncode($data);
        exit;
    }

    protected function xmlFormat($moduleId)
    {
        Wind::import('SRV:design.bo.PwDesignModuleBo');
        $bo = new PwDesignModuleBo($moduleId);
        $module = $bo->getModule();
        if ($module['module_type'] != PwDesignModule::TYPE_SCRIPT) {
            exit('fail');
        }
        $data = $bo->getData(true);
        $dom = new DOMDocument('1.0', 'utf-8');
        $root = $dom->createElement('root');
        $dom->appendChild($root);
        foreach ($data as $v) {
            $child = $dom->createElement('data');
            foreach ((array) $v as $_k => $_v) {
                $_v = WindSecurity::escapeHTML($_v);
                $_k = $dom->createElement($_k);
                $_k->appendChild($dom->createTextNode($_v));
                $child->appendChild($_k);
                $root->appendChild($child);
            }
        }
        header('Content-type: application/xml');
        echo $dom->saveXML();
        exit;
    }

    private function _getScriptDs()
    {
        return Wekit::load('design.PwDesignScript');
    }
}
