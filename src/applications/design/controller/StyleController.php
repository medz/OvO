<?php

Wind::import('APPS:design.controller.DesignBaseController');
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: StyleController.php 28899 2013-05-29 07:23:48Z gao.wanggao $
 * @package
 */
class StyleController extends DesignBaseController
{
    public function beforeAction($handlerAdapter)
    {
        parent::beforeAction($handlerAdapter);
        Wekit::load('design.PwDesignPermissions');
        $permissions = $this->_getPermissionsService()->getPermissionsForModule($this->loginUser->uid, $this->bo->moduleid, $this->pageid);
        if ($permissions < PwDesignPermissions::IS_ADMIN) {
            $this->showError('DESIGN:permissions.fail');
        }
    }

    public function editAction()
    {
        $srv = $this->_getDesignService();
        $this->setOutput($srv->getSysListClass(), 'sysstyle');
        $this->setOutput($srv->getSysFontSize(), 'sysfontsize');
        $this->setOutput($srv->getSysBorderStyle(), 'sysborder');
        $this->setOutput($srv->getSysLineWidth(), 'syslinewidth');
        $this->setOutput($this->bo->getStyle(), 'style');
        $this->setOutput($this->bo->moduleid, 'moduleid');
    }

    public function doeditAction()
    {
        $styleclass = $this->getInput('styleclass', 'post');
        $font = $this->getInput('font', 'post');
        $link = $this->getInput('link', 'post');
        $border = $this->getInput('border', 'post');
        $margin = $this->getInput('margin', 'post');
        $padding = $this->getInput('padding', 'post');
        $background = $this->getInput('background', 'post');

        if ($border['isdiffer']) {
            unset($border['linewidth']);
            unset($border['style']);
            unset($border['color']);
        } else {
            unset($border['top']);
            unset($border['left']);
            unset($border['right']);
            unset($border['bottom']);
        }

        if ($margin['isdiffer']) {
            unset($margin['both']);
        } else {
            unset($margin['top']);
            unset($margin['right']);
            unset($margin['left']);
            unset($margin['bottom']);
        }
        if ($padding['isdiffer']) {
            unset($padding['both']);
        } else {
            unset($padding['top']);
            unset($padding['right']);
            unset($padding['left']);
            unset($padding['bottom']);
        }

        Wind::import('SRV:design.dm.PwDesignModuleDm');
        $dm = new PwDesignModuleDm($this->bo->moduleid);
        $dm->setStyle($font, $link, $border, $margin, $padding, $background, $styleclass);
        $resource = $this->_getModuleDs()->updateModule($dm);
        if ($resource instanceof PwError) {
            $this->showError($resource->getError());
        }
        $display = Wekit::load('design.srv.display.PwDesignDisplay');
        $styleSrv = $this->_getStyleService();
        $styleSrv->setDom($display->bindDataKey($this->bo->moduleid));
        $style = $dm->getStyle();
        //$style = array('font'=>$font, 'link'=>$link, 'border'=>$border, 'margin'=>$margin, 'padding'=>$padding, 'background'=>$background, 'styleclass'=>$styleclass);
        $styleSrv->setStyle($style); //$this->differStyle($style)
        $_style['styleDomId'] = $styleSrv->getCss($style);
        $_style['styleDomIdLink'] = $styleSrv->getLink($style);
        $_style['styleDomClass'] = $styleclass;
        $this->setOutput($_style, 'html');
        $this->showMessage('operate.success');
    }

    private function differStyle($style)
    {
        $array = array('top', 'right', 'bottom', 'left');
        $border = $style['border'];
        $border['isdiffer'] = 1;
        if ($border['linewidth']) {
            $border['top']['linewidth'] = (int) $border['linewidth'];
            $border['right']['linewidth'] = (int) $border['linewidth'];
            $border['bottom']['linewidth'] = (int) $border['linewidth'];
            $border['left']['linewidth'] = (int) $border['linewidth'];
            unset($border['linewidth']);
        }

        foreach ($array as $v) {
            $border[$v]['linewidth'] = (int) $border[$v]['linewidth'];
        }

        if ($border['style']) {
            $border['top']['style'] = $border['style'];
            $border['right']['style'] = $border['style'];
            $border['bottom']['style'] = $border['style'];
            $border['left']['style'] = $border['style'];
            unset($border['style']);
        }

        foreach ($array as $v) {
            $border[$v]['style'] = isset($border[$v]['style']) ? $border[$v]['style'] : 'none';
        }

        if ($border['color']) {
            $border['top']['color'] = $border['color'];
            $border['right']['color'] = $border['color'];
            $border['bottom']['color'] = $border['color'];
            $border['left']['color'] = $border['color'];
            unset($border['color']);
        }
        foreach ($array as $v) {
            $border[$v]['color'] = isset($border[$v]['color']) ? $border[$v]['color'] : '';
        }
        $style['border'] = $border;

        return $style;
    }

    private function _getModuleDs()
    {
        return Wekit::load('design.PwDesignModule');
    }

    private function _getDesignService()
    {
        return Wekit::load('design.srv.PwDesignService');
    }

    private function _getStyleService()
    {
        return Wekit::load('design.srv.PwDesignStyle');
    }

    private function _getBakDs()
    {
        return Wekit::load('design.PwDesignBak');
    }
}
