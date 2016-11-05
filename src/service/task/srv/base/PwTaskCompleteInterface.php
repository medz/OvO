<?php

/**
 * 完成任务的扩展中必须实现的接口
 *
 * @author xiaoxia.xu <x_824@sina.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwTaskCompleteInterface.php 5435 2012-03-05 03:56:05Z xiaoxia.xuxx $
 * @package src.service.task.srv.base
 */
interface PwTaskCompleteInterface {
	
	
	/**
	 * 做任务的接口
	 * 
	 * <b>说明：</b>
	 * 该接口将会被调用,该接口中提供用户判断是否该任务是否已经被完成
	 * 同时将完成的相关信息保存到$step中<br/>
	 * <b>返回：</b>
	 * 该接口需要返回一个二维数组，同时该数组必须包含如下键值：
	 * <ul>
	 * <li>isComplete: boolean类型，如果该任务已经完成，则返回true,否则返回false</li>
	 * <li>step: 该任务的进度状况，该值将会在下次做该任务的时候返回给用户进行跟新，该step中如果含有percent{一个用百分比表明的任务进度如20%},
	 * 该值将会在“进行中的任务”中表明该任务进度条</li>
	 * </ul>
	 *
	 * @param array $conditions 后台设置的该任务的完成条件
	 * @param array $step 该任务的当前进度信息值
	 * @return array array('isComplete' => boolean, 'step' => $step);
	 */
	public function doTask($conditions, $step);
}