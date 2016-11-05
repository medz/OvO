-- TableName pw_invite_code 用户邀请码基本表
-- Fields code 邀请码
-- Fields created_userid 邀请码购买者
-- Fields invited_uid 被邀请者
-- Fields ifused 该邀请码是否已经被使用
-- Fields created_time 该邀请码的创建时间
-- Fields modified_time 该邀请码的使用时间
-- Primary key code_createdUserid 邀请码和创建者id唯一索引
-- INDEX idx_inviteduid_ifused 被邀请者ID
-- INDEX idx_typeid 
CREATE TABLE `pw_invite_code` (
	`code` CHAR(32) NOT NULL DEFAULT '',
	`created_userid` INT(10) NOT NULL DEFAULT 0,
	`invited_userid` INT(10) NOT NULL DEFAULT 0,
	`ifused` TINYINT(1) NOT NULL DEFAULT 0,
	`created_time` INT(10) NOT NULL DEFAULT 0,
	`modified_time` INT(10) NOT NULL DEFAULT 0,
	PRIMARY KEY (`code`),
	INDEX `idx_createduid` (`created_userid`),
	INDEX `idx_inviteduid` (`invited_userid`),
	INDEX `idx_createdTime` (`created_time`)
) ENGINE=MyISAM	DEFAULT CHARSET=utf8;