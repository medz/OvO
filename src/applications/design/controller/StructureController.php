<?php


 
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>.
 *
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: StructureController.php 28899 2013-05-29 07:23:48Z gao.wanggao $
 */
class StructureController extends PwBaseController
{
    public $bo;

    public function beforeAction($handlerAdapter)
    {
        parent::beforeAction($handlerAdapter);
         
        $permissions = $this->_getPermissionsService()->getPermissionsForUserGroup($this->loginUser->uid);
        if ($permissions < PwDesignPermissions::IS_DESIGN) {
            $this->showError('DESIGN:permissions.fail');
        }
        $name = $this->getInput('name', 'post');
         
        $this->bo = new PwDesignStructureBo($name);
    }

    public function titleAction()
    {
        $titles = $this->bo->getTitle();
        $pageid = (int) $this->getInput('pageid', 'post');
        $title = $this->getInput('title', 'post');
        $tab = $this->getInput('tab', 'post');
        if (!$titles['titles']) {
            if ($tab) {
                $i = 1;
                foreach ($tab as $v) {
                    $titles['titles'][] = array('title' => '栏目'.$i, 'tab' => $v);
                    $i++;
                }
                $this->setOutput('tab', 'structure');
            } else {
                $titles['titles'] = array(array('title' => $title));
            }
        }
        $this->setOutput($this->_getDesignService()->getSysFontSize(), 'sysfontsize');
        $this->setOutput($titles, 'titles');
        $this->setOutput($this->bo->name, 'name');
        $this->setOutput($pageid, 'pageid');
    }

    /**
     * 拖拉模块标题修改
     * Enter description here ...
     */
    public function doedittitleAction()
    {
        $html = '';
        $array = array();
        $pageid = (int) $this->getInput('pageid', 'post');
        $title = $this->getInput('title', 'post');
        if ($pageid < 1) {
            $this->showError('permissions.fail');
        }
        $link = $this->getInput('link', 'post');
        $image = $this->getInput('image', 'post');
        $float = $this->getInput('float', 'post');
        $margin = $this->getInput('margin', 'post');
        $fontsize = $this->getInput('fontsize', 'post');
        $fontcolor = $this->getInput('fontcolor', 'post');
        $fontbold = $this->getInput('fontbold', 'post');
        $fontunderline = $this->getInput('fontunderline', 'post');
        $fontitalic = $this->getInput('fontitalic', 'post');
        $bgimage = $this->getInput('bgimage', 'post');
        $bgcolor = $this->getInput('bgcolor', 'post');
        $bgposition = $this->getInput('bgposition', 'post');
        $structure = $this->getInput('structure', 'post');
        $tab = $this->getInput('tab', 'post');
        $styleSrv = $this->_getStyleService();
        $_n = 0;
        foreach ($tab as $v) {
            if ($v) {
                list($t, $n) = explode('_', $v);
                if ($n >= $_n) {
                    $_n = $n + 1;
                }
            }
        }
        $background['image'] = $bgimage;
        $background['color'] = $bgcolor;
        $background['position'] = $bgposition;
        foreach ($title as $k => $value) {
            $_tmp = array(
                'title'         => WindSecurity::escapeHTML($title[$k]),
                'link'          => $link[$k],
                'image'         => $image[$k],
                'float'         => $float[$k],
                'margin'        => (int) $margin[$k],
                'fontsize'      => (int) $fontsize[$k],
                'fontcolor'     => $fontcolor[$k],
                'fontbold'      => $fontbold[$k],
                'fontunderline' => $fontunderline[$k],
                'fontitalic'    => $fontitalic[$k],
            );
            $style = $this->_buildTitleStyle($_tmp);
            $styleSrv->setStyle($style);
            list($dom, $jstyle) = $styleSrv->getCss();
            $jtitle = $image[$k] ? '<img src="'.$_tmp['image'].'" title="'.$_tmp['title'].'">' : $_tmp['title'];
            if ($jtitle) {
                if ($structure == 'tab') {
                    if (!$tab[$k]) {
                        $tab[$k] = 'tab_'.$_n;
                        $_n++;
                    }

                    $html .= '<li role="tab">';
                    $html .= '<a data-id="'.$tab[$k].'" href="'.$_tmp['link'].'"';
                    $html .= $jstyle ? ' style="'.$jstyle.'"' : '';
                    $html .= '>';
                    $html .= $jtitle;
                    $html .= '</a>';
                    $html .= '</li>';
                    $_tmp['tab'] = $tab[$k];
                } else {
                    $html .= '<span';
                    $html .= $jstyle && !$_tmp['link'] ? ' style="'.$jstyle.'"' : '';
                    $html .= '>';
                    $html .= $_tmp['link'] ? '<a href="'.$_tmp['link'].'" style="'.$jstyle.'">' : '';
                    $html .= $jtitle;
                    $html .= $_tmp['link'] ? '</a>' : '';
                    $html .= '</span>';
                }
                $array['titles'][] = $_tmp;
            }
        }

        $data['tab'] = $html;
        $data['tabName'] = $tab;
        if ($background) {
            $array['background'] = $background;
            $bg = array('background' => $background);
            $styleSrv->setStyle($bg);
            list($dom, $data['background']) = $styleSrv->getCss();
        }
         
        $dm = new PwDesignStructureDm();
        $style = $this->bo->getStyle();
        $dm->setStructTitle($array)
            ->setStructname($this->bo->name)
            ->setStructStyle($style['font'], $style['link'], $style['border'], $style['margin'], $style['padding'], $style['background'], $style['styleclass']);
        $resource = $this->_getStructureDs()->replaceStruct($dm);
        if ($resource instanceof PwError) {
            $this->showError($resource->getError());
        }
        $this->setOutput($data, 'html');
        $this->showMessage('operate.success');
    }

    //导入模块的标题编辑
    public function editAction()
    {
        $pageid = (int) $this->getInput('pageid', 'post');
        $title = $this->bo->getTitle();
        $this->setOutput($title, 'title');
        $this->setOutput($this->bo->name, 'name');
        $this->setOutput($pageid, 'pageid');
    }

    public function doeditAction()
    {
        $pageid = (int) $this->getInput('pageid', 'post');
        $title = $this->getInput('title', 'post');
        $struct = $this->bo->getStructure();
        if (!$struct) {
            $this->showMessage('operate.fail');
        }
         
        $dm = new PwDesignStructureDm();
        $dm->setStructTitle($title)
            ->setStructname($this->bo->name);
        $resource = $this->_getStructureDs()->replaceStruct($dm);
        if ($resource instanceof PwError) {
            $this->showError($resource->getError());
        }

         
        $pageBo = new PwDesignPageBo($pageid);
        $pageInfo = $pageBo->getPage();

         
        $compile = new PwPortalCompile($pageBo);
        if ($pageInfo['page_type'] == PwDesignPage::PORTAL) {
            $compile->replaceTitle($this->bo->name, $title);
        } elseif ($pageInfo['page_type'] == PwDesignPage::SYSTEM) {
            !$struct['segment'] && $struct['segment'] = '';
            $compile->replaceTitle($this->bo->name, $title, $struct['segment']);
        }

        $this->setOutput($title, 'html');
        $this->showMessage('operate.success');
    }

    public function styleAction()
    {
        $srv = $this->_getDesignService();
        $this->setOutput($srv->getSysStyleClass(), 'sysstyle');
        $this->setOutput($srv->getSysFontSize(), 'sysfontsize');
        $this->setOutput($srv->getSysBorderStyle(), 'sysborder');
        $this->setOutput($srv->getSysLineWidth(), 'syslinewidth');
        $this->setOutput($this->bo->getStyle(), 'style');
        $this->setOutput($this->bo->name, 'name');
    }

    public function doeditstyleAction()
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

         
        $dm = new PwDesignStructureDm();
        $dm->setStructStyle($font, $link, $border, $margin, $padding, $background, $styleclass)
            ->setStructName($this->bo->name)
            ->setStructTitle($this->bo->getTitle());
        $resource = $this->_getStructureDs()->replaceStruct($dm);
        if ($resource instanceof PwError) {
            $this->showError($resource->getError());
        }

        $style = $dm->getStyle();
        //$style = array('font'=>$font, 'link'=>$link, 'border'=>$border, 'margin'=>$margin, 'padding'=>$padding, 'background'=>$background, 'styleclass'=>$styleclass);
        $styleSrv = $this->_getStyleService();
        $styleSrv->setDom($this->bo->name);

        $styleSrv->setStyle($style); //$this->differStyle($style)
        $_style['styleDomId'] = $styleSrv->getCss($style);
        $_style['styleDomIdLink'] = $styleSrv->getLink($style);
        $_style['styleDomClass'] = $styleclass;
        $this->setOutput($_style, 'html');
        $this->showMessage('operate.success');
    }

    public function deleteAction()
    {
        $this->_getStructureDs()->deleteStruct($this->bo->name);
        $this->showMessage('operate.success');
    }

    private function _buildTitleStyle($style)
    {
        return array(
                'float' => array('type' => $style['float'], 'margin' => $style['margin']),
                'font'  => array('size' => $style['fontsize'], 'color' => $style['fontcolor'], 'bold' => $style['fontbold'], 'underline' => $style['fontunderline'], 'italic' => $style['fontitalic']),
                //'background'=>array('color'=>$style['bgcolor'],'image'=>$style['bgimage'],'position'=>$style['bgposition']),
        );
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

    protected function _getPermissionsService()
    {
        return Wekit::load('design.srv.PwDesignPermissionsService');
    }

    private function _getStructureDs()
    {
        return Wekit::load('design.PwDesignStructure');
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
