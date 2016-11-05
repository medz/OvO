<?php
Wind::import('ADMIN:library.AdminBaseController');
/**
 * 后台伪静态
 *
 * @author Shi Long <long.shi@alibaba-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: RewriteController.php 24341 2013-01-29 03:08:55Z jieyin $
 * @package rewrite.admin
 */
class RewriteController extends AdminBaseController {
	/*
	 * (non-PHPdoc) @see WindController::run()
	 */
	public function run() {
		$addons = Wekit::load('domain.srv.PwDomainService')->getRewriteAddOns();
		$rewrite = Wekit::C('rewrite');
		$this->setOutput($addons, 'addons');
		$this->setOutput($rewrite, 'rewrite');
	}

	/**
	 * 设置伪静态
	 */
	public function doModifyAction() {
		list($format, $isopen) = $this->getInput(array('format', 'isopen'));
		$bo = new PwConfigSet('rewrite');
		foreach ($format as $k => $v) {
			if (empty($v) && isset($isopen[$k])) $this->showError('REWRITE:format.empty');
			$bo->set("format.$k", $v);
		}
		$addons = Wekit::load('domain.srv.PwDomainService')->getRewriteAddOns();
		$rewriteData = array();
		$unique = array();
		foreach ($addons as $k1 => $v1) {
			$open = isset($isopen[$k1]) ? 1 : 0;
			$bo->set("isopen.$k1", $open);
			if ($open) {
				$format_i = preg_replace('/\{\w+\}/', '', $format[$k1]);
				if (in_array($format_i, $unique)) {
					$this->showError(array('REWRITE:format.conflict', array($format[$k1])));
				}
				$unique[] = $format_i;
				if ($k1 == 'thread') {
					$rewriteData['cate'] = array(
						'format' => $format[$k1],
						'pattern' => $this->_compileFormat($format[$k1]),
						'route' => 'bbs/cate/run');
				}
				$rewriteData[$k1] = array(
					'format' => $format[$k1], 
					'pattern' => $this->_compileFormat($format[$k1]), 
					'route' => $v1[2]);
			}
		}
		$bo->flush();
		Wekit::C()->setConfig('site', 'rewrite', $rewriteData);
		Wekit::load('domain.srv.PwDomainService')->refreshTplCache();
		Wekit::load('SRV:nav.srv.PwNavService')->updateConfig();
		$this->showMessage('success');
	}

	private function _compileFormat($format) {
		if ($pos = strpos($format, '{page}')) {
			$split = $format[$pos - 1];
			$format = substr($format, 0, $pos - 1) . substr($format, $pos);
		}
		$format = preg_quote($format, '/');
		$format = str_replace('\{fname\}', '(?P<fname>([a-z0-9]+)?)', $format);
		$format = str_replace('\{name\}', '(?P<name>[\x7f-\xff\da-z\.\_]+?)', $format);
		$format = str_replace('\{page\}', preg_quote($split, '/') . '?(?P<page>([0-9]+|e)?)', $format);
		$format = preg_replace('/\\\{(\w+)\\\}/', '(?P<\\1>(\d+)?)', $format);
		return '/^' . $format . '$/i';
	}
}

?>