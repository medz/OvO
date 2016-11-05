-- TableName pw_user_tag 用户个人标签基本表 
-- Fields tag_id 标签ID
-- Fields name 标签名字
-- Fields ifhot 标签是否是热门标签
-- Fields used_count 该标签被使用的次数
CREATE TABLE `pw_user_tag` (
  `tag_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(20) NOT NULL DEFAULT '',
  `ifhot` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  `used_count` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`tag_id`),
  UNIQUE `idx_name` (`name`),
  INDEX `idx_usedcount` (`used_count`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='用户个人标签基本信息表';

-- TableName pw_user_tag_relation 用户个人标签与用户的关系表
-- Fields tag_id 标签ID
-- Fields uid 用户ID
-- Fields created_time 创建关联时间
CREATE TABLE `pw_user_tag_relation` (
  `tag_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `created_time` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`uid`, `tag_id`),
  INDEX `idx_createdtime` (`created_time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='用户个人标签与用户的关系表';