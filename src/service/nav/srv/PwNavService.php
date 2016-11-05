<?php

/**
 * @author $Author: gao.wanggao $ 
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwNavService.php 19259 2012-10-12 06:22:59Z gao.wanggao $ 
 * @package  nav
 */
class PwNavService {
	
	public function getNavType() {
		$navType = array();
		$dir = Wind::getRealDir('SRV:nav.srv.navtype.');
		$list = WindFolder::read($dir, WindFolder::READ_FILE);
		foreach ($list AS $v) {
			$v = $dir.$v;
			if (!is_file($v)) continue;
			$types = @include $v;
			foreach ($types AS $type) {
				if (!isset($type['type'])) continue;
				$navType[$type['type']] = $type['name'];
			}
		}
		return $navType;
	}
	
	public function updateConfig() {
		$config = new PwConfigSet('nav');
		$navBo = Wekit::load('SRV:nav.bo.PwNavBo');
		$navTypes = $this->getNavType();
		$ds = $this->_getNavDs();
		foreach ($navTypes AS $type=>$name) {
			$_list = array();
			$list = $ds->getNavByType($type);
			foreach ($list AS $key => $value) {
				if (!$value['name']) continue;
				$_list[$key]['name'] = $navBo->bindHtml($value);
				$_list[$key]['sign'] = $value['sign'];
				foreach ((array)$value['child'] AS $ckey => $cvalue) {
					if (!$cvalue['name']) continue;
					$_list[$key]['child'][$ckey]['name'] = $navBo->bindHtml($cvalue);
					$_list[$key]['child'][$ckey]['sign'] = $cvalue['sign'];
				}
			}
			$config->set($type, $_list)->flush();
		}
		return true;
	}
	
	public function updateNavOpen($sign, $isshow = 0) {
		if (!$sign) return false;
		$nav = $this->_getNavDs()->getNavBySign('my', $sign);
		if (!$nav['navid']) return false;
		Wekit::load('SRV:nav.dm.PwNavDm');
		$dm = new PwNavDm($nav['navid']);
		$dm->setIsshow($isshow);
		$resource = $this->_getNavDs()->updateNav($dm);
		if ($resource instanceof PwError) return false;
		$this->updateConfig();
		return true;
	}
	
	private function _getNavDs() {
		return Wekit::load('SRV:nav.PwNav');
	}
	
}

?>