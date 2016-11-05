<?php
defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('LIB:dataSource.iPwDataSource');

/**
 * 获取新鲜事
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @license http://www.phpwind.com
 * @version $Id: PwGetFreshById.php 14777 2012-07-26 10:26:51Z jieyin $
 * @package attention
 */

class PwGetFreshById implements iPwDataSource {
	
	public $id;

	public function __construct($id) {
		$this->id = $id;
	}

	public function getData() {
		return Wekit::load('attention.PwFresh')->fetchFresh(array($this->id));
	}
}