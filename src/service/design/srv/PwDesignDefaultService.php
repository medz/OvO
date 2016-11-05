<?php
/**
 * 默认数据服务
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwDesignDefaultService.php 25611 2013-03-20 02:20:51Z gao.wanggao $
 * @package
 */
class PwDesignDefaultService
{
    public function reviseDefaultData()
    {
        $ds = Wekit::load('design.PwDesignData');
        Wind::import('SRV:design.srv.vo.PwDesignDataSo');
        Wind::import('SRV:design.dm.PwDesignDataDm');
        $vo = new PwDesignDataSo();
        $list = $ds->searchData($vo);
        foreach ($list as $k => $v) {
            $v['extend_info'] = unserialize($v['extend_info']);
            foreach ($v['extend_info'] as &$_v) {
                $_v = str_replace('install.php', 'index.php', $_v);
            }
            $dm = new PwDesignDataDm($k);
            $dm->setExtend($v['extend_info']);
            $ds->updateData($dm);
        }

        return true;
    }

    public function likeModule()
    {
        $ds = $this->_getPageDs();
        Wind::import('SRV:design.dm.PwDesignPageDm');
        $dm = new PwDesignPageDm();
        $dm->setType(PwDesignPage::SYSTEM)
            ->setName('热门喜欢')
            ->setRouter('like/like/run')
            ->setSegments(array('likedemo'));
        $pageid = $ds->addPage($dm);
        if ($pageid instanceof PwError) {
            return false;
        }

        Wind::import('SRV:design.srv.vo.PwDesignComponentSo');
        $vo = new PwDesignComponentSo();
        $vo->setCompname('瀑布流 ');
        $comp = Wekit::load('design.PwDesignComponent')->searchComponent($vo);
        if (!$comp) {
            return false;
        }
        $comp = array_shift($comp);
        $tpl = $comp['comp_tpl'];
        $property = array('ispic' => 1, 'desnum' => 144, 'order' => 5, 'limit' => 100, 'timefmt' => 'm-d');
        $cache = array('expired' => 0, 'start_hour' => 0, 'start_minute' => 0, 'end_hour' => 0, 'end_minute' => 0);
        Wind::import('SRV:design.dm.PwDesignModuleDm');
        $moduleDm = new PwDesignModuleDm();
        $moduleDm->setFlag('thread')
            ->setPageId($pageid)
            ->setName('演示：热门喜欢')
            ->setModuleTpl($tpl)
            ->setProperty($property)
            ->setCache($cache)
            ->setCompid($comp['comp_id'])
            ->setIsused(1);
        $moduleid = Wekit::load('design.PwDesignModule')->addModule($moduleDm);
        if ($moduleid instanceof PwError) {
            return false;
        }

        $dm = new PwDesignPageDm($pageid);
        $dm->setModuleIds(array($moduleid));
        $ds->updatePage($dm);

        $rand = WindUtility::generateRandStr(8);
        $tpl = <<<TPL
<div id="$rand" class="design_layout_style J_mod_layout box_no" role="structure_$rand" data-lcm="100">			
<h2 class="design_layout_hd cc J_layout_hd" role="titlebar"></h2>			
<div id="J_mod_$moduleid" class="design_layout_ct mod_box J_mod_box" data-id="$moduleid">
<design id="D_mod_$moduleid" role="module">
</design>
</div>
</div>
TPL;
        Wekit::load('design.PwDesignSegment')->replaceSegment('likedemo', $pageid, $tpl);
        Wind::import('SRV:design.srv.data.PwAutoData');
        $srv = new PwAutoData($moduleid);
        $srv->addAutoData();

        return true;
    }


    public function tagModule()
    {
        $ds = $this->_getPageDs();
        Wind::import('SRV:design.dm.PwDesignPageDm');
        $dm = new PwDesignPageDm();
        $dm->setType(PwDesignPage::SYSTEM)
            ->setName('话题')
            ->setRouter('tag/index/run')
            ->setSegments(array('huatidemo'));
        $pageid = $ds->addPage($dm);
        if ($pageid instanceof PwError) {
            return false;
        }

        Wind::import('SRV:design.srv.vo.PwDesignComponentSo');
        $vo = new PwDesignComponentSo();
        $vo->setCompname('话题封面 [ 间隔 ]');
        $comp = Wekit::load('design.PwDesignComponent')->searchComponent($vo);
        if (!$comp) {
            return false;
        }
        $comp = array_shift($comp);
        $tpl = $comp['comp_tpl'];
        $property = array('islogo' => 1, 'order' => 5, 'limit' => 18, 'timefmt' => 'm-d');
        $cache = array('expired' => 15, 'start_hour' => 0, 'start_minute' => 0, 'end_hour' => 0, 'end_minute' => 0);
        Wind::import('SRV:design.dm.PwDesignModuleDm');
        $moduleDm = new PwDesignModuleDm();
        $moduleDm->setFlag('tag')
            ->setPageId($pageid)
            ->setName('演示：热门话题')
            ->setModuleTpl($tpl)
            ->setProperty($property)
            ->setCache($cache)
            ->setCompid($comp['comp_id'])
            ->setIsused(1);
        $moduleid = Wekit::load('design.PwDesignModule')->addModule($moduleDm);
        if ($moduleid instanceof PwError) {
            return false;
        }
        $dm = new PwDesignPageDm($pageid);
        $dm->setModuleIds(array($moduleid));
        $ds->updatePage($dm);
        $rand = WindUtility::generateRandStr(8);
        $tpl = <<<TPL
<div id="$rand" class="design_layout_style J_mod_layout box_no" role="structure_$rand" data-lcm="100">			
<h2 class="design_layout_hd cc J_layout_hd" role="titlebar"></h2>			
<div id="J_mod_$moduleid" class="design_layout_ct mod_box J_mod_box" data-id="$moduleid">
<design id="D_mod_$moduleid" role="module">
</design>
</div>
</div>
TPL;
        Wekit::load('design.PwDesignSegment')->replaceSegment('huatidemo', $pageid, $tpl);
        Wind::import('SRV:design.srv.data.PwAutoData');
        $srv = new PwAutoData($moduleid);
        $srv->addAutoData();

        return true;
    }


    private function _getBakService()
    {
        return Wekit::load('design.srv.PwPageBakService');
    }


    private function _getPageDs()
    {
        return Wekit::load('design.PwDesignPage');
    }
}
