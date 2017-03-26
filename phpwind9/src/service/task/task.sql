-- TableName pw_task 任务表
-- Fields taskid			主键，自动增长
-- Fields is_auto			1为自动申请，0为手动申请
-- Fields is_display_all 	1为显示给所有用户，0为符合条件才显示
-- Fields view_order 		顺序
-- Fields is_open 			0 - 未启用 1 - 启用
-- Fields start_time 		开始时间
-- Fields end_time			结束时间
-- Fields period			周期
-- Fields pre_task			前置任务id
-- Fields title				名称
-- Fields description		描述
-- Fields icon				图标
-- Fields user_groups		可申请用户组
-- Fields reward			奖励，序列化数据
-- Fields conditions		完成条件的限制条件，序列化数据
-- Index idx_pretask
DROP TABLE IF EXISTS `pw_task`;
CREATE TABLE `pw_task` (
`taskid` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
`pre_task` INT(10) UNSIGNED NOT NULL DEFAULT 0,
`is_auto` TINYINT(1) NOT NULL DEFAULT 0,
`is_display_all` TINYINT(1) NOT NULL DEFAULT 0,
`view_order` SMALLINT(4) NOT NULL DEFAULT 0,
`is_open` TINYINT(3) NOT NULL DEFAULT 0,
`start_time` INT(10) UNSIGNED NOT NULL DEFAULT 0,
`end_time` INT(10) UNSIGNED NOT NULL DEFAULT 0,
`period` SMALLINT(6) NOT NULL DEFAULT 0,
`title` VARCHAR(100) NOT NULL DEFAULT '',
`description` VARCHAR(255) NOT NULL DEFAULT '',
`icon` VARCHAR(200) NOT NULL DEFAULT '',
`user_groups` VARCHAR(255) NOT NULL DEFAULT '-1',
`reward` VARCHAR(255) NOT NULL DEFAULT '',
`conditions` VARCHAR(255) NOT NULL DEFAULT '',
PRIMARY KEY (`taskid`),
INDEX `idx_pretask` (`pre_task`)
)ENGINE=MYISAM DEFAULT CHARSET=utf8; 

-- TableName pw_task_user 用户任务表
-- Fields taskid		任务id
-- Fields uid			用户id
-- Fields status		任务状态 0-已申请 1-完成任务未领奖励 2 - 已领奖
-- Fields is_period		是否周期性
-- Fields step 			任务完成程度
-- Fields created_time		任务创建时间
-- Fields finish_time		任务结束时间
-- Index  idx_uid_status
DROP TABLE IF EXISTS `pw_task_user`;
CREATE TABLE `pw_task_user` (
`taskid` INT(10) UNSIGNED NOT NULL DEFAULT 0,
`uid` INT(10) UNSIGNED NOT NULL DEFAULT 0,
`task_status` TINYINT(3) NOT NULL DEFAULT 0,
`is_period` TINYINT(1) NOT NULL DEFAULT 0,
`step` varchar(100) NOT NULL DEFAULT '',
`created_time` INT(10) UNSIGNED NOT NULL DEFAULT 0,
`finish_time` INT(10) UNSIGNED NOT NULL DEFAULT 0,
PRIMARY KEY (`uid`, `taskid`),
INDEX `idx_uid_taskstatus` (`uid`, `task_status`)
)ENGINE=MYISAM DEFAULT CHARSET=utf8;

-- TableName pw_task_group 任务索引表，用于适合任务列表检索
-- Fields task_id		任务id
-- Fields gid			用户组id
-- Fields is_display_all        是否对所有用于显示
-- Fields is_auto		是否自动申请
-- Fields start_time		开始时间
-- Fields end_time		结束时间
DROP TABLE IF EXISTS `pw_task_group`;
CREATE TABLE `pw_task_group` (
`taskid` INT(10) UNSIGNED NOT NULL DEFAULT 0,
`gid` INT(10) NOT NULL DEFAULT 0,
`is_auto` TINYINT(1) NOT NULL DEFAULT 0,
`end_time` INT(10) UNSIGNED NOT NULL DEFAULT 0,
PRIMARY KEY (`gid`, `taskid`)
)ENGINE=MYISAM DEFAULT CHARSET=utf8;

-- TableName pw_task_cache	自动申领任务状态cacahe表，记录了用户上一次自动申领的任务状态
-- Fields uid			用户id
-- Fields task_ids			最近一次自动申领的任务ID以及可自动申领的周期性任务ID（序列化存储）
DROP TABLE IF EXISTS `pw_task_cache`;
CREATE TABLE `pw_task_cache` (
`uid` INT(10) UNSIGNED NOT NULL DEFAULT 0,
`task_ids` VARCHAR(200) NOT NULL DEFAULT '',
PRIMARY KEY (`uid`)
)ENGINE=MYISAM DEFAULT CHARSET=utf8;
