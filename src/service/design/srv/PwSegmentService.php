<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwSegmentService.php 18274 2012-09-12 09:53:24Z gao.wanggao $ 
 * @package 
 */
class PwSegmentService {
	
	/**
	 * 对模版片段进行更新
	 * Enter description here ...
	 * @param int $pageid
	 */
	public function updateSegmentByPageId($pageid) {
		$ds = $this->_getSegmentDs();
		$srv = $this->_getCompileService();
		$list = $ds->getSegmentByPageid($pageid);
		foreach ($list AS $k=>$v) {
			$struct_tpl = $srv->replaceModule($v['segment_struct']);
			$ds->replaceSegment($v['segment'], $pageid, $struct_tpl, $v['segment_struct']);
		}
	}
	
	
	
	private function _getCompileService() {
		return Wekit::load('design.srv.PwDesignCompile');
	}
	
	private function _getSegmentDs() {
		return Wekit::load('design.PwDesignSegment');
	}
}
?>