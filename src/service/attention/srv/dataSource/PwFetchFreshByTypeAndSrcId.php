<?php
defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('LIB:dataSource.iPwDataSource');

/**
 * 获取某个类型的新鲜事
 *
 * @author $Author: jieyin $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwFetchFreshByTypeAndSrcId.php 14776 2012-07-26 10:25:06Z jieyin $ 
 * @package attention
 */

class PwFetchFreshByTypeAndSrcId implements iPwDataSource {
	
	public $type;
	public $ids = array();

	public function __construct($type, $ids) {
		$this->type = $type;
		$this->ids = $ids;
	}

	public function getData() {
		return Wekit::load('attention.PwFresh')->getFreshByType($this->type, $this->ids);
	}
}
?>