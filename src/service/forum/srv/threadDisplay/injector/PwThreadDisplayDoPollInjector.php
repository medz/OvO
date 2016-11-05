<?php
defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 帖子发布-投票帖 相关服务
 *
 * @author MingXing Sun <mingxing.sun@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwThreadDisplayDoPollInjector.php 17614 2012-09-07 03:14:46Z yanchixia $
 * @package forum
 */

class PwThreadDisplayDoPollInjector extends PwBaseHookInjector {
	
	public function run() {
		Wind::import('SRV:forum.srv.threadDisplay.do.PwThreadDisplayDoPoll');
		return new PwThreadDisplayDoPoll($this->bp->tid, $this->bp->user);
	}
}