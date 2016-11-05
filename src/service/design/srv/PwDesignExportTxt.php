<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwDesignExportTxt.php 24989 2013-02-28 02:53:30Z gao.wanggao $ 
 * @package 
 */
class PwDesignExportTxt {
	
	public $pageInfo = array();
	
	public function __construct($pageInfo) {
		$this->pageInfo = $pageInfo;
	}
	

	public function txt($charset = 'utf-8') {
		$pageInfo = $this->pageInfo;
		$_modules = array();
		//$ids = explode(',', $pageInfo['module_ids']);
		$modules = $this->_getModuleDs()->getByPageid($pageInfo['page_id']);
		$fromCharset = Wekit::app()->charset;
		foreach ($modules  AS $k=>$v) {
			if (!$v['isused']) continue;
			unset($v['isused'], $v['module_id']);
			$v['module_property'] = unserialize($v['module_property']);
			$v['module_cache'] = unserialize($v['module_cache']);
			$v['module_style'] = unserialize($v['module_style']);
			$v['module_title'] = unserialize($v['module_title']);
			$v = $this->_conv($v, $fromCharset, $charset);
			$v['module_property'] = serialize($v['module_property']);
			$v['module_cache'] = serialize($v['module_cache']);
			$v['module_style'] = serialize($v['module_style']);
			$v['module_title'] = serialize($v['module_title']);
			$_modules[$k] = $v;
		}
		$names = explode(',', $pageInfo['struct_names']);

		$structures = $this->_getStructureDs()->fetchStruct($names);
		foreach ($structures AS &$v) {
			$v['struct_title'] = unserialize($v['struct_title']);
			$v['struct_style'] = unserialize($v['struct_style']);
			$v = $this->_conv($v, $fromCharset, $charset);
			$v['struct_title'] = serialize($v['struct_title']);
			$v['struct_style'] = serialize($v['struct_style']);
		}
		$txtSegment = array();
		$segments = $this->_getSegmentDs()->getSegmentByPageid($pageInfo['page_id']);
		foreach ($segments AS $k=>$v) {
			if (!$v['segment_tpl']) continue;
			$txtSegment[$k] = $this->_conv($v['segment_struct'], $fromCharset, $charset);
		}
		$txtPage['module_ids'] = $pageInfo['module_ids'];
		$txtPage['struct_names'] = $pageInfo['struct_names'];
		$_nr = "\n";
		$_time = Pw::getTime();
		$_title = $_nr;
		$_text['page']= $txtPage;
		$_text['segment'] = $txtSegment;
		$_text['structure'] = $structures;
		$_text['module'] = $_modules;
		$_text = wordwrap(base64_encode(serialize($_text)), 100, $_nr, true);
		$_end = $_nr;
		$filename = $pageInfo['page_name'] ? $pageInfo['page_name'] : $_time;
		$_text = $_title . $_text . $_end;
		return array(
			'content'=>$_text,
			'filename'=>$filename,
			'ext'=>'txt'
		);
	}
	
	private function _conv($array, $fromCharset, $toCharset) {
		if (!is_array($array)) return WindConvert::convert($array, $toCharset, $fromCharset);
		foreach ($array AS $k=>$v) {
			if (is_array($v)) {
				$array[$k] = $this->_conv($v, $fromCharset, $toCharset);
			}
			$array[$k] = WindConvert::convert($v, $toCharset, $fromCharset);
		}
		return $array;
	}
	
	private function _getSegmentDs() {
		return Wekit::load('design.PwDesignSegment');
	}
	
	private function _getModuleDs() {
		return Wekit::load('design.PwDesignModule');
	}
	
	private function _getStructureDs() {
		return Wekit::load('design.PwDesignStructure');
	}
	
	private function _getPageDs() {
		return Wekit::load('design.PwDesignPage');
	}
	
	private function _getPortalDs() {
		return Wekit::load('design.PwDesignPortal');
	}
}
?>