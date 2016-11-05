<?php
/**
 * 系统任务列表
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: jinlong.panjl $>
 * @author $Author: jinlong.panjl $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: systemCron.php 22664 2012-12-26 07:55:11Z jinlong.panjl $ 
 * @package 
 */

return array(
	array(				
		'name'=>'清理在线用户',	//key:任务名
		'file'=>'PwCronDoClearOnline',	//文件名
		'type'=>'now',				//循环类型  month/week/day/hour/now之一  now为马上执行,
		'time'=>array('day'=>0,'hour'=>0,'minute'=>10),//循环时间:day,hour,minute   hour 24小时制  如果type=now,这里的时间就为循环时间
	),
	array(
		'name'=>'门户更新触发任务',
		'file'=>'PwCronDoDesign',
		'type'=>'day',
		'time'=>array('day'=>0,'hour'=>3,'minute'=>0),
	),
	array(
		'name'=>'更新帖子点击数',
		'file'=>'PwCronDoUpdateHits',
		'type'=>'now',
		'time'=>array('day'=>0,'hour'=>0,'minute'=>5),
	),
	array(
		'name'=>'清理帖子今日统计',
		'file'=>'PwCronDoClearForumTodayposts',
		'type'=>'day',
		'time'=>array('day'=>0,'hour'=>0,'minute'=>0),
	)
);
?>