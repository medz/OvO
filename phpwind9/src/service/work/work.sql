-- TableName pw_user_work 用户的工作经历插件
-- Fields id 
-- Fields uid 用户ID
-- Fields company 公司名称
-- Fields starty 开始时间-年
-- Fields startm 开始时间-月
-- Fields endy 结束时间-年
-- Fields endm 结束时间-月
CREATE TABLE `pw_user_work` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `company` VARCHAR(100) NOT NULL DEFAULT '',
  `starty` SMALLINT(4) NOT NULL DEFAULT 0,
  `startm` TINYINT(2) NOT NULL DEFAULT 0,
  `endy` SMALLINT(4) NOT NULL DEFAULT 0,
  `endm` TINYINT(2) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  INDEX `idx_uid_starty_start_m` (`uid`, `starty`, `startm`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;