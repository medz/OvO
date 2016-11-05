<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwDesignImportTxt.php 23476 2013-01-10 05:10:23Z gao.wanggao $
 * @package
 */
class PwDesignImportTxt
{
    public $newIds = array();

    private $_pageInfo = array();
    private $_structures = array();
    private $_oldstruct = array();
    private $_content = '';


    public function checkTxt($filename = '', $content = '')
    {
        if (!$filename && !$content) {
            return new PwError('DESIGN:upload.file.error');
        }
        if ($filename) {
            if (!$content = WindFile::read($filename)) {
                return new PwError('DESIGN:upload.file.error');
            }
        }
        $content = preg_replace("/\/\*(.+)\*\//", '', $content);
        $content = unserialize(base64_decode($content));
        $_array = array('page', 'segment', 'structure', 'module');
        foreach ($_array as $v) {
            if (!isset($content[$v])) {
                return new PwError('DESIGN:file.check.fail');
            }
        }
        $this->_content = $content;
        if ($filename) {
            WindFile::del($filename);
        }

        return true;
    }

    public function setPageInfo($pageinfo)
    {
        $this->_pageInfo = $pageinfo;
    }

    public function importTxT()
    {
        $resource = $this->importModule($this->_content['module']);
        if ($resource instanceof PwError) {
            return $resource;
        }
        $resource = $this->importStructure($this->_content['structure']);
        if ($resource instanceof PwError) {
            return $resource;
        }
        $this->importSegment($this->_content['segment']);

        return $this->updatePage();
    }

    //如果失败，回滚操作
    public function rollback()
    {
        $moduleDs = $this->_getModuleDs();
        $structDs = $this->_getStructureDs();
        foreach ($this->newIds as $v) {
            $moduleDs->deleteModule($v);
        }
        foreach ($this->_structures as $v) {
            $structDs->deleteStruct($v);
        }
    }

    protected function importModule($modules)
    {
        $ds = $this->_getModuleDs();
        Wind::import('SRV:design.dm.PwDesignModuleDm');
        foreach ($modules as $k => $v) {
            $dm = new PwDesignModuleDm();
            if (!$v['module_name']) {
                continue;
            }
            $style = unserialize($v['module_style']);
            $dm->setPageId($this->_pageInfo['page_id'])
                ->setFlag($v['model_flag'])
                ->setName($v['module_name'])
                ->setStruct($v['module_struct'])
                ->setProperty(unserialize($v['module_property']))
                ->setCache(unserialize($v['module_cache']))
                ->setTitle(unserialize($v['module_title']))
                ->setStyle($style['font'], $style['link'], $style['border'], $style['margin'], $style['padding'], $style['background'], $style['styleclass'])
                ->setIsused(1)
                ->setModuleType($v['module_type'])
                ->setModuleTpl($v['module_tpl']);
            $resource = $ds->addModule($dm);
            if ($resource instanceof PwError) {
                return $resource;
            }
            $this->newIds[$k] = (int) $resource;
        }

        return true;
    }

    protected function importStructure($structs)
    {
        Wind::import('SRV:design.dm.PwDesignStructureDm');
        foreach ($structs as $k => $v) {
            //TODO structname 唯一性检查
            $name = 'I_'.WindUtility::generateRandStr(6);
            $dm = new PwDesignStructureDm();
            $style = unserialize($v['struct_style']);
            $dm->setStructTitle(unserialize($v['struct_title']))
                ->setStructname($name)
                ->setStructStyle($style['font'], $style['link'], $style['border'], $style['margin'], $style['padding'], $style['background'], $style['styleclass']);
            $resource = $this->_getStructureDs()->replaceStruct($dm);
            if ($resource instanceof PwError) {
                return $resource;
            }
            $this->_structures[] = $name;
            $this->_oldstruct[] = $v['struct_name'];
        }

        return true;
    }

    protected function importSegment($segments)
    {
        Wekit::load('design.PwDesignPage');
        if ($this->_pageInfo['page_type'] == PwDesignPage::PORTAL) {
            return $this->importPortalSegment($segments);
        }
        $_struct = '';
        $_array = explode(',', $this->_pageInfo['segments']);
        if (in_array('first_segment', $_array)) {
            $firstSegment = 'first_segment';
        } else {
            $firstSegment = array_shift($_array);
        }

        foreach ($segments as $k => $v) {
            if (!$v) {
                continue;
            }
            $_struct .= $v;
        }
        $_struct = $this->replaceStruct($_struct);
        $_tpl = $this->_getCompileService()->replaceModule($_struct);
        $this->_getSegmentDs()->replaceSegment($firstSegment, $this->_pageInfo['page_id'], $_tpl, $_struct);

        return true;
    }

    protected function importPortalSegment($segments)
    {
        $srv = $this->_getCompileService();
        $ds = $this->_getSegmentDs();
        //兼容template的怪异编译方式
        /*foreach ($segments AS $k=>$segment) {
            if ($k != 'footer_segment') continue;
            $segments['segment_drag'] .= $segment;
            unset($segments['footer_segment']);
        }*/

        foreach ($segments as $k => $segment) {
            $struct = $this->replaceStruct($segment);
            $_tpl = $srv->replaceModule($struct);
            $ds->replaceSegment($k, $this->_pageInfo['page_id'], $_tpl, $struct);
        }
    }

    protected function updatePage()
    {
        Wind::import('SRV:design.dm.PwDesignPageDm');
        $moduleIds = implode(',',  $this->newIds);
        $moduleIds = $moduleIds ? $moduleIds.','.$this->_pageInfo['module_ids'] : $this->_pageInfo['module_ids'];
        $moduleIds = array_filter(explode(',', $moduleIds));
        $dm = new PwDesignPageDm($this->_pageInfo['page_id']);
        $dm->setModuleIds($moduleIds)
            ->setStrucNames($this->_structures);
        $resource = Wekit::load('design.PwDesignPage')->updatePage($dm);

        return $resource;
    }

    protected function replaceStruct($struct)
    {
        //对新添加的module进行转换
        foreach ($this->newIds as $k => $v) {
            $_in = array(
                'data-id="'.$k.'"',
                'id="J_mod_'.$k.'"',
                'id="D_mod_'.$k.'"',
                //IE fixed
                'data-id='.$k,
                'id=J_mod_'.$k,
                'id=D_mod_'.$k,
            );
            $_out = array(
                'data-id="'.$v.'"',
                'id="J_mod_'.$v.'"',
                'id="D_mod_'.$v.'"',
                'data-id="'.$v.'"',
                'id="J_mod_'.$v.'"',
                'id="D_mod_'.$v.'"',
            );
            $struct = str_replace($_in, $_out, $struct);
        }
        //对新添加的structures进行转换
        foreach ($this->_oldstruct as $k => $v) {
            $_in = array(
                'id="'.$v.'"',
                'role="structure_'.$v.'"',
            );

            $_out = array(
                'id="'.$this->_structures[$k].'"',
                'role="structure_'.$this->_structures[$k].'"',
            );
            $struct = str_replace($_in, $_out, $struct);
        }

        return $struct;
    }

    private function _getCompileService()
    {
        return Wekit::load('design.srv.PwDesignCompile');
    }

    private function _getSegmentDs()
    {
        return Wekit::load('design.PwDesignSegment');
    }

    private function _getStructureDs()
    {
        return Wekit::load('design.PwDesignStructure');
    }

    private function _getBakDs()
    {
        return Wekit::load('design.PwDesignBak');
    }

    private function _getModuleDs()
    {
        return Wekit::load('design.PwDesignModule');
    }
}
