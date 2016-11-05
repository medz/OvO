<?php
/**
 * Enter description here ...
 * 
 * @author peihong.zhangph <peihong.zhangph@aliyun-inc.com> Dec 13, 2011
 * @link http://www.phpwind.com
 * @copyright 2011 phpwind.com
 * @license
 * @version $Id: PwTagAction.php 3440 2012-01-12 08:25:36Z peihong.zhangph $
 */

abstract class PwTagAction{
	
	/**
	 * 
	 * 获取某话题的最新内容
	 * @param array $ids
	 */
	abstract function getContents($ids);
	
}