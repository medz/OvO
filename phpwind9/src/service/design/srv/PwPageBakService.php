<?php

/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>.
 *
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwPageBakService.php 22555 2012-12-25 08:37:31Z gao.wanggao $
 */
class PwPageBakService
{
    protected $pageInfo = [];
    protected $pageid = 0;

    /**
     * 建立上一个版本备份
     * Enter description here ...
     *
     * @param int $pageid
     */
    public function doBak($pageid)
    {
        $ds = $this->_getBakDs();
        $type = [PwDesignBak::SEGMENT, PwDesignBak::MODULE, PwDesignBak::PAGE, PwDesignBak::STRUCTURE];
        foreach ($type as $v) {
            $ds->deleteBak($v, $pageid, 0);
            $ds->updateSnap($v, $pageid, 1, 0);
        }

        return true;
    }

    /**
     * 建立备份快照
     * Enter description here ...
     *
     * @param int $pageid
     */
    public function doSnap($pageid)
    {
        $this->pageid = $pageid;
        $this->_setPageInfo();
        $this->bakPage();
        $this->bakSegment();
        $this->bakStructure();
        $this->bakModule();
        //$this->bakData();
    }

    protected function bakPage()
    {
        $this->_getBakDs()->replaceBak(PwDesignBak::PAGE, $this->pageid, 1, $this->pageInfo);
    }

    protected function bakSegment()
    {
        $info = $this->_getSegmentDs()->getSegmentByPageid($this->pageid);
        $this->_getBakDs()->replaceBak(PwDesignBak::SEGMENT, $this->pageid, 1, $info);
    }

    protected function bakStructure()
    {
        $names = explode(',', $this->pageInfo['struct_names']);
        $info = $this->_getStructureDs()->fetchStruct($names);
        if (!$info) {
            foreach ($names as $name) {
                $info[$name] = [];
            }
        }
        $this->_getBakDs()->replaceBak(PwDesignBak::STRUCTURE, $this->pageid, 1, $info);
    }

    protected function bakModule()
    {
        $info = $this->_getModuleDs()->getByPageid($this->pageid);
        $this->_getBakDs()->replaceBak(PwDesignBak::MODULE, $this->pageid, 1, $info);
    }

    protected function bakData()
    {
        $moduleids = $this->pageInfo['module_ids'] ? explode(',', $this->pageInfo['module_ids']) : [];
        $data = $this->_getDataDs()->fetchDataByModuleid($moduleids);
        $this->_getBakDs()->replaceBak(PwDesignBak::DATA, $this->pageid, 1, $data);
    }

    private function _setPageInfo()
    {
        $this->pageInfo = Wekit::load('design.PwDesignPage')->getPage($this->pageid);
    }

    private function _getModuleDs()
    {
        return Wekit::load('design.PwDesignModule');
    }

    private function _getStructureDs()
    {
        return Wekit::load('design.PwDesignStructure');
    }

    private function _getSegmentDs()
    {
        return Wekit::load('design.PwDesignSegment');
    }

    private function _getBakDs()
    {
        return Wekit::load('design.PwDesignBak');
    }

    private function _getDataDs()
    {
        return Wekit::load('design.PwDesignData');
    }
}
