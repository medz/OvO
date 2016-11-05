<?php
/**
 * Enter description here ...
 * 
 * @author peihong.zhangph <peihong.zhangph@aliyun-inc.com> Dec 13, 2011
 * @link http://www.phpwind.com
 * @copyright 2011 phpwind.com
 * @license
 * @version $Id: PwNoticeAction.php 3440 2012-01-12 08:25:36Z peihong.zhangph $
 */

abstract class PwNoticeAction{
	
	public $aggregate = false;
	
	abstract function buildTitle($param = 0,$extendParams = null,$aggregatedNotice = null);
	
	/**
	 * 
	 * 组装扩展参数
	 */
	abstract function formatExtendParams($extendParams,$aggregatedNotice = null);
	
	abstract function getDetailList($notice);
}