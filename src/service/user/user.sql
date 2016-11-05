-- TableName pw_user_ban 用户禁止表 
-- Fields id 禁止ID
-- Fields uid 用户ID
-- Fields typeid 禁言类型  禁言类型用按位与
-- Fields fid 禁言的类型ID 如 fid 版块ID
-- Fields ban_day 禁止天数
-- Fields created_userid 创建人
-- Fields created_time 状态创建时间
-- Fields reason 原因
-- Unique Key idx_uid_fid 由用户ID，禁止子类型ID号
-- Index idx_createdUid 根据创建人
CREATE TABLE `pw_user_ban` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `typeid` CHAR(20) NOT NULL DEFAULT '',
  `fid` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `ban_day` SMALLINT(6) NOT NULL DEFAULT 0,
  `created_userid` INT(10) NOT NULL DEFAULT 0,
  `created_time` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `reason` VARCHAR(80) NOT NULL default '',
  PRIMARY KEY (`id`),
  UNIQUE `idx_uid_typeid_fid` (`uid`, `typeid`, `fid`),
  INDEX `idx_createdUid` (`created_userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- TableName pw_user_status 用户状态查询表
-- Fields uid 用户ID
-- Fields ifcheck 保存用户是否已经审核
-- Fields ifactive 保存用户是否已经激活
CREATE TABLE `pw_user_register_check` (
  `uid` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `ifchecked` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
  `ifactived` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
  PRIMARY KEY (`uid`),
  INDEX `idx_ifcheck` (`ifcheck`),
  INDEX `idx_ifactive` (`ifactive`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- TableName pw_user_baseinfo 用户信息表
-- Fields uid 用户ID
-- Fields ifcheck 是否已经被审核
-- Fields ifemailactive 是否邮箱已经激活
-- Fields status 用户状态
-- Fields vippoint vip用户组提升综合分
-- Fields memberpoint member用户组提升综合分
-- Fields groupid  用户当前使用身份0:普通用户组，-1：VIP用户组
-- Fields vipid vip组等级
-- Fields memberid 普通组的等级
-- Fields realname 用户真实姓名
-- Fields bbs_sign 帖子签名
-- Primary Key uid
-- Index idx_ifcheck 审核字段索引
-- Index idx_ifemailactive 邮件激活字段索引
-- Index idx_groupid 当前显示身份字段索引
-- Index idx_vipid vip组字段索引
-- Index idx_memberid 会员组字段索引 
CREATE TABLE `pw_user_baseinfo` (
  `uid` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `username` VARCHAR(25) NOT NULL DEFAULT '',
  `email` VARCHAR() NOT NULL DEFAULT '',
  `realname` VARCHAR(50) NOT NULL DEFAULT '',
  `status` SMALLINT(3) NOT NULL DEFAULT 0,
  `groupid` MEDIUMINT(8) NOT NULL DEFAULT 0,
  `memberid` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0,
  `groups` VARCHAR(255) NOT NULL DEFAULT '',
  `bbs_sign` TEXT NOT NULL ,
  PRIMARY KEY  (`uid`),
  INDEX `idx_status` (`status`),
  INDEX `idx_groupid` (`groupid`),
  INDEX `idx_memberid` (`memberid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- TableName pw_user_extendinfo 用户扩展信息
-- Fields uid 用户ID
-- Fields zipcode 邮政编码
-- Fields telphone 用户固定电话号码
-- Fields address 邮寄地址
-- Fields regreason 注册原因
-- Fields credit_limit 积分上限
-- Fields profile 用户个人简介
-- Primary key uid
CREATE TABLE `pw_user_extendinfo` (
  `uid` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `zipcode` VARCHAR(10) NOT NULL DEFAULT '',
  `telphone` VARCHAR(20) NOT NULL DEFAULT '',
  `address` VARCHAR(100) NOT NULL DEFAULT '',
  `regreason` VARCHAR(200) NOT NULL DEFAULT '',
  `credit_limit` VARCHAR(255) NOT NULL DEFAULT '',
  `profile` TEXT,
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- TableName pw_user_belong  用户拥有的组
-- Fields uid 用户ID
-- Fields gid 用户组ID
-- Fields endtime 用户组过期时间
-- Unique key idx_uid_gid 由用户ID，用户组ID 组成的唯一索引
-- Index idx_gid 用户组ID 索引
CREATE TABLE `pw_user_belong` (
  `uid` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `gid` MEDIUMINT(8) NOT NULL DEFAULT 0,
  `endtime` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  UNIQUE KEY `idx_uid_gid` (`uid`, `gid`),
  INDEX `idx_gid` (`gid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- TableName pw_user_statistics 用户统计信息数据表
-- Fields uid 用户ID
-- Fields lastlogindate 最后登录时间
-- Fields lastloginip 最后登录IP
-- Fields online 在线时间长度
-- Fields trypwd 尝试的登录错误信息，trydate|trynum
-- Fields findpwd 找回密码尝试错误次数,trydate|trynum
-- Fields follow 关注数
-- Fields fans 粉丝数
-- Primary Key uid
CREATE TABLE IF NOT EXISTS `pw_user_statistics` (
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `lastlogindate` int(10) unsigned NOT NULL DEFAULT '0',
  `lastloginip` varchar(20) NOT NULL DEFAULT '',
  `online` int(10) unsigned NOT NULL DEFAULT '0',
  `trypwd` varchar(16) NOT NULL DEFAULT '',
  `findpwd` varchar(26) NOT NULL DEFAULT '',
  `follows` int(10) unsigned NOT NULL DEFAULT '0',
  `fans` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 同一IP允许注册间隔时间功能
-- TableName pw_user_register_ip 用户注册IP缓存表
-- Fields ip IP地址
-- Fields last_regdate 上次注册的时间
-- Fields num 该IP注册的数目
-- Primary Key ip
CREATE TABLE `pw_user_register_ip` (
  `ip` VARCHAR(20) NOT NULL DEFAULT '',
  `last_regdate` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `num` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`ip`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- TableName pw_user_active_code 用户激活码记录表
-- Fields uid 用户ID
-- Fields email 发送激活码的邮箱地址
-- Fields code 激活码
-- Fields send_time 发送时间
-- Fields active_time 激活时间
-- Primary key uid
CREATE TABLE `pw_user_active_code` (
  `uid` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `email` VARCHAR(80) NOT NULL DEFAULT '',
  `code` VARCHAR(10) NOT NULL DEFAULT '',
  `flag` VARCHAR(10) NOT NULL DEFAULT '',
  `send_time` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `active_time` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`uid`),
  INDEX `idx_flag` (`flag`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- TableName pw_user_login_ip_recode 用户登录的IP次数记录
-- Fields ip 用户端IP
-- Fields last_time 最后登录尝试时间格式：0000-00-00
-- Fields error_count 登录的错误次数
CREATE TABLE `pw_user_login_ip_recode` (
	`ip` VARCHAR(20) NOT NULL DEFAULT '',
	`last_time` VARCHAR(11) NOT NULL DEFAULT '',
	`error_count` SMALLINT(6) UNSIGNED NOT NULL DEFAULT 0,
	PRIMARY KEY (`ip`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;