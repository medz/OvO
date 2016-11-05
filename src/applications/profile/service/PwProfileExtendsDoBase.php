<?php

/**
 * 扩展接口
 *
 * @author xiaoxia.xu<xiaoxia.xuxx@alibaba-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwProfileExtendsDoBase.php 21826 2012-12-13 10:45:50Z jinlong.panjl $
 * @package wind
 */
abstract class PwProfileExtendsDoBase {
	/**
	 * @var PwUserProfileExtends
	 */
	protected $bp = null;
	
	/**
	 *
	 * @param PwUserProfileExtends $bp
	 */
	public function __construct(PwUserProfileExtends $bp = null) {
		$this->bp = $bp;
	}
	
	
	/**
	 * 输出模板
	 *
	 * @param string $left
	 * @param string $tab
	 */
	public function createHtml($left, $tab) {
	}
	
	/**
	 * 执行操作
	 */
	public function execute() {
		
	}
	
	/**
	 * 检查，在执行之前做检测
	 * 如果检查失败需要返回PwError的错误类型
	 * 如果检查成功返回true
	 * 
	 * @return mixed
	 */
	public function check() {
		return true;
	}
	
	/**
	 * 输出底部模板
	 *
	 */
	public function displayFootHtml($left) {

	}
}