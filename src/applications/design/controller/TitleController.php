<?php
Wind::import('APPS:design.controller.DesignBaseController');
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: TitleController.php 28899 2013-05-29 07:23:48Z gao.wanggao $ 
 * @package 
 */
class TitleController extends DesignBaseController{
	
	public function beforeAction($handlerAdapter) {
		parent::beforeAction($handlerAdapter);
		Wekit::load('design.PwDesignPermissions');
		$permissions = $this->_getPermissionsService()->getPermissionsForModule($this->loginUser->uid,$this->bo->moduleid, $this->pageid);
		if ($permissions < PwDesignPermissions::IS_ADMIN ) $this->showError("DESIGN:permissions.fail");
	}
	
	public function editAction() {
		$titles = $this->bo->getTitle();
		if (!$titles['titles']){
			$titles['titles'] = array(array('title'=>''));
		}
		$this->setOutput($this->_getDesignService()->getSysFontSize(), 'sysfontsize');
		$this->setOutput($titles, 'titles');
		$this->setOutput($this->bo->moduleid, 'moduleid');
	}
	
	public function doeditAction() {
		$array = array();
		$html = '';
		$title = $this->getInput('title','post');
		$link = $this->getInput('link','post');
		$image = $this->getInput('image','post');
		$float = $this->getInput('float','post');
		$margin = $this->getInput('margin','post');
		//$position = $this->getInput('position','post');
		//$pixels = $this->getInput('pixels','post');
		$fontsize = $this->getInput('fontsize','post');
		$fontcolor = $this->getInput('fontcolor','post');
		$fontbold = $this->getInput('fontbold','post');
		$fontunderline = $this->getInput('fontunderline','post');
		$fontitalic = $this->getInput('fontitalic','post');
		
		$bgimage = $this->getInput('bgimage','post');
		$bgcolor = $this->getInput('bgcolor','post');
		$bgposition = $this->getInput('bgposition','post');
		
		$styleSrv = $this->_getStyleService();
		
		$background = array();
		$bgimage && $background['image'] = $bgimage;
		$bgcolor && $background['color'] = $bgcolor;
		$bgposition && $background['position'] = $bgposition;
		
		//foreach ($pixels AS &$v) $v = (int)$v ? (int)$v: '';
		foreach ($fontsize AS &$v) $v = (int)$v ? (int)$v: '';
		foreach ($title AS $k=>$value) {
			$_tmp = array(
				'title'=>$title[$k],
				'link'=>$link[$k],
				'image'=>$image[$k],
				'float'=>$float[$k],
				'margin'=>$margin[$k],
				'fontsize'=>$fontsize[$k],
				'fontcolor'=>$fontcolor[$k],
				'fontbold'=>$fontbold[$k],
				'fontunderline'=>$fontunderline[$k],
				'fontitalic'=>$fontitalic[$k],
			);
			$style = $this->_buildTitleStyle($_tmp);
			$styleSrv->setStyle($style);
			list($dom,$jstyle) = $styleSrv->getCss($style);
			$jtitle = $image[$k] ? '<img src="'.$_tmp['image'].'" title="'.WindSecurity::escapeHTML($_tmp['title']).'">' : WindSecurity::escapeHTML($_tmp['title']);
			if ($jtitle) {
				$html .= '<span ';
				$html .= $jstyle ? 'style="'.$jstyle.'"' : '' ;
				$html .= '>';
				$html .= $_tmp['link'] ? '<a href="'.$_tmp['link'].'">' : '';
				$html .= $jtitle;
				$html .= $_tmp['link'] ? '</a>' : '';
				$html .='</span>';
				$array['titles'][] = $_tmp;
			}
		}
		if ($background) {
			$array['background'] = $background;
			$bg = array('background'=>$background);
			$styleSrv->setStyle($bg);
			list($dom, $data['background']) = $styleSrv->getCss();
		}
		$bgStyle = $data['background'] ? '  style="'.$data['background'].'"' : '';
		if ($html) $html = '<h2 class="cc design_tmode_h2"'.$bgStyle.'>'.$html.'</h2>';
		Wind::import('SRV:design.dm.PwDesignModuleDm');
 		$dm = new PwDesignModuleDm($this->bo->moduleid);
 		$dm->setTitle($array);
		$resource = $this->_getModuleDs()->updateModule($dm);
		if ($resource instanceof PwError) $this->showError($resource->getError());
		$this->setOutput($html, 'html');
		$this->showMessage("operate.success");
	}
	
	private function _buildTitleStyle($style) {
		return array(
				'float'=>array('type'=>$style['float'],'margin'=>$style['margin']),
				'font'=>array('size'=>$style['fontsize'],'color'=>$style['fontcolor'],'bold'=>$style['fontbold'],'underline'=>$style['fontunderline'],'italic'=>$style['fontitalic']),
				'background'=>array('color'=>$style['bgcolor'],'image'=>$style['bgimage'],'position'=>$style['bgposition']),
		);
	}
	
	private function _getModuleDs() {
		return Wekit::load('design.PwDesignModule');
	}
	
	private function _getDesignService() {
		return Wekit::load('design.srv.PwDesignService');
	}
	
	private function _getStyleService() {
		return Wekit::load('design.srv.PwDesignStyle');
	}

}
?>