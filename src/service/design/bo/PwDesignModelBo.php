<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwDesignModelBo.php 23901 2013-01-17 03:52:47Z gao.wanggao $
 * @package
 */
class PwDesignModelBo
{
    public $modle;
    private $_modelInfo;

    public function __construct($modle)
    {
        $this->modle = $modle;
        $this->_setModleInfo();
    }

    public function getModel()
    {
        return $this->_modelInfo;
    }

    public function isModel()
    {
        if (!$this->_modelInfo) {
            return false;
        }

        return true;
    }

    public function getProperty()
    {
        return $this->_modelInfo['property'];
    }

    public function getSignKeys()
    {
        $modelSign = $this->_modelInfo['sign'];
        $sysSign = array(
                    array('<title>', '模块标题'),
                    array('<for:>...</for>', 'foreach循环'),
                    array('<for:正整数>...</for>', '指定条数循环'),
                    array('<if:odd>...</if>', '奇数行'),
                    array('<if:even>...</if>', '偶数行'),
                    array('<if:正整数>...</if>', '指定数字行'),
                    array('<if:标签>...</if>', '判断某标签为空'),
                    array('<if:!标签>...</if>', '判断某标签不为空'),
                    array('<else:>', '条件判断:否则'),
                );

        return array_merge($modelSign, $sysSign);
    }

    public function getStandardSign()
    {
        return $this->_modelInfo['standardSign'];
    }

    public function transformCustom($vKey, $vProperty)
    {
        $html = $this->_getCustomHtml($vKey);
        if (preg_match_all('/\{\$property\[(.+)\]}/isU', $html, $matches)) {
            foreach ($matches[1] as $k => $v) {
                $out = isset($vProperty[$v]) ? $vProperty[$v] : '';
                $html = str_replace($matches[0][$k], $out, $html);
            }
        }

        return $html;
    }

    private function _getCustomHtml($vKey)
    {
        $_configParser = Wind::getComponent('configParser');
        $configPath = Wind::getRealPath('SRV:design.srv.model.'.$this->modle.'.html_'.$vKey);
        if (!is_file($configPath)) {
            return array();
        }

        return $_configParser->parse($configPath);
    }


    private function _setModleInfo()
    {
        $_configParser = Wind::getComponent('configParser');
        $configPath = Wind::getRealPath('SRV:design.srv.model.'.$this->modle.'.config');
        if (!is_file($configPath)) {
            $this->_modelInfo = array();

            return;
        }
        $config = $_configParser->parse($configPath);
        $config['property'] = array_merge($config['normal'], $config['special']);
        $this->_modelInfo = $config;
    }
}
