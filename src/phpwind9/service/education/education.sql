-- TableName pw_user_education 用户的教育经历插件
-- Fields id 
-- Fields uid 用户ID
-- Fields schoolid 学校ID
-- Fields degree 学历
-- Fields start_time 入学时间
CREATE TABLE `pw_user_education` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `schoolid` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `degree` TINYINT(2) UNSIGNED NOT NULL DEFAULT 0,
  `start_time` SMALLINT(4) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  INDEX `idx_uid_startTime` (`uid`, `start_time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;