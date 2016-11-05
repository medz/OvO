<?php

/**
 * admin应用模块，基础db服务
 * 
 * 该服务继承自PwBaseDao
 * @author Qiong Wu <papa0924@gmail.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: codetemplates(windframework_docs_zend_8.0).xml 2781 2011-09-22 03:59:17Z yishuo $
 * @package wind
 */
class AdminBaseDao extends PwBaseDao {

	public function __construct() {
		$this->setDelayAttributes(
			array(
				'connection' => array(
					'ref' => (Wekit::app()->dbComponentName ? Wekit::app()->dbComponentName : 'db'))));
	}

	/**
	 * 获取当前dao表明称
	 *
	 * @return string
	 */
	public function getTable($table = '') {
		!$table && $table = $this->_table;
		Wekit::app()->dbTableMark && $table = Wekit::app()->dbTableMark . $table;
		return $this->getConnection()->getTablePrefix() . $table;
	}
}

?>