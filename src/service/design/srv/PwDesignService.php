<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>.
 *
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwDesignService.php 28208 2013-05-07 09:58:07Z gao.wanggao $
 */
class PwDesignService
{
    public function getModelList()
    {
        $modelList = array();
        $list = WindFolder::read(Wind::getRealDir('SRV:design.srv.model'), WindFolder::READ_DIR);
        $_configParser = Wind::getComponent('configParser');
        foreach ($list as $model) {
            $configPath = Wind::getRealPath('SRV:design.srv.model.'.$model.'.config');
            if (!is_file($configPath)) {
                continue;
            }
            $config = $_configParser->parse($configPath);
            if (!isset($config['model'])) {
                continue;
            }
            $modelList[$config['model']] = array('name' => $config['name'], 'type' => $config['type']);
        }

        return $modelList;
    }

    public function getDesignModelType($select = '')
    {
        $array = array(
            'bbs'   => '论坛模型',
            'user'  => '用户模型',
            'space' => '空间模型',
            'other' => '其它模型',
            'api'   => '扩展模型',
        );

        return $select ? $array[$select] : $array;
    }

    /**
     * 获取应用中心门户模版.
     */
    public function getDesignAppStyle($page = 1, $perpage = 10)
    {
        $type = 'portal';
        $ds = Wekit::load('APPCENTER:service.PwStyle');
        $addons = Wekit::load('APPCENTER:service.srv.PwInstallApplication')->getConfig('style-type');
        $page < 1 && $page = 1;
        list($start, $limit) = Pw::page2limit($page, $perpage);
        $list = $ds->getStyleListByType($type, $limit, $start);
        foreach ($list as &$v) {
            if ($v['logo'] && (strpos($v['logo'], 'http://') === false)) {
                $args = array(Wekit::url()->themes, $addons[$type][1], $v['alias'], $v['logo']);
                $v['logo'] = implode('/', $args);
            }
        }

        return $list;
    }

    public function getSysListClass($select = '')
    {
        $array = array(
            '默认' => 'mod_no',
            '灰色' => 'mod_boxA',
            '浅蓝' => 'mod_boxB',
            '红色' => 'mod_boxC',
            '橙色' => 'mod_boxD',
            '黄色' => 'mod_boxE',
            '绿色' => 'mod_boxF',
            '青色' => 'mod_boxG',
            '蓝色' => 'mod_boxH',
            '紫色' => 'mod_boxI',
        );

        return $select ? $array[$select] : $array;
    }

    public function getSysStyleClass($select = '')
    {
        $array = array(
            '默认'  => 'box_wrap',
            '无样式' => 'box_no',
            '灰色'  => 'layout_boxA',
            '浅蓝'  => 'layout_boxB',
            '红色'  => 'layout_boxC',
            '橙色'  => 'layout_boxD',
            '黄色'  => 'layout_boxE',
            '绿色'  => 'layout_boxF',
            '青色'  => 'layout_boxG',
            '蓝色'  => 'layout_boxH',
            '紫色'  => 'layout_boxI',
        );

        return $select ? $array[$select] : $array;
    }

    public function getSysFontSize($select = '')
    {
        $array = array(10, 12, 14, 16, 18, 20);

        return $select ? $select : $array;
    }

    public function getSysLineWidth($select = '')
    {
        $array = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10);

        return $select ? $select : $array;
    }

    public function getSysBorderStyle($select = '')
    {
        $array = array(
            'solid'  => '实线',
            'dotted' => '点线',
            'dashed' => '虚线',
            'double' => '双实线',
        );

        return $select ? $array[$select] : $array;
    }

    public function getStandardSignkey($model)
    {
        Wind::import('SRV:design.bo.PwDesignModelBo');
        $bo = new PwDesignModelBo($model);
        $signKeys = $bo->getSignKeys();
        $standard = $bo->getStandardSign();
        foreach ($standard as &$v) {
            $v = $this->_transformSign($v);
        }

        return $standard;
    }

    /**
     * 获取模型自定义标签
     * Enter description here ...
     *
     * @param string $string
     */
    public function getModelSignkey($string)
    {
        $array = explode(',', $string);
        $signKey = array();
        if (!is_array($array) || count($array) < 1) {
            return array();
        }
        foreach ($array as $v) {
            list($key, $sign) = explode('=', $v);
            $signKey[$key] = $sign;
        }

        return $signKey;
    }

    /**
     * 判断提交的模块模版是否合法
     * Enter description here ...
     *
     * @param string $string
     */
    public function checkTemplate($string)
    {
        //$isModule = $isTpl = true;
        /*if(!preg_match('/\<design\s*id=\"*D_mod_(\d+)\"*\s*role=\"*module\"*\s*[>|\/>]<\/design>/isU', $string, $matches)) {
            $isModule = false;
        }*/
        $forr = $forl = $ifr = $ifl = false;
        if (strpos($string, '<for:') !== false) {
            $forr = true;
        }
        if (strpos($string, '</for>') !== false) {
            $forl = true;
        }

        if (strpos($string, '<if:') !== false) {
            $ifr = true;
        }

        if (strpos($string, '</if>') !== false) {
            $ifl = true;
        }

        if ($forr != $forl) {
            return false;
        }
        if ($ifr != $ifl) {
            return false;
        }

        return true;
    }

    /**
     *	过滤白名单.
     */
    public function filterTemplate($string)
    {
        $string = str_replace('<?', '&lt;?', $string);
        $in = array(
            '/<!--#(.*)#-->/isU',
            '/<!--\{(.*)\}-->/isU',
            /*'/<\?php(.*)\?>/isU',
            '/<\?(.*)\?>/isU',
            '/<\?(.*)/isU',*/
            '/<script(.*)>/isU',
            /*'/<javascript(.*)>/isU',
            '/<vbscript(.*)>/isU',*/
            '/<\/script>/isU',
            /*'/<\/javascript>/isU',
            '/<\/vbscript>/isU',*/
            '/<frame(.*)>/isU',
            '/<\/fram(.*)>/isU',
            '/<iframe(.*)>/isU',
            '/<\/ifram(.*)>/isU',
        );
        $out = array(
            '&lt;!--# \\1 #--&gt',
            '&lt;!--{ \\1 }--&gt',
            /*'&lt;?php\\1?&gt;',
            '&lt;?\\1?&gt;',
            '&lt;&nbsp;?\\1',*/
            '&lt;script\\1&gt;',
            /*'&lt;javascript\\1&gt;',
            '&lt;vbscript\\1&gt;',*/
            '&lt;/script&gt;',
            /*'&lt;/javascript&gt;',
            '&lt;/vbscript&gt;',*/
            '&lt;frame\\1&gt;',
            '&lt;/fram\\1&gt;',
            '&lt;iframe\\1&gt;',
            '&lt;/ifram\\1&gt;',
        );

        return preg_replace($in, $out, $string);
    }

    public function clearCompile()
    {
        WindFolder::rm(Wind::getRealDir('DATA:compile'), true);
        WindFolder::rm(Wind::getRealDir('DATA:design'), true);
    }

    public function clearTemplate($pageid, $tplPath)
    {
        if (!$tplPath) {
            return false;
        }
        $dir = Wind::getRealDir('THEMES:portal.local.').$tplPath;
        WindFolder::rm($dir, true);

        return true;
    }

    /**
     * 自定义页默认模版.
     */
    public function defaultTemplate($pageid, $tplPath)
    {
        $fromDir = Wind::getRealDir('TPL:special.default');
        $toDir = Wind::getRealDir('THEMES:portal.local.'.$tplPath);
        if ($this->copyRecur($fromDir, $toDir)) {
            return true;
        }

        return false;
    }

    /**
     * 递归复制文件夹.
     */
    public function copyRecur($fromFolder, $toFolder)
    {
        $dir = @opendir($fromFolder);
        if (!$dir) {
            return false;
        }
        WindFolder::mk($toFolder);
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($fromFolder.'/'.$file)) {
                    $this->copyRecur($fromFolder.'/'.$file, $toFolder.'/'.$file);
                } else {
                    @copy($fromFolder.'/'.$file, $toFolder.'/'.$file);
                    @chmod($toFolder.'/'.$file, 0777);
                }
            }
        }
        closedir($dir);

        return true;
    }

    private function _transformSign($sign)
    {
        if (!preg_match('/\{(.+)}/isU', $sign, $matches)) {
            return false;
        }

        return $matches[1];
    }
}
