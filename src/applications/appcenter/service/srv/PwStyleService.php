<?php
Wind::import('APPCENTER:service.dm.PwStyleDm');
Wind::import('APPCENTER:service.srv.helper.PwApplicationHelper');
/**
 * 风格服务
 *
 * @author Shi Long <long.shi@alibaba-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwStyleService.php 24585 2013-02-01 04:02:37Z jieyin $
 * @package service.style.srv
 */
class PwStyleService {
	private $manifest = 'Manifest.xml';

	/**
	 * 使用这个风格
	 */
	public function useStyle($styleid) {
		if (!$style = $this->_styleDs()->getStyle($styleid)) return new PwError(
			'STYLE:style.not.exist');
		$oldStyle = $this->_styleDs()->getCurrentStyleByType($style['style_type']);
		if ($oldStyle) {
			$oldDm = new PwStyleDm();
			$oldDm->setAppid($oldStyle['app_id'])->setIsCurrent(0);
			$this->_styleDs()->updateStyle($oldDm);
		}
		$dm = new PwStyleDm();
		$dm->setAppid($styleid)->setIsCurrent(1);
		$this->_styleDs()->updateStyle($dm);
		//Wekit::load('domain.srv.PwDomainService')->refreshTplCache();
		return $this->_setConfigBo($style);
	}

	/**
	 * 查找未安装的风格
	 *
	 * @return array 未安装的风格名
	 */
	public function getUnInstalledThemes() {
		$config = Wekit::load('APPCENTER:service.srv.PwInstallApplication')->getConfig('style-type');
		$themes = array();
		foreach ($config as $k => $v) {
			$dir = Wind::getRealDir('THEMES:' . $v[1]);
			$files = WindFolder::read($dir, WindFolder::READ_DIR);
			foreach ($files as $file) {
				if (WindFile::isFile($dir . '/' . $file . '/' . $this->manifest)) $themes[$k][] = $file;
			}
		}
		if (empty($themes)) return array();
		$styles = array();
		foreach ($themes as $k => $v) {
			$r = $this->_styleDs()->fetchStyleByAliasAndType($v, $k, 'alias');
			$r = array_diff($v, array_keys($r));
			$r && $styles[$k] = $r;
		}
		return $styles;
	}

	/**
	 * 设置全局TPL配置
	 *
	 * @param array $style        	
	 * @return boolean
	 */
	private function _setConfigBo($style) {
		$configBo = new PwConfigSet('site');
		$config = Wekit::load('APPCENTER:service.srv.PwInstallApplication')->getConfig('style-type');
		foreach ($config as $k => $v) {
			$configBo->set("theme.$k.pack", $v[1]);
		}
		$configBo->set("theme.{$style['style_type']}.default", $style['alias']);
		$configBo->flush();
		return true;
	}

	/**
	 *
	 * @return PwStyle
	 */
	private function _styleDs() {
		return Wekit::load('APPCENTER:service.PwStyle');
	}
}

?>