<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwDesignCompile.php 28769 2013-05-23 03:48:46Z gao.wanggao $ 
 * @package 
 */
class PwDesignCompile {
	
	public $pageid = 0;
	protected $router;
	protected $isDesign = false;
	protected $moduleIds = array();
	protected $structNames = array();
	protected $segments = array();
	protected $pageBo;
	
	
	private $_uri = '';
   	private $_mode = '';
   	private $_dataModule = 0;
   	private $_permission = -1;
   	private $_loginUid;
   	private $_pageName = '';
   	private $_uniqueId = 0;
   	
	private static $_instance = null;
	
	public static function getInstance() {
		!isset(self::$_instance) && self::$_instance = new self();
		return self::$_instance;
	}
    
    public function setIsDesign($isdesign = false) {
    	$loginUser = Wekit::getLoginUser();
	    $designPermission = $loginUser->getPermission('design_allow_manage.push');
	    $this->_permission = $designPermission ? $designPermission : -1;
	    $this->_loginUid = $loginUser->uid;
	    
    	$this->isDesign = false;
    	if ($isdesign) {
			if ( $this->_permission > 0) $this->isDesign = (bool)$isdesign;
    	}
    }
    
    public function setCompileMode($mode = 'data') {
    	$this->_mode = $mode;
    }
    
    public function setPageid($pageid) {
    	$this->pageid = (int)$pageid;
    }
    
    public function getPageid() {
    	return $this->pageid;
    }
    
    public function getPageBo() {
   	  	return $this->pageBo;
    }
    
    public function getPageType() {
    	$pageInfo = $this->pageBo->getPage();
    	return $pageInfo['page_type'];
    }
    
    public function setPermission() {
    	Wekit::load('design.PwDesignPermissions');
		$this->_permission = Wekit::load('design.srv.PwDesignPermissionsService')->getPermissionsForPage($this->_loginUid, $this->pageid);
    }
    
    public function beforeDesign($router, $pageName = '', $uniqueId = 0) {
    	if ($router == 'bbs/read/run') {
			$thread = Wekit::load('forum.PwThread')->getThread($uniqueId);
			$uniqueId = $thread['fid'];
		}
    	Wind::import('SRV:design.bo.PwDesignPageBo');
    	$this->pageBo = new PwDesignPageBo();
		$pageid = $this->pageBo->getPageId($router, $pageName, $uniqueId);
		$this->pageBo->setPageInfo($pageid);
		$this->pageid = (int)$this->pageBo->pageid;
  
    }
    
    
    /**
     * 判断是否需进行门户编辑
     * Enter description here ...
     */
    public function isPortalCompile() {
    	if ($this->_permission < PwDesignPermissions::IS_ADMIN ) {
    		return 0;
    	}
    	if (!$this->isDesign) return 0;
    	$pageInfo = $this->pageBo->getPage();
    	if ($pageInfo['page_type'] == PwDesignPage::PORTAL) {
    		return 1;
    	}
    	return 2;
    }
    
    public function startDesign($uniqueId = 0, $uri = '') {
    	$data = $this->pageBo->getPageCss();
    	$data .= $this->_getDataCode();
    	if ($this->isDesign) {
    	    if ($this->pageBo->getLock()) {
	    		$this->isDesign = false;
	    		$data .= '<template source="TPL:design.segment.toolbar_lock" load="true" />';
	    		$data .= '<div style="display:none">';
	    		$data .= '<input id="J_pageid"  name="pageid" type="hidden" value="'.$this->pageid.'">';
	    		$data .= '<input id="J_uri" name="uri" type="hidden" value="'.$uri.'">';
	    		$data .= '</div>';
    		} else {
				$page = $this->pageBo->getPage();
				if ($this->_permission == PwDesignPermissions::IS_DESIGN ) {
					$data .= '<?php $toolbar = "toolbar";?>';
				} else {
					$data .= '<?php $toolbar = "nodesign";?>';
				}
	    		//$data .= '<action action="design/design/toolbar" args="array(\'pageid\'=>'.$this->pageid.')" isRedirect="false" />';
				$data .= '<template source="TPL:design.segment.toolbar" load="true" />';
				$data .= '<div style="display:none">';
	    		$data .= '<input id="J_pageid"  name="pageid" type="hidden" value="'.$this->pageid.'">';
	    		$data .= '<input id="J_uri" name="uri" type="hidden" value="'.$uri.'">';
	    		if ($page['page_type'] == PwDesignPage::SYSTEM && $page['page_unique']){
	    			$type = 'unique';
	    		} else {
	    			$type = 'normal';
	    		}
	    		$data .= '<input id="J_type" name="type" type="hidden" value="'.$type.'">';
	    		$data .= '<input id="J_uniqueid" name="unique" type="hidden" value="'.$uniqueId.'">';
	    		$data .= '<input id="J_compile" name="compile" type="hidden" value="<?php echo Pw::encrypt(urlencode(str_replace(Wind::getRealDir(\'DATA:design\'),\'\',$__tpl)))?>">';
	    		$data .= '</div>';
    		}
    	}
    	//$url = urldecode($uri);
		$data .= '<!--# 
			$loginUser = Wekit::getLoginUser();
	   	 	$designPermission = $loginUser->getPermission(\'design_allow_manage.push\');
	    	if ($designPermission > 0){#-->';
		$data .= '<form method="post" action="">';
    	$data .= '<button class="design_mode_edit" type="submit">模块管理</button>';
    	$data .= '<input type="hidden" name="design" value="1">';
    	$data .= '</form>';
    	$data .= '<!--# } #-->';
    	return $data;
    }
    
	/**
	 * 刷新当前页面数据
	 */
	public function refreshPage() {
		$list = Wekit::load('design.PwDesignModule')->getByPageid($this->pageid);
		Wind::import('SRV:design.srv.data.PwAutoData');
		foreach ($list AS $id=>$v) {
			if ($id < 1) continue;
			$srv = new PwAutoData($id);
			$srv->addAutoData();
		}
		//Wekit::load('design.srv.PwDesignService')->clearCompile();
		return true;
	}
    
    
    public function afterDesign(){
    	$ds = Wekit::load('design.PwDesignPage');
    	Wind::import('SRV:design.dm.PwDesignPageDm');
		$dm = new PwDesignPageDm($this->pageid);
		$dm->setModuleIds(array_unique($this->moduleIds))
			->setStrucNames($this->structNames)
			->setSegments($this->segments);
		$resource = $ds->updatePage($dm);
		Wekit::load('design.PwDesignModule')->batchUpdateIsUsed($this->moduleIds);
		if ($this->isDesign) {
			 $loginUser = Wekit::getLoginUser();
			 $dm = new PwDesignPageDm($this->pageid);
			 $dm->setDesignLock($loginUser->uid, Pw::getTime());
			 $ds->updatePage($dm);
			 Wekit::load('design.srv.PwPageBakService')->doSnap($this->pageid);
		}
    }
    
    
    public function compileSegment($segmentId) {
    	$this->appendSegment($segmentId);
    	$segment = $this->_getSegment($segmentId);
    	if (!$this->isDesign) {
    		return $segment;
    	}
    	return '<div class="tempplace J_mod_wrap" id="'.$segmentId.'">'.$segment.'</div>';
    }
    
    public function compileData($segmentId) {
    	$this->appendSegment($segmentId);
    	$segment = $this->_getSegment($segmentId);
    	if ($this->isDesign) {
    		return '<div class="tempplace J_mod_wrap" id="'.$segmentId.'">'.$segment.'</div>';
    	} else {
    		if (!$this->_dataModule) return '<?php $getdata=1;?>';
	    	Wind::import('SRV:design.bo.PwDesignModuleBo');
	    	$bo = new PwDesignModuleBo($this->_dataModule);
	    	$html = $bo->getTemplate();
		    $standard = $bo->getStandardSign();
		    $view = $bo->getView();
    		$tpl = $this->_compileSign($html, $bo->moduleid, $standard['sTitle'], $standard['sUrl'], $view['isblank'], true);
    		$tpl = str_replace('"', '\"', $tpl);
    		$tpl = preg_replace("/[\s]{2,}/","",$tpl); 
    		$tpl = preg_replace("/[\n]/","",$tpl); 
    		return '<?php $datamodule = "'.$this->_dataModule.'"; $pageid = "'.$this->pageid.'"; $template="'.$tpl.'"; $getdata=1;?>';
    	}
    }
    
    /**
     * 用于模版中模块的编译
     * Enter description here ...
     * @param $moduleId
     */
    public function compileModule($module = '') {
    	Wind::import('SRV:design.bo.PwDesignModuleBo');
    	$module && list($data,$mod,$moduleId) = explode('_', $module);
    	!$moduleId && $moduleId = PwDesignModuleBo::$stdId;
    	$moduleId = (int)$moduleId;
  		if (!$moduleId) return '';
    	$bo = new PwDesignModuleBo($moduleId);
    	$html = $bo->getTemplate();
	    $caption = $bo->getTitleHtml();
	    $standard = $bo->getStandardSign();
	    $module = $bo->getModule();
	    $view = $bo->getView();
	    if (preg_match('/\<title>/isU',$html,$m)) {
	    	$html = str_replace($m[0], $caption, $html);
	    } else {
	    	$html = $caption.$html;
	    }
	    //$this->appendSegment($module);
	    $this->appendModuleId($moduleId);
	   	if ($this->isDesign && $module['module_type'] != PwDesignModule::TYPE_SCRIPT) {//模块进行片段化处理
	    	if ($html){
	    		$tpl = '<div class="J_mod_box" id="J_mod_'.$moduleId.'" data-id="'.$moduleId.'">';
		   	$tpl .= $html;
		    	$tpl .= '</div>';
	    	}
	   	} else {
	    	$tpl = $html;
	    }
	    
    	return $this->_compileSign($tpl, $bo->moduleid, $standard['sTitle'], $standard['sUrl'], $view['isblank']);
    }
    
    public function compileScript() {
    	Wind::import('SRV:design.bo.PwDesignModuleBo');
    	$moduleId = PwDesignModuleBo::$stdId;
    	$moduleId = (int)$moduleId;
  		if (!$moduleId) return '';
    	$bo = new PwDesignModuleBo($moduleId);
    	$html = $bo->getTemplate();
	    $caption = $bo->getTitleHtml();
	    $standard = $bo->getStandardSign();
	    $view = $bo->getView();
	    if (preg_match('/\<title>/isU',$html,$m)) {
	    	$html = str_replace($m[0], $caption, $html);
	    } else {
	    	$html = $caption.$html;
	    }
		$html = preg_replace("/\r\n|\n|\r/", '', $html);
		$html = preg_replace("/onerror=\"(.+)\"/", '', $html);
		$html = "document.write('".$html."');";
	    return $this->_compileSign($html, $bo->moduleid, $standard['sTitle'], $standard['sUrl'], $view['isblank']);
    }
	
    
    public function compileTitle($struct = '') {
    	if (!$struct) return '';
    	Wind::import('SRV:design.bo.PwDesignStructureBo');
    	$bo = new PwDesignStructureBo($struct);
    	if ($this->isDesign) {
    		return '<div id="'.$struct.'" class="J_mod_title" role="structure_'.$struct.'">'.$bo->getTitle().'</div>';
    	}
    	return $bo->getTitle();
    }
    
    public function compileList($moduleId) {
    	$moduleId = (int)$moduleId;
  		if (!$moduleId) return '';
    	Wind::import('SRV:design.bo.PwDesignModuleBo');
    	$bo = new PwDesignModuleBo($moduleId);
    	$html = $bo->getTemplate();
	    $caption = $bo->getTitleHtml();
	    $standard = $bo->getStandardSign();
	    $module = $bo->getModule();
	    $view = $bo->getView();
	    if (preg_match('/\<title>/isU',$html,$m)) {
	    	$html = str_replace($m[0], $caption, $html);
	    } else {
	    	$html = $caption.$html;
	    }
	    //$this->appendSegment($module);
	    $this->appendModuleId($moduleId);
	   	if ($this->isDesign && $module['module_type'] != PwDesignModule::TYPE_SCRIPT) {//模块进行片段化处理
	    	if ($html){
	    		$tpl = '<div class="J_mod_box" id="J_mod_'.$moduleId.'" data-id="'.$moduleId.'">';
		   	$tpl .= $html;
		    	$tpl .= '</div>';
	    	}
	   	} else {
	    	$tpl = $html;
	    }
	    
    	return $this->_compileSign($tpl, $bo->moduleid, $standard['sTitle'], $standard['sUrl'], $view['isblank']);
    }
	
    public function compileTips($id = '') {
    	$pageInfo = $this->pageBo->getPage();
    	if ($this->isDesign && !$pageInfo['struct_names']) {
    		return '<div id="linkdemo" class="tempplace_tips">选择一个合适的结构拖至此区域</div>';
    	}
    	return '';
    }
    
    /**
     * 用于segment中模块的转换
     * Enter description here ...
     * @param unknown_type $content <design id="D_mod_60" role="module"></design>
     */
    public  function replaceModule($content = '') {
		 if(preg_match_all('/\<design\s*id=\"*D_mod_(\d+)\"*\s*role=\"*module\"*\s*[>|\/>]<\/design>/isU', $content, $matches)) {
		 	Wind::import('SRV:design.bo.PwDesignModuleBo');
    		foreach ($matches[1] AS $k=>$v) {
    			$this->appendModuleId($v);
     			$bo = new PwDesignModuleBo($v);
    			$html = $bo->getTemplate();
    			$caption = $bo->getTitleHtml();
    			$standard = $bo->getStandardSign();
    			$view = $bo->getView();
    			if (preg_match('/\<title>/isU',$html,$m)) {
    				$html = str_replace($m[0], $caption, $html);
    			} else {
    				$html = $caption.$html;
    			}
    			$html = $this->_compileSign($html, $bo->moduleid, $standard['sTitle'], $standard['sUrl'], $view['isblank']);
    			$content = str_replace($matches[0][$k], $html, $content);
    		}
		 }
		return $content;
	}

	public function reduceStructure($content = '') {
		if(preg_match_all('/\<div[^>]*role=\"*structure_(.+)\"* [^>]+>/isU', $content, $matches)) {
			foreach ($matches[1] AS $k=>$v) {
				$this->appendStructName($v);
			}
		} 
    	$content = str_replace('J_mod_layout_none', 'J_mod_layout', $content); 
    	return $content;
	}
	
   	public function appendModuleId($moduelid){
    	(int)$moduelid && $this->moduleIds[] = (int)$moduelid;
    }
    
    public function appendStructName($name) {
    	$name && $this->structNames[] = $name;
    }
    
    public function appendSegment($segment) {
    	$segment && $this->segments[] = $segment;
    }
	
	private function _compileSign($content, $moduleid = 0, $standardTitle = '', $standardUrl = '', $isblank = false, $isdata = false) {
    	$istitle = '';
    	//列表style
    	$content = preg_replace('/title=["|\']'.$standardTitle.'["|\']/isU', 'title="__TITLE"', $content);
    	$content = preg_replace('/alt=["|\']'.$standardTitle.'["|\']/isU', 'alt="__TITLE"', $content);
    	
		if(preg_match_all('/\{(\w+)\|(\d+)}/U', $content, $matches)) {
			foreach ($matches[1] AS $k=>$v) {
				if ($standardTitle == '{'.$v.'}') {
					$content = str_replace($matches[0][$k], '{'.$v.'}', $content);
					$istitle = $matches[0][$k];
				}
    		}
    	 }
    	 
		//_blank
		if($isblank){
			$content = preg_replace('/href=["|\']'.$standardUrl.'["|\']/isU', 'href="'.$standardUrl.'" target="_blank"', $content);
		}
    	
    	if (!$isdata){
	    	$out = '<if:__style>';
	    	$out .= '<span style="{__style}">'.$standardTitle.'</span><else:>';
	    	$out .= $standardTitle;
	    	$out .= '</if>';
	    	$content = str_replace($standardTitle, $out, $content);
    	}
    	
    	if ($istitle) {
    		$content = str_replace($standardTitle, $istitle, $content);
    	}
    	
    	$content = str_replace('title="__TITLE"', 'title="'.$standardTitle.'"', $content);
    	$content = str_replace('alt="__TITLE"', 'alt="'.$standardTitle.'"', $content);
    	
    	if ($moduleid) {
    		$srv = Wekit::load('design.srv.display.PwDesignDisplay');
    		$moduleName = $srv->bindDataKey($moduleid);
    	}
    	$content = preg_replace('/\<for:(\d+)>/isU', '<for:>', $content, 1);
		$in = array(
			'/\<if:{(\w+)}>/iU',
			'/\<if:!{(\w+)}>/iU',
			'/\{(\w+)\|(\d+)\|(\d+)}/U',
			'/\{(\w+)\|(\d+)}/U',
			'/\{(\w+)\|img}/U',//([a-z]+)
			'/\{(\w+)\|html}/U',
			'/\{(\w+)}/iU',
			'/\<for:>/iU',
			'/\<\/for>/iU',
			'/\<if:(\d+)>/iU',
			'/\<\/if>/iU',
			'/\<if:odd>/iU',
			'/\<if:even>/iU',
			'/\<if:__style>/iU',
			'/\<else:>/iU',
			'/\<elseif:(\d+)>/iU',
		);
		if ($isdata){
			$out = array(
				'<% if(\\1 != ""){ %>',
				'<% if(\\1 == ""){ %>',
				'<%=\\1%>',
				'<%=\\1%>',
				'<%=\\1%>',
				'<%=\\1%>',
				'<%=\\1%>',
				/*'<?php \\$__data=(is_array(\\$__design_data[\''.$moduleName.'\']))?\\$__design_data[\''.$moduleName.'\']:array();foreach(\\$__data AS \\$__k=>\\$__v){?>',*/
				'',
				'',
				'<% if(k == \\1){%>',
				'<% }%>',
				'',
				'',
				'',
				'<% }else{%>',
				'<% }elseif(__k == \\1){%>'
			);
		
		} else {
			$out = array(
				'<?php if(empty(\\$__v[\'\\1\'])){?>',
				'<?php if(!empty(\\$__v[\'\\1\'])){?>',
				'<?php echo WindSecurity::escapeHTML(\\$__v[\'\\1\']);?>',
				'<?php echo WindSecurity::escapeHTML(Pw::substrs(\\$__v[\'\\1\'],\\2));?>',
				/*'<?php echo \\$__v[\'\\1\'];?>', //TODO
				'<?php echo \\$__v[\'\\1\'];?>',*/
				'<?php echo WindSecurity::escapeHTML(\\$__v[\'\\1\']);?>',
				'<?php echo WindSecurity::escapeHTML(\\$__v[\'\\1\']);?>',
				'<?php echo WindSecurity::escapeHTML(\\$__v[\'\\1\']);?>',
				/*'<?php \\$__data=(is_array(\\$__design_data[\''.$moduleName.'\']))?\\$__design_data[\''.$moduleName.'\']:array();foreach(\\$__data AS \\$__k=>\\$__v){?>',*/
				'<?php if(is_array(\\$__design_data[\''.$moduleName.'\'])){
						\\$__data=\\$__design_data[\''.$moduleName.'\'];
					}else{
						\\$display=Wekit::load(\'design.srv.display.PwDesignDisplay\');
						\\$__data=\\$display->getModuleData('.$moduleid.');
					};foreach(\\$__data AS \\$__k=>\\$__v){?>',
				'<?php }?>',
				'<?php if(\\$__k == \\1){?>',
				'<?php }?>',
				'<?php if(!is_int(\\$__k/2)){?>',
				'<?php if(is_int(\\$__k/2)){?>',
				'<?php if(\\$__v[\'__style\']){?>',
				'<?php }else{?>',
				'<?php }elseif(\\$__k == \\1){?>'
			);
		}
		//$content = str_replace('\\','\\\\',$content);
		//$content = str_replace('"','\"',$content);
		$content = preg_replace($in ,$out, $content);
		return $content;
	}
    
    private function _getDataCode() {
    	$args = 'array(';
    	$moduleids  = $this->pageBo->getPageModules();
    	foreach ($moduleids AS $v) {
    		$v && $args .= $v . ',';
    	}
    	$args .= ')';
    	return '<!--# 
    			$__design_pageid = '.$this->pageid.';
    			Wind::import("SRV:design.bo.PwDesignPageBo");
    			$__design = new PwDesignPageBo();
    			$__design_data = $__design->getDataByModules('.$args.');
    			#-->';
    }
    
	private function _getSegment($segment) {
		$data =  Wekit::load('design.PwDesignSegment')->getSegment($segment, $this->pageid);
		if (!$data) return '';
		$segment = $data['segment_tpl'];
		//Wind::import('SRV:design.bo.PwDesignStructureBo');
		if(preg_match_all('/\<div[^>]*role=\"*structure_(.+)\"* [^>]+>/isU', $segment, $matches)) {
			foreach ($matches[1] AS $k=>$v) {
				$this->appendStructName($v);
			}
		}
		if (!$this->isDesign) {
			$segment = str_replace('role=titlebar>&nbsp;</H2>', 'role="titlebar"></H2>', $segment);
		    $segment = str_replace('role=titlebar', 'role="titlebar"', $segment);
    		$segment = str_replace('<H2 class="design_layout_hd cc J_layout_hd" role="titlebar"></H2>','',$segment);
    		//$segment = preg_replace('/\<h2\s*[^>]*role=\"titlebar"[^>]+><\/h2>/isU' ,'', $segment);
		}
		
    	//IE fix
		if(preg_match_all('/\<div\s*[^>]*id=\"*J_mod_(\d+)\"* [^>]+>/isU', $segment, $matches)) {
			foreach ($matches[1] AS $k=>$v) {
				if ($this->_mode == 'data') $this->_dataModule = $v;
				$this->appendModuleId($v);
    		}
    	 }
    	 
		if(preg_match_all('/\<div\s*[^>]*id=\"*J_mod_(\d+)\"*[>|\/>]/isU', $segment, $matches)) {
			foreach ($matches[1] AS $k=>$v) {
				if ($this->_mode == 'data') $this->_dataModule = $v;
				$this->appendModuleId($v);
    		}
    	 }
    	
		//TODO
		if ($this->_permission < 4) {
			$segment = str_replace('J_mod_layout', 'J_mod_layout_none', $segment);
		}
		$segment = $this->replaceModule($segment);
		return $segment;
	}
}
?>