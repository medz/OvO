<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwDesignImportZip.php 24904 2013-02-26 04:01:46Z gao.wanggao $
 * @package
 */
class PwDesignImportZip
{
    public $newIds = array();

    protected $themesPath = '';
    protected $pageid = 0;
    protected $tplPath = '';

    private $_files = array();
    private $_tplExt = '.htm';

    public function __construct($pageBo)
    {
        $this->pageid = $pageBo->pageid;
        $this->themesPath = Wind::getRealDir('THEMES:portal.local.');
        $this->tplPath = $this->themesPath.$pageBo->getTplPath();
    }

    /**
     * 检查目录
     * Enter description here ...
     */
    public function checkDirectory()
    {
        if (is_writable($this->themesPath)) {
            return true;
        }

        return false;
    }

    /**
     * 检查并导入zip文件
     * Enter description here ...
     * @param string $filename
     */
    public function checkZip($filename)
    {
        Wind::import('LIB:utility.PwZip');
        $config = array();
        $_isTpl = false;
        $extension = array('htm', 'js', 'gif', 'jpg', 'jpeg', 'txt', 'png', 'css', 'xml');
        $zip = new PwZip();
        $xml = new WindXmlParser('1.0', Wekit::app()->charset);
        if (!$fileData = $zip->extract($filename)) {
            return new PwError('DESIGN:upload.file.error');
        }
        foreach ($fileData as &$file) {
            $file['filename'] = str_replace('\\', '/', $file['filename']);
            $pos = strpos($file['filename'], '/');
            $lenth = strlen($file['filename']);
            $file['filename'] = substr($file['filename'], (int) $pos + 1, $lenth - $pos);
            if (strtolower($file['filename']) == 'manifest.xml') {
                $config = $xml->parseXmlStream($file['data'], 0);
            }
            //过滤文件类型
            $ext = strtolower(substr(strrchr($file['filename'], '.'), 1));
            if (!in_array($ext, $extension)) {
                unset($file);
                continue;
            }

            //过滤中文文件名
            if (preg_match('/^[\x7f-\xff]+$/', $file['filename'])) {
                unset($file);
                continue;
            }

            //导入模块数据并记录新ID
            if ($file['filename'] == 'module/data.txt') {
                $this->importTxt($file['data']);
                unset($file);
            }
        }
        //if (!$config) return new PwError("DESIGN:file.check.fail");

        foreach ($fileData as &$_file) {
            $ext = strrchr($_file['filename'], '.');
            if ($ext != $this->_tplExt) {
                continue;
            }
            $_file['data'] = $this->filterTemplate($_file['data']);
            $_file['data'] = $this->replaceTpl($_file['data']);
            $_file['data'] = $this->compileStyle($_file['data']);
            if ($_file['data']) {
                $_isTpl = true;
            }
        }
        WindFile::del($filename);
        //TODO 版本号验证
        if (!$fileData) {
            return new PwError('DESIGN:file.check.fail');
        }
        if (!$_isTpl) {
            return new PwError('DESIGN:file.check.fail');
        }
        if (!$this->writeFile($fileData)) {
            return true;
        }

        return false;
    }

    /**
     * 导入应用中心模版
     * Enter description here ...
     * @param string $folder
     */
    public function appcenterToLocal($folder)
    {
        if (!$folder) {
            return false;
        }
        $appPath = Wind::getRealDir('THEMES:portal.appcenter.'.$folder.'.');
        $fileData = $this->read($appPath);
        $ifTpl = false;
        foreach ($fileData as &$file) {
            if ($file['filename'] == $appPath.'module/data.txt') {
                $this->importTxt($file['data']);
                unset($file);
            }
        }
        foreach ($fileData as &$_file) {
            $_file['filename'] = str_replace($appPath, '', $_file['filename']);
            $ext = strrchr($_file['filename'], '.');
            if ($ext != $this->_tplExt) {
                continue;
            }
            $_file['data'] = $this->replaceTpl($_file['data'], $config);
            $_file['data'] = $this->compileStyle($_file['data']);
            $ifTpl = true;
        }
        if (!$ifTpl) {
            return false;
        }
        if (!$this->writeFile($fileData)) {
            return true;
        }

        return false;
    }


    protected function replaceTpl($section)
    {
        Wind::import('SRV:design.dm.PwDesignModuleDm');
        $ds = Wekit::load('design.PwDesignModule');
        if (preg_match_all('/\<pw-list\s*id=\"(\d+)\"\s*>/isU', $section, $matches)) {
            foreach ($matches[1] as $k => $v) {
                if (isset($this->newIds[$v])) {
                    $section = str_replace($matches[0][$k], '<pw-list id="'.$this->newIds[$v].'">', $section);
                } else {
                    $section = str_replace($matches[0][$k], '<pw-list>', $section);
                }
            }
        }

        return $section;
    }

    /**
     * 替换frame标签
     */
    protected function filterTemplate($string)
    {
        $string = str_replace('<?', '&lt;?', $string);
        $in = array(
            '/<!--#(.*)#-->/isU',
            '/<!--\{(.*)\}-->/isU',
            '/<frame(.*)>/isU',
            '/<\/fram(.*)>/isU',
            '/<iframe(.*)>/isU',
            '/<\/ifram(.*)>/isU',
        );
        $out = array(
            '&lt;!--# \\1 #--&gt',
            '&lt;!--{ \\1  }--&gt',
            '&lt;frame\\1&gt;',
            '&lt;/fram\\1&gt;',
            '&lt;iframe\\1&gt;',
            '&lt;/ifram\\1&gt;',
        );

        return preg_replace($in, $out, $string);
    }

    /**
     * 给style.css 加个随机码
     * Enter description here ...
     * @param unknown_type $section
     */
    protected function compileStyle($section)
    {
        $in = '{@G:design.url.css}/style.css';
        $out = '{@G:design.url.css}/style.css?rand='.Pw::getTime();

        return str_replace($in, $out, $section);
    }

    protected function importTxt($content)
    {
        $srv = Wekit::load('design.srv.PwDesignImportTxt');
        $pageInfo = $this->_getPageDs()->getPage($this->pageid);
        $srv = new PwDesignImportTxt();
        $srv->setPageInfo($pageInfo);
        $resource = $srv->checkTxt('', $content);
        if ($resource instanceof PwError) {
            return false;
        }
        $resource = $srv->importTxt();
        $this->newIds = $srv->newIds;
    }

    protected function writeFile($fileData)
    {
        $failArray = array();
        $dir = $this->tplPath;
        WindFolder::rm($dir, true);
        WindFolder::mk($dir);
        foreach ($fileData as $file) {
            WindFolder::mkRecur($dir.'/'.dirname($file['filename']));
            if (!WindFile::write($dir.'/'.$file['filename'], $file['data'])) {
                $failArray[] = $file['filename'];
            }
        }

        return $failArray;
    }

    protected function read($dir)
    {
        if (!is_dir($dir)) {
            return array();
        }
        if (!$handle = @opendir($dir)) {
            return array();
        }
        while (false !== ($file = @readdir($handle))) {
            if ('.' === $file || '..' === $file) {
                continue;
            }
            $fileName = $dir.$file;
            if (is_file($fileName)) {
                if (!$_handle = fopen($fileName, 'rb')) {
                    continue;
                }
                $data = '';
                while (!feof($_handle)) {
                    $data .= fgets($_handle, 4096);
                }
                fclose($_handle);
                $this->_files[] = array('filename' => $fileName, 'data' => $data);
            } elseif (is_dir($fileName)) {
                $this->read($fileName.'/');
            }
        }
        unset($data);
        @closedir($handle);

        return $this->_files;
    }

    private function _getPageDs()
    {
        return Wekit::load('design.PwDesignPage');
    }
}
