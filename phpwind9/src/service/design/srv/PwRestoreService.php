<?php
/**
 * 设计备份还原服务
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>.
 *
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwRestoreService.php 22964 2013-01-04 05:37:43Z gao.wanggao $
 */
class PwRestoreService
{
    public function doRestoreSnap($pageid)
    {
        //$this->deleteData($pageid);
        $this->restoreModule($pageid, 1);
        //$this->restoreData($pageid, 1);
        $this->restoreStructure($pageid, 1);
        $this->restoreSegment($pageid, 1);
        $this->restorePage($pageid, 1);
        $this->docachePage($pageid);

        return true;
    }

    public function doRestoreBak($pageid)
    {
        //$this->deleteData($pageid);
        $this->restoreModule($pageid);
        //$this->restoreData($pageid);
        $this->restoreStructure($pageid);
        $this->restoreSegment($pageid);
        $this->restorePage($pageid);
        $this->docachePage($pageid);

        return true;
    }

    protected function docachePage($pageid)
    {
        $pageInfo = $this->_getPageDs()->getPage($pageid);
        $ids = explode(',', $pageInfo['module_ids']);

        foreach ($ids as $id) {
            $id = (int) $id;
            if ($id < 1) {
                continue;
            }
            $srv = new PwAutoData($id);
            $srv->addAutoData();
        }
    }

    protected function deleteData($pageid)
    {
        $ds = $this->_getModuleDs();
        $ds->deleteByPageId($pageid);
    }

    protected function restoreModule($pageid, $issnap = 0)
    {
        $bakDs = $this->_getBakDs();
        $conpDs = $this->_getComponentDs();
        $module = $bakDs->getBak(PwDesignBak::MODULE, $pageid, $issnap);
        if (! is_array($module['bak_info'])) {
            return false;
        }
        $ds = $this->_getModuleDs();

        $bo = new PwDesignPageBo($pageid);

        $srv = new PwPortalCompile($bo);

        foreach ($module['bak_info'] as $k => $v) {
            $dm = new PwDesignModuleDm($k);
            $dm->setFlag($v['model_flag'])
            ->setName($v['module_name'])
            ->setCompid($v['module_compid'])
            ->setProperty(unserialize($v['module_property']))
            ->setCache(unserialize($v['module_cache']))
            ->setTitle(unserialize($v['module_title']))
            ->setModuleTpl($v['module_tpl'])
            ->setIsused($v['isused']);
            $style = unserialize($v['module_style']);
            $dm->setStyle($style['font'], $style['link'], $style['border'], $style['margin'], $style['padding'], $style['background'], $style['styleclass']);
            $ds->updateModule($dm);
        }
        $pageInfo = $bo->getPage();
        if ($pageInfo['page_type'] == PwDesignPage::PORTAL) {
            $srv->restoreList($module['bak_info'], 'index');
        }

        return true;
    }

    protected function restoreData($pageid, $issnap = 0)
    {
        $data = $this->_getBakDs()->getBak(PwDesignBak::DATA, $pageid, $issnap);
        if (! is_array($data['bak_info'])) {
            return false;
        }

        $ds = $this->_getDataDs();
        foreach ($data['bak_info'] as $k => $v) {
            $this->_getDataDs()->deleteByModuleId($k);
            list($bold, $underline, $italic, $color) = $v['style'];
            $dm = new PwDesignDataDm();
            $dm->setDatatype($v['data_type'])
                ->setFromType($v['from_type'])
                ->setFromApp($v['from_app'])
                ->setFromid($v['from_id'])
                ->setModuleid($v['module_id'])
                ->setStandard(unserialize($v['standard']))
                ->setStyle($bold, $underline, $italic, $color)
                ->setExtend(unserialize($v['extend_info']))
                ->setVieworder($v['vieworder'])
                ->setStarttime($v['start_time'])
                ->setEndtime($v['end_time']);
            $resource = $ds->addData($dm);
        }

        return true;
    }

    protected function restoreStructure($pageid, $issnap = 0)
    {
        $data = $this->_getBakDs()->getBak(PwDesignBak::STRUCTURE, $pageid, $issnap);
        if (! is_array($data['bak_info'])) {
            return false;
        }

        $ds = $this->_getStructureDs();
        foreach ($data['bak_info'] as $k => $v) {
            $style = unserialize($v['struct_style']);
            $dm = new PwDesignStructureDm($k);
            $dm->setStructName($k)
                ->setStructTitle(unserialize($v['struct_title']))
                ->setStructStyle($style['font'], $style['link'], $style['border'], $style['margin'], $style['padding'], $style['background'], $style['styleclass']);
            $ds->replaceStruct($dm);
        }

        return true;
    }

    protected function restoreSegment($pageid, $issnap = 0)
    {
        $segments = $this->_getBakDs()->getBak(PwDesignBak::SEGMENT, $pageid, $issnap);
        $ds = $this->_getSegmentDs();
        $srv = null;

        $bo = new PwDesignPageBo($pageid);
        $pageInfo = $bo->getPage();
        if ($pageInfo['page_type'] == PwDesignPage::SYSTEM) {
            $srv = new PwPortalCompile($bo);
        }
        foreach ($segments['bak_info'] as $k => $v) {
            $ds->replaceSegment($k, $pageid, $v['segment_tpl']);
            $strr = substr(strrchr($k, '__'), 1);
            if (isset($srv) && $strr == 'tpl') {
                $file = substr($k, 0, strlen($k) - 5);
                $srv->restoreTpl($file, $v['segment_struct']); //门户片段
            }
        }

        return true;
    }

    protected function restorePage($pageid, $issnap = 0)
    {
        $page = $this->_getBakDs()->getBak(PwDesignBak::PAGE, $pageid, $issnap);

        $dm = new PwDesignPageDm($pageid);
        $dm->setName($page['bak_info']['page_name'])
            ->setType($page['bak_info']['page_type'])
            ->setRouter($page['bak_info']['page_router'])
            ->setUnique($page['bak_info']['page_unique'])
            ->setModuleIds(explode(',', $page['bak_info']['module_ids']))
            ->setStrucNames(explode(',', $page['bak_info']['struct_names']))
            ->setSegments(explode(',', $page['bak_info']['segments']));

        return $this->_getPageDs()->updatePage($dm);
    }

    private function _getDataDs()
    {
        return Wekit::load('design.PwDesignData');
    }

    private function _getBakDs()
    {
        return Wekit::load('design.PwDesignBak');
    }

    private function _getModuleDs()
    {
        return Wekit::load('design.PwDesignModule');
    }

    private function _getStructureDs()
    {
        return Wekit::load('design.PwDesignStructure');
    }

    private function _getPageDs()
    {
        return Wekit::load('design.PwDesignPage');
    }

    private function _getSegmentDs()
    {
        return Wekit::load('design.PwDesignSegment');
    }

    private function _getComponentDs()
    {
        return Wekit::load('design.PwDesignComponent');
    }

    private function _getPushDs()
    {
        return Wekit::load('design.PwDesignPush');
    }
}
