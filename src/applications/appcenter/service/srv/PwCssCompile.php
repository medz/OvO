<?php
/**
 * 编译css ，界面设置
 *
 * @author Shi Long <long.shi@alibaba-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwCssCompile.php 24341 2013-01-29 03:08:55Z jieyin $
 * @package appcenter
 */
class PwCssCompile {
	/**
	 * 编译css ，界面设置
	 *
	 * @param array $__css
	 */
	public function doCompile($__css = null) {
		if ($__css === null) $__css = Wekit::C('css');
		$template = Wind::getRealPath('TPL:appcenter.admin.head_css.htm', true);
		ob_start();
		include $template;
		$style = ob_get_clean();
		$style = preg_replace(array('/\{[\s]+\}/', '/(\{|\})[\s]+/', '/[\t\n\r]/'), array('{}', '\\1', ''), $style);
		$style = trim(preg_replace('/(body|\.header_wrap|\.box_wrap|\.box_wrap \.box_title)\{\}/', '', $style));
		$style && $style = '<style>' . $style . '</style>';
		$configDs = Wekit::C();
		$configDs->setConfig('site', 'css.tag', $style);
		$configDs->setConfig('site', 'css.logo', $__css['logo']);
	}
}

?>