<?php
Wind::import('WIND:ftp.WindSocketFtp');

/**
 * 普通ftp保存，不限制文件后缀
 *
 * @author Shi Long <long.shi@alibaba-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwFtpSave.php 22490 2012-12-25 03:05:09Z long.shi $
 * @package appcenter
 */
class PwFtpSave extends WindSocketFtp {

	protected function checkFile($filename) {
		return false;
	}
}

?>