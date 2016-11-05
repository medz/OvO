<?php
/**
 * 后台管理平台错误操作处理
 *
 * @author Qiong Wu <papa0924@gmail.com> 2011-10-14
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: MessageController.php 28892 2013-05-29 06:41:54Z jieyin $
 * @package common
 */
class MessageController extends PwErrorController {
	/**
	 * 消息提示
	 * 
	 * @see WindErrorHandler::run()
	 */
	public function run() {
		$this->setOutput($this->state, 'state');
		if (isset($this->error['data'])) {
			$this->setOutput($this->error['data'], "data");
			unset($this->error['data']);
		}
		if (isset($this->error['html'])) {
			$this->setOutput($this->error['html'], 'html');
			unset($this->error['html']);
		}
		$this->setOutput($this->error, "message");
		$this->setTemplate('common.error');
	}
}
