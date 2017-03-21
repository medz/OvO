<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>.
 *
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwPortalCompile.php 24221 2013-01-23 04:05:43Z gao.wanggao $
 */
class PwPortalCompile
{
    private $dir = '';
    private $commonDir = '';
    private $pageid = 0;
    private $isCompile = false;

    public function __construct($pageBo)
    {
        $this->pageid = $pageBo->pageid;
        $dir = Wind::getRealDir('THEMES:portal.local.');
        $this->dir = $dir.$pageBo->getTplPath().'/template/';
        $this->commonDir = $dir.'common/template/';
    }

    public function doCompile($content)
    {
        $content = $this->compilePw($content);
        $content = $this->compileDrag($content);
        $content = $this->compileList($content);

        return $this->compileTitle($content);
    }

    /**
     * 生成自定义页模版
     * Enter description here ...
     *
     * @param $compileStr
     */
    public function compilePortal($compileStr)
    {
        $file = $this->dir.'index.htm';
        $content = $this->read($file);
        $content = $this->compileDesign($content);

        if ($content === false) {
            $compileStr;
        }
        /*if (preg_match('/\<pw-start\/>(.+)<pw-end\/>/isU', $content, $matches)) {
            if ($matches[0]) {
                $compileStr = $matches[0];
            }
        }*/
        //$content = preg_replace('/\<pw-start\/>(.+)<pw-end\/>/isU', $compileStr, $content);
        if ($this->isCompile) {
            $this->write($content, $file);
        }

        return $compileStr;
    }

    /**
     * 对标签进行编译
     * Enter description here ...
     *
     * @param unknown_type $content
     */
    public function compileDesign($content, $segment = '')
    {
        $this->isCompile = false;
        $content = $this->compilePw($content);
        $content = $this->updateTitle($content);
        $content = $this->updateList($content);
        $content = $this->compileDrag($content);
        $content = $this->compileList($content, $segment);

        return $this->compileTitle($content, $segment);
    }

    /**
     * 对pw-tpl标签进行编译
     * Enter description here ...
     *
     * @param unknown_type $tplId
     */
    public function compileTpl($section, $compile = false)
    {
        if (preg_match_all('/\<pw-tpl\s*id=\"([\w.]+)\"\s*\/>/isU', $section, $matches)) {
            $ds = Wekit::load('design.PwDesignSegment');
            foreach ($matches[1] as $k => $matche) {
                if (! $matche) {
                    continue;
                }
                list($common, $tpl) = explode('.', $matche, 2);
                //解析pw-tpl id="common.segment"  如果模版目录内文件不存在,使用公共的
                if ($common == 'common') {
                    $file = $this->dir.$tpl.'.htm';
                    $v = $tpl;
                    $dir = $this->dir;
                    if (! WindFile::isFile($file)) {
                        $v = $tpl;
                        $dir = $this->commonDir;
                    }
                } else {
                    $v = $matche;
                    $dir = $this->dir;
                }

                $file = $dir.$v.'.htm';
                if (! WindFile::isFile($file)) {
                    WindFolder::mkRecur($dir);
                    $isAble = $this->_checkRealWriteAble($dir);
                    if (! $isAble) {
                        return $section;
                    }
                    WindFolder::mkRecur(dirname($dir).'/images/');
                    WindFolder::mkRecur(dirname($dir).'/css/');
                    $this->write('<pw-drag id="'.$v.'"/>', $file);
                }

                $xmlFile = dirname($dir).'/Manifest.xml';
                if (! WindFile::isFile($xmlFile)) {
                    $fromFile = Wind::getRealDir('TPL:special.default.').'Manifest.xml';
                    @copy($fromFile, $xmlFile);
                    @chmod($xmlFile, 0777);
                }
                $content = $this->read($file);
                if ($compile) {
                    $content = $this->compileDesign($content, $v);
                    $ds->replaceSegment($v.'__tpl', $this->pageid, '', $content);
                    if ($this->isCompile) {
                        $this->write($content, $file);
                    }
                }
                $section = str_replace($matches[0][$k], $content, $section);
            }
        }

        return $section;
    }

    /**
     * 修改模块
     * Enter description here ...
     *
     * @param int    $id
     * @param string $repace
     */
    public function replaceList($id, $repace, $tpl = 'index')
    {
        if (! $tpl) {
            return false;
        }
        $file = $this->dir.$tpl.'.htm';
        if (! WindFile::isFile($file)) {
            $file = $this->commonDir.$tpl.'.htm';
        }
        $content = $this->read($file);
        if (preg_match_all('/\<pw-list\s*id=\"(\d+)\"\s*[>|\/>](.+)<\/pw-list>/isU', $content, $matches)) {
            foreach ($matches[1] as $k => $v) {
                if ($v != $id) {
                    continue;
                }
                $_html = '<pw-list id="'.$id.'">'.$repace.'</pw-list>';
                $content = str_replace($matches[0][$k], $_html, $content);
            }
        }
        $this->write($content, $file);
    }

    /**
     * 修改主标题
     * Enter description here ...
     *
     * @param string $name
     * @param string $repace
     */
    public function replaceTitle($name, $repace, $tpl = 'index')
    {
        if (! $tpl) {
            return false;
        }
        $file = $this->dir.$tpl.'.htm';
        if (! WindFile::isFile($file)) {
            $file = $this->commonDir.$tpl.'.htm';
        }
        $content = $this->read($file);
        if (preg_match_all('/\<pw-title\s*id=\"(\w+)\"\s*[>|\/>](.+)<\/pw-title>/isU', $content, $matches)) {
            foreach ($matches[1] as $k => $v) {
                if ($v != $name) {
                    continue;
                }
                $_html = '<pw-title id="'.$name.'">'.$repace.'</pw-title>';
                $content = str_replace($matches[0][$k], $_html, $content);
            }
        }
        $this->write($content, $file);
    }

    public function restoreTpl($file, $content)
    {
        list($common, $tpl) = explode('.', $file, 2);
        if ($common == 'common' || $tpl != '') {
            $file = $this->commonDir.$tpl.'.htm';
        } else {
            $file = $this->dir.$file.'.htm';
        }

        return $this->write($content, $file);
    }

    /**
     * 导入模块还原
     */
    public function restoreList($bakData, $file = 'index')
    {
        $file = $this->dir.$file.'.htm';
        $content = $this->read($file);
        if (preg_match_all('/\<pw-list\s*id=\"(\d+)\"\s*[>|\/>](.+)<\/pw-list>/isU', $content, $matches)) {
            foreach ($matches[1] as $k => $v) {
                if (! isset($bakData[$v])) {
                    continue;
                }
                $repace = $bakData[$v]['module_tpl'] ? $bakData[$v]['module_tpl'] : '';
                $_html = '<pw-list id="'.$v.'">'.$repace.'</pw-list>';
                $content = str_replace($matches[0][$k], $_html, $content);
            }
        }

        return $this->write($content, $file);
    }

    protected function compilePw($section)
    {
        $in = [
            '<pw-start>',
            '<pw-head>',
            '<pw-navigate>',
            '<pw-footer>',
            '<pw-end>',
            '<pw-drag>',
        ];
        $out = [
            '<pw-start/>',
            '<pw-head/>',
            '<pw-navigate/>',
            '<pw-footer/>',
            '<pw-end/>',
            '<pw-drag/>',
        ];

        return str_replace($in, $out, $section);
    }

    protected function updateTitle($section)
    {
        if (preg_match_all('/\<pw-title\s*id=\"(\w+)\"\s*[>|\/>](.+)<\/pw-title>/isU', $section, $matches)) {
            $ds = Wekit::load('design.PwDesignStructure');
            foreach ($matches[1] as $k => $v) {
                $dm = new PwDesignStructureDm();
                $dm->setStructTitle($matches[2][$k])
                    ->setStructName($v);
                $ds->editStruct($dm);
            }
        }

        return $section;
    }

    protected function updateList($section)
    {
        if (preg_match_all('/\<pw-list\s*id=\"(\d+)\"\s*[>|\/>](.+)<\/pw-list>/isU', $section, $matches)) {
            $ds = Wekit::load('design.PwDesignModule');
            foreach ($matches[1] as $k => $v) {
                //$limit = $this->compileFor($matches[2][$k]);
                $dm = new PwDesignModuleDm($v);
                $dm->setModuleTpl($matches[2][$k]);
                 // ->setProperty(array('limit' => $limit));
                $ds->updateModule($dm);
            }
        }

        return $section;
    }

    protected function compileList($section, $segment = '')
    {
        $ds = Wekit::load('design.PwDesignModule');
        if (preg_match_all('/\<pw-list[>|\/>](.+)<\/pw-list>/isU', $section, $matches)) {
            foreach ($matches[1] as $k => $v) {
                $v = str_replace('	', '', trim($v));
                $limit = $this->compileFor($v);
                $name = 'section_'.$this->getRand(6);
                $dm = new PwDesignModuleDm();
                $dm->setPageId($this->pageid)
                    ->setSegment($segment)
                    ->setFlag('thread')
                    ->setName($name)
                    ->setModuleTpl($v)
                    ->setModuleType(PwDesignModule::TYPE_IMPORT)
                    ->setIsused(1)
                    ->setProperty(['limit' => $limit]);
                $moduleId = $ds->addModule($dm);
                if ($moduleId instanceof PwError) {
                    continue;
                }
                $_html = '<pw-list id="'.$moduleId.'">\\1</pw-list>';
                $section = preg_replace('/\<pw-list[>|\/>](.+)<\/pw-list>/isU', $_html, $section, 1);
            }
            $this->isCompile = true;
        }

        return $section;
    }

    protected function compileTitle($section, $segment = '')
    {
        $ds = Wekit::load('design.PwDesignStructure');
        if (preg_match_all('/\<pw-title[>|\/>](.+)<\/pw-title>/isU', $section, $matches)) {
            foreach ($matches[1] as $k => $v) {
                $v = trim($v);
                $name = 'T_'.$this->getRand(6);
                $dm = new PwDesignStructureDm();
                $dm->setStructTitle($v)
                    ->setStructName($name)
                    ->setSegment($segment);
                $resource = $ds->replaceStruct($dm);
                if ($resource instanceof PwError) {
                    continue;
                }
                $_html = '<pw-title id="'.$name.'">\\1</pw-title>';
                //$section = str_replace($matches[0][$k], $_html, $section);
                $section = preg_replace('/\<pw-title[>|\/>](.+)<\/pw-title>/isU', $_html, $section, 1);
            }
            $this->isCompile = true;
        }

        return $section;
    }

    protected function compileDrag($section)
    {
        if (preg_match_all('/\<pw-drag\/>/isU', $section, $matches)) {
            foreach ($matches[0] as $k => $v) {
                $_html = '<pw-drag id="'.$this->getRand(8).'"/>';
                $section = preg_replace('/\<pw-drag\/>/isU', $_html, $section, 1);
            }
            $this->isCompile = true;
        }

        return $section;
    }

    /**
     * 对<for:1>进行解析
     * Enter description here ...
     */
    protected function compileFor($section)
    {
        $limit = 0;
        if (preg_match('/\<for:(\d+)>/isU', $section, $matches)) {
            $limit = (int) $matches[1];
        }

        return $limit;
    }

    protected function getRand($length)
    {
        $mt_string = 'qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM';
        $randstr = '';
        for ($i = 0; $i < $length; $i++) {
            $randstr .= $mt_string[mt_rand(0, 52)];
        }

        return $randstr;
    }

    protected function write($content, $file)
    {
        return WindFile::write($file, $content);
    }

    protected function read($file)
    {
        return WindFile::read($file);
    }

    private function _checkRealWriteAble($pathfile)
    {
        if (! $pathfile) {
            return false;
        }
        $isDir = substr($pathfile, -1) == '/' ? true : false;
        if ($isDir) {
            if (is_dir($pathfile)) {
                mt_srand((float) microtime() * 1000000);
                $pathfile = $pathfile.'pw_'.uniqid(mt_rand()).'.tmp';
            } elseif (@mkdir($pathfile)) {
                return $this->_checkWriteAble($pathfile);
            } else {
                return false;
            }
        }
        @chmod($pathfile, 0777);
        $fp = @fopen($pathfile, 'ab');
        if ($fp === false) {
            return false;
        }
        fclose($fp);
        $isDir && @unlink($pathfile);

        return true;
    }
}
