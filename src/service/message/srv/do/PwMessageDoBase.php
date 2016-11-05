<?php

/**
 * 发消息service扩展
 *
 * @author jinlong.panjl <jinlong.panjl@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id$
 * @package wind
 */
abstract class PwMessageDoBase {

	public function check($fromUid, $content,$uid=0) {
		return true;
	}

	public function addMessage($uid, $fromUid, $content) {

	}
}