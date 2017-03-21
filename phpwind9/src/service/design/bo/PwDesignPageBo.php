<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>.
 *
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwDesignPageBo.php 22471 2012-12-24 12:06:23Z gao.wanggao $
 */
class PwDesignPageBo
{
    public $pageid;
    private $_module_pre = 'J_mod_';
    private $_pageInfo = [];
    private $_cronMeduleId = [];

    public function __construct($pageid = null)
    {
        if (isset($pageid)) {
            $this->pageid = (int) $pageid;
            $this->_setPageInfo();
        }
    }

    public function setPageInfo($pageid)
    {
        $this->pageid = (int) $pageid;
        $this->_setPageInfo();
    }

    public function getPage()
    {
        return $this->_pageInfo;
    }

    public function getTplPath()
    {
        if ($this->_pageInfo['page_router'] == 'special/index/run') {
            return 'special_'.$this->_pageInfo['page_unique'];
        } elseif ($this->_pageInfo['is_unique']) {
            return str_replace('/', '_', $this->_pageInfo['page_router']).'_'.$this->_pageInfo['page_unique'];
        } else {
            return str_replace('/', '_', $this->_pageInfo['page_router']);
        }
    }

    public function getPageModules()
    {
        return $this->_pageInfo['module_ids'] ? explode(',', $this->_pageInfo['module_ids']) : [];
    }

    public function getPageId($router, $pageName = '', $uniqueId = 0)
    {
        $isUniqueid = (int) $uniqueId;
        $pageId = 0;
        $ds = $this->_getPageDs();
        $pageList = $ds->getPageByRouter($router);
        foreach ($pageList as $v) {
            if ($v['is_unique'] && $v['is_unique'] == $isUniqueid) {
                $pageId = $v['page_id'];
                break;
            }
            if (! $v['is_unique']) {
                $pageId = $v['page_id'];
            }
        }
        if ($pageId < 1) {
            $dm = new PwDesignPageDm();
            $dm->setName($pageName)
                ->setRouter($router)
                ->setUnique($uniqueId)
                ->setType(PwDesignPage::NORMAL);
            $pageName && $dm->setType(PwDesignPage::SYSTEM);

            if ($router == 'special/index/run') {
                $portal = Wekit::load('design.PwDesignPortal')->getPortal($uniqueId);
                $dm->setName($portal['title'])
                ->setType(PwDesignPage::PORTAL)
                ->setIsUnique($isUniqueid);
            }
            $pageId = $ds->addPage($dm);
            if ($pageId instanceof PwError) {
                return false;
            }
            //自定义页面复制默认模版
            if ($router == 'special/index/run') {
                $tplPath = 'special_'.$uniqueId;
                $srv = Wekit::load('design.srv.PwDesignService');
                $result = $srv->defaultTemplate($pageId, $tplPath);
                if ($result) {
                    $dm = new PwDesignPortalDm($portal['id']);
                    $dm->setTemplate($tplPath);
                    Wekit::load('design.PwDesignPortal')->updatePortal($dm);
                }
            }
        }

        return $pageId;
    }

    public function getLock()
    {
        list($uid, $time) = explode('|', $this->_pageInfo['design_lock']);
        if (Pw::getTime() - (int) $time < 60) {
            $user = Wekit::getLoginUser();
            if ($user->uid != $uid) {
                return true;
            }
        }

        return false;
    }

    public function getPageCss()
    {
        $css = '';
        $array = array_merge($this->_getStructureCss(), $this->_getModuleCss());
        foreach ($array as $k => $v) {
            if (! $v[1]) {
                continue;
            }
            $css .= "\r\n".' #'.$k.'{'.$v.'}';
        }

        return '<style type="text/css">'.$css."\r\n</style>";
    }

    public function getDataByModules($moduleids = [])
    {
        foreach ($moduleids as &$_moduleid) {
            $_moduleid = (int) $_moduleid;
        }
        $ds = $this->_getDataDs();
        $time = Pw::getTime();
        $orderData = $delDataid = $cronMeduleId = $_data = [];
        $data = $ds->fetchDataByModuleid($moduleids);
        foreach ($data as $v) {
            $key = $this->_module_pre.$v['module_id'];
            $orderData[$key][] = $v['data_id'];
            list($bold, $underline, $italic, $color) = explode('|', $v['style']);

            //对预约数据进行处理
            if ($v['is_reservation']) {
                if ($v['start_time'] <= $time) {
                    if ($v['data_type'] == PwDesignData::ISFIXED) {
                        $this->_getPushDs()->updateAutoByModuleAndOrder($v['module_id'], $v['vieworder']);
                    }
                    if (! in_array($v['module_id'], $cronMeduleId)) {
                        $cronMeduleId[] = $v['module_id'];
                    }
                }
                continue;
            }

            $_tmp = unserialize($v['extend_info']);
            $_tmp['__style'] = $this->_formatStyle($bold, $underline, $italic, $color);

            //到期数据处理
            if ($v['end_time'] > 0 && $v['end_time'] < $time) {
                if (! in_array($v['module_id'], $cronMeduleId)) {
                    $cronMeduleId[] = $v['module_id'];
                }
            }
            $_data[$key][] = $_tmp;
        }
        $this->updateDesignCron($cronMeduleId);

        return $_data;
    }

    public function updateDesignCron($moduleids)
    {
        if (! $moduleids) {
            return false;
        }
        $diff = $_data = [];
        $time = Pw::getTime();
        $ds = Wekit::load('design.PwDesignCron');
        $crons = $ds->fetchCron($moduleids);
        $_moduleids = array_keys($crons);
        foreach ($moduleids as $id) {
            if (! in_array($id, $_moduleids)) {
                $diff[] = $id;
            }
        }
        if (! $diff) {
            return false;
        }
        foreach ($diff as $v) {
            $_data[] = ['module_id' => $v, 'created_time' => $time];
        }
        if ($diff) {
            $ds->batchAdd($_data);
        }
        $srv = Wekit::load('cron.srv.PwCronService');
        $srv->getSysCron('PwCronDoDesign', $time);

        return true;
    }

    private function _getStructureCss()
    {
        $css = [];

        $srv = Wekit::load('design.srv.PwDesignStyle');
        $structureNames = explode(',', $this->_pageInfo['struct_names']);
        foreach ($structureNames as $v) {
            if (! $v) {
                continue;
            }
            $bo = new PwDesignStructureBo($v);
            $srv->setDom($v);
            $srv->setStyle($bo->getStyle());
            list($domId, $_css) = $srv->getCss();
            $css[$domId] = $_css;
            list($domId, $_css) = $srv->getLink($bo->getStyle());
            $css[$domId] = $_css;
        }

        return $css;
    }

    private function _getModuleCss()
    {
        $css = [];

        $srv = Wekit::load('design.srv.PwDesignStyle');
        $moduleIds = explode(',', $this->_pageInfo['module_ids']);
        foreach ($moduleIds as $v) {
            if (! $v) {
                continue;
            }
            $bo = new PwDesignModuleBo($v);
            $dom = $this->_module_pre.$v;
            $srv->setDom($dom);
            $srv->setStyle($bo->getStyle());
            list($domId, $_css) = $srv->getCss();
            $css[$domId] = $_css;
            list($domId, $_css) = $srv->getLink($bo->getStyle());
            $css[$domId] = $_css;
        }

        return $css;
    }

    private function _formatStyle($bold = '', $underline = '', $italic = '', $color = '')
    {
        if ($bold) {
            $style = 'font-weight:bold;';
        }
        if ($underline) {
            $style .= 'text-decoration:underline;';
        }
        if ($italic) {
            $style .= 'font-style:italic;';
        }
        if ($color) {
            $style .= 'color:'.$color;
        }

        return $style ? $style : '';
    }

    private function _setPageInfo()
    {
        $this->_pageInfo = $this->_getPageDs()->getPage($this->pageid);
    }

    private function _getPageDs()
    {
        return Wekit::load('design.PwDesignPage');
    }

    private function _getPushDs()
    {
        return Wekit::load('design.PwDesignPush');
    }

    private function _getDataDs()
    {
        return Wekit::load('design.PwDesignData');
    }

    private function _getBakDs()
    {
        return Wekit::load('design.PwDesignBak');
    }
}
