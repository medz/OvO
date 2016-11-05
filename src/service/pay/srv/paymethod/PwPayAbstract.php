<?php
defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 在线支付
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwPayAbstract.php 24975 2013-02-27 09:24:54Z jieyin $
 * @package forum
 */

abstract class PwPayAbstract {

	public $charset = 'utf-8';
	public $baseurl;
	
	public function __construct() {
		$this->charset = Wekit::V('charset');
	}

	public function check() {
		return true;
	}

	abstract public function createOrderNo();

	abstract public function getUrl(PwPayVo $vo);
}