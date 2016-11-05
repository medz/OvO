
DROP TABLE IF EXISTS `pw_acloud_apis`;
CREATE TABLE `pw_acloud_apis` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `template` text,
  `argument` varchar(255) NOT NULL DEFAULT '',
  `argument_type` varchar(255) NOT NULL DEFAULT '',
  `fields` varchar(255) NOT NULL DEFAULT '',
  `status` tinyint(3) NOT NULL DEFAULT '0',
  `category` tinyint(3) NOT NULL DEFAULT '0',
  `created_time` int(10) unsigned NOT NULL DEFAULT '0',
  `modified_time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='aCloud预定义的系统接口列表';

DROP TABLE IF EXISTS `pw_acloud_apps`;
CREATE TABLE `pw_acloud_apps` (
  `app_id` char(22) NOT NULL DEFAULT '',
  `app_name` varchar(60) NOT NULL DEFAULT '',
  `app_token` char(128) NOT NULL DEFAULT '',
  `created_time` int(10) unsigned NOT NULL DEFAULT '0',
  `modified_time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`app_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='应用调用站点数据所用的token';

DROP TABLE IF EXISTS `pw_acloud_app_configs`;
CREATE TABLE `pw_acloud_app_configs` (
  `app_id` char(22) NOT NULL DEFAULT '',
  `app_key` varchar(30) NOT NULL DEFAULT '',
  `app_value` text,
  `app_type` tinyint(3) NOT NULL DEFAULT '1',
  `created_time` int(10) unsigned NOT NULL DEFAULT '0',
  `modified_time` int(10) unsigned NOT NULL DEFAULT '0',
  UNIQUE KEY `app_id` (`app_id`,`app_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='应用自定义设置';

DROP TABLE IF EXISTS `pw_acloud_extras`;
CREATE TABLE `pw_acloud_extras` (
  `ekey` varchar(100) NOT NULL DEFAULT '',
  `evalue` text,
  `etype` tinyint(3) NOT NULL DEFAULT '1',
  `created_time` int(10) unsigned NOT NULL DEFAULT '0',
  `modified_time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`ekey`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='站点的云平台开启状态和基本属性';

DROP TABLE IF EXISTS `pw_acloud_keys`;
CREATE TABLE `pw_acloud_keys` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `key1` char(128) NOT NULL DEFAULT '',
  `key2` char(128) NOT NULL DEFAULT '',
  `key3` char(128) NOT NULL DEFAULT '',
  `key4` char(128) NOT NULL DEFAULT '',
  `key5` char(128) NOT NULL DEFAULT '',
  `key6` char(128) NOT NULL DEFAULT '',
  `created_time` int(10) unsigned NOT NULL DEFAULT '0',
  `modified_time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='站点和云平台通信使用的密钥';

DROP TABLE IF EXISTS `pw_acloud_sql_log`;
CREATE TABLE `pw_acloud_sql_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `log` text,
  `created_time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='aCloud执行SQL后记录的日志';

DROP TABLE IF EXISTS `pw_acloud_table_settings`;
CREATE TABLE `pw_acloud_table_settings` (
  `name` varchar(255) NOT NULL DEFAULT '',
  `status` tinyint(3) NOT NULL DEFAULT '0',
  `category` tinyint(3) NOT NULL DEFAULT '0',
  `primary_key` varchar(20) NOT NULL DEFAULT '',
  `created_time` int(10) unsigned NOT NULL DEFAULT '0',
  `modified_time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='用户抓取数据时的配置';

DROP TABLE IF EXISTS `pw_admin_auth`;
CREATE TABLE `pw_admin_auth` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `uid` int(10) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `username` varchar(15) NOT NULL DEFAULT '' COMMENT '用户名',
  `roles` varchar(255) NOT NULL DEFAULT '' COMMENT '角色',
  `created_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `modified_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后修改时间',
  PRIMARY KEY (`id`),
  KEY `idx_uid` (`uid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='用户权限角色表';

DROP TABLE IF EXISTS `pw_admin_config`;
CREATE TABLE `pw_admin_config` (
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT '配置名称',
  `namespace` varchar(15) NOT NULL DEFAULT 'global' COMMENT '配置命名空间',
  `value` text COMMENT '缓存值',
  `vtype` enum('string','array','object') NOT NULL DEFAULT 'string' COMMENT '配置值类型',
  `description` text COMMENT '配置介绍',
  PRIMARY KEY (`namespace`,`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='网站配置表';

DROP TABLE IF EXISTS `pw_admin_custom`;
CREATE TABLE `pw_admin_custom` (
  `username` varchar(15) NOT NULL,
  `custom` text COMMENT '常用菜单项',
  PRIMARY KEY (`username`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='后台常用菜单表';

DROP TABLE IF EXISTS `pw_admin_role`;
CREATE TABLE `pw_admin_role` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `name` varchar(15) NOT NULL DEFAULT '' COMMENT '角色名',
  `auths` text COMMENT '权限点',
  `created_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `modified_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后修改时间',
  PRIMARY KEY (`id`),
  KEY `idx_name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='后台用户角色表';

DROP TABLE IF EXISTS `pw_advertisement`;
CREATE TABLE `pw_advertisement` (
  `pid` int(10) unsigned NOT NULL,
  `identifier` varchar(30) NOT NULL,
  `type_id` tinyint(3) unsigned NOT NULL,
  `width` smallint(6) NOT NULL DEFAULT '0',
  `height` smallint(6) NOT NULL DEFAULT '0',
  `status` tinyint(3) NOT NULL DEFAULT '0',
  `schedule` varchar(100) NOT NULL,
  `show_type` tinyint(3) NOT NULL DEFAULT '0',
  `condition` text,
  UNIQUE KEY `pid` (`pid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='广告位数据表';

DROP TABLE IF EXISTS `pw_announce`;
CREATE TABLE `pw_announce` (
  `aid` smallint(6) NOT NULL AUTO_INCREMENT,
  `vieworder` smallint(6) NOT NULL DEFAULT '0',
  `created_userid` int(10) unsigned NOT NULL DEFAULT '0',
  `typeid` tinyint(1) NOT NULL DEFAULT '0',
  `url` varchar(80) DEFAULT '',
  `subject` varchar(100) NOT NULL DEFAULT '',
  `content` MEDIUMTEXT,
  `start_date` int(10) unsigned NOT NULL DEFAULT '0',
  `end_date` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`aid`),
  KEY `idx_startdate` (`start_date`),
  KEY `idx_vieworder` (`vieworder`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='公告管理表';

DROP TABLE IF EXISTS `pw_application`;
CREATE TABLE `pw_application` (
  `app_id` char(20) NOT NULL DEFAULT '' COMMENT '应用id',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '名称',
  `alias` varchar(100) NOT NULL DEFAULT '' COMMENT '别名',
  `logo` varchar(100) NOT NULL DEFAULT '' COMMENT '应用logo',
  `author_name` varchar(30) NOT NULL DEFAULT '' COMMENT '作者名',
  `author_icon` varchar(100) NOT NULL DEFAULT '' COMMENT '作者头像',
  `author_email` varchar(200) NOT NULL DEFAULT '' COMMENT '作者email',
  `website` varchar(200) NOT NULL DEFAULT '' COMMENT '开发者网站',
  `version` varchar(50) NOT NULL DEFAULT '' COMMENT '应用版本',
  `pwversion` varchar(50) NOT NULL DEFAULT '',
  `created_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `modified_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT '描述',
  PRIMARY KEY (`app_id`),
  UNIQUE KEY `alias` (`alias`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='本地应用信息表';

DROP TABLE IF EXISTS `pw_application_log`;
CREATE TABLE `pw_application_log` (
  `app_id` char(20) NOT NULL DEFAULT '' COMMENT '应用id',
  `log_type` char(10) NOT NULL DEFAULT '' COMMENT '日志类型',
  `data` text COMMENT '日志内容',
  `created_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `modified_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
  UNIQUE KEY `app_id` (`app_id`,`log_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='应用安装日志表';

DROP TABLE IF EXISTS `pw_app_poll`;
CREATE TABLE `pw_app_poll` (
  `poll_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增长ID',
  `voter_num` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '投票人数',
  `isafter_view` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否投票后查看结果',
  `isinclude_img` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否包含图片',
  `option_limit` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '投票选项控制',
  `regtime_limit` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '投票注册时间控制',
  `created_userid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '投票发起人',
  `app_type` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '投票类型',
  `expired_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '投票有效时间',
  `created_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '投票创建时间',
  PRIMARY KEY (`poll_id`),
  KEY `idx_createduserid_createdtime` (`created_userid`,`created_time`),
  KEY `idx_voternum` (`voter_num`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='投票基本信息表';

DROP TABLE IF EXISTS `pw_app_poll_option`;
CREATE TABLE `pw_app_poll_option` (
  `option_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '选项自增长ID',
  `poll_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '投票ID',
  `voted_num` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '该选项投票数',
  `content` varchar(255) NOT NULL DEFAULT '' COMMENT '选项内容',
  `image` varchar(255) NOT NULL DEFAULT '' COMMENT '选项图片',
  PRIMARY KEY (`option_id`),
  KEY `idx_pollid` (`poll_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='投票选项表';

DROP TABLE IF EXISTS `pw_app_poll_thread`;
CREATE TABLE `pw_app_poll_thread` (
  `tid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '帖子ID',
  `poll_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '投票ID',
  `created_userid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '投票发起人',
  PRIMARY KEY (`tid`),
  KEY `idx_pollid` (`poll_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='帖子投票关系表';

DROP TABLE IF EXISTS `pw_app_poll_voter`;
CREATE TABLE `pw_app_poll_voter` (
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '投票参与人ID',
  `poll_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '投票ID',
  `option_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '投票选项ID',
  `created_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '参与投票时间',
  KEY `idx_uid_createdtime` (`uid`,`created_time`),
  KEY `idx_pollid` (`poll_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='用户投票记录表';

DROP TABLE IF EXISTS `pw_attachs`;
CREATE TABLE `pw_attachs` (
  `aid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '附件id',
  `name` varchar(80) NOT NULL DEFAULT '' COMMENT '文件名',
  `type` varchar(15) NOT NULL DEFAULT '' COMMENT '文件类型',
  `size` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '文件大小',
  `path` varchar(80) NOT NULL DEFAULT '' COMMENT '存储路径',
  `ifthumb` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否有缩略图',
  `created_userid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上传人用户id',
  `created_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上传时间',
  `app` varchar(15) NOT NULL DEFAULT '' COMMENT '来自应用类型',
  `app_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '来自应用模块id',
  `descrip` varchar(255) NOT NULL DEFAULT '' COMMENT '文件描述',
  PRIMARY KEY (`aid`),
  KEY `idx_app_appid` (`app`,`app_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='附件表';

DROP TABLE IF EXISTS `pw_attachs_thread`;
CREATE TABLE `pw_attachs_thread` (
  `aid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '附件id',
  `fid` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '所属版块id',
  `tid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '所属帖子id',
  `pid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '所属回复id',
  `name` varchar(80) NOT NULL DEFAULT '' COMMENT '文件名',
  `type` varchar(15) NOT NULL DEFAULT '' COMMENT '文件类型',
  `size` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '文件大小',
  `hits` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '下载数',
  `width` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '图片宽度',
  `height` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '图片高度',
  `path` varchar(80) NOT NULL DEFAULT '' COMMENT '存储路径',
  `ifthumb` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否有缩略图',
  `special` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否售密',
  `cost` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '售密价格',
  `ctype` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '积分类型',
  `created_userid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上传人用户id',
  `created_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上传时间',
  `descrip` varchar(255) NOT NULL DEFAULT '' COMMENT '文件描述',
  PRIMARY KEY (`aid`),
  KEY `idx_createduserid` (`created_userid`),
  KEY `idx_tid_pid` (`tid`,`pid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='帖子附件表';

DROP TABLE IF EXISTS `pw_attachs_thread_buy`;
CREATE TABLE `pw_attachs_thread_buy` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `aid` int(10) unsigned NOT NULL DEFAULT '0',
  `created_userid` int(10) unsigned NOT NULL DEFAULT '0',
  `created_time` int(10) unsigned NOT NULL DEFAULT '0',
  `cost` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `ctype` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_aid` (`aid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='帖子附件购买记录';

DROP TABLE IF EXISTS `pw_attachs_thread_download`;
CREATE TABLE `pw_attachs_thread_download` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增长ID',
  `aid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '附件aid',
  `created_userid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '下载人',
  `created_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '下载时间',
  `cost` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '花费积分数量',
  `ctype` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '花费积分类型',
  PRIMARY KEY (`id`),
  KEY `idx_aid` (`aid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='帖子附件下载记录' ;

DROP TABLE IF EXISTS `pw_attention`;
CREATE TABLE `pw_attention` (
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `touid` int(10) unsigned NOT NULL DEFAULT '0',
  `created_time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`touid`,`uid`),
  KEY `idx_uid_createdtime` (`uid`,`created_time`),
  KEY `idx_touid_createdtime` (`touid`,`created_time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='关注主表';

DROP TABLE IF EXISTS `pw_attention_fresh`;
CREATE TABLE `pw_attention_fresh` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `src_id` int(10) unsigned NOT NULL DEFAULT '0',
  `created_userid` int(10) unsigned NOT NULL DEFAULT '0',
  `created_time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_createduserid_createdtime` (`created_userid`,`created_time`),
  KEY `idx_type_srcid` (`type`,`src_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='新鲜事主表';

DROP TABLE IF EXISTS `pw_attention_fresh_index`;
CREATE TABLE `pw_attention_fresh_index` (
  `fresh_id` int(10) unsigned NOT NULL DEFAULT '0',
  `tid` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`fresh_id`),
  KEY `idx_tid` (`tid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='新鲜事与帖子关联表';

DROP TABLE IF EXISTS `pw_attention_fresh_relations`;
CREATE TABLE `pw_attention_fresh_relations` (
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `fresh_id` int(10) unsigned NOT NULL DEFAULT '0',
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `created_userid` int(10) unsigned NOT NULL DEFAULT '0',
  `created_time` int(10) unsigned NOT NULL DEFAULT '0',
  KEY `idx_uid_createdtime` (`uid`,`created_time`),
  KEY `idx_freshid` (`fresh_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='新鲜事关系表';

DROP TABLE IF EXISTS `pw_attention_recommend_cron`;
CREATE TABLE `pw_attention_recommend_cron` (
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户uid',
  `created_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='可能认识的人更新任务表';

DROP TABLE IF EXISTS `pw_attention_recommend_record`;
CREATE TABLE `pw_attention_recommend_record` (
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户uid',
  `recommend_uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '潜在好友',
  `same_uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '共同好友',
  UNIQUE KEY `idx_uid_puid_suid` (`uid`,`recommend_uid`,`same_uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='共同关注记录表';

DROP TABLE IF EXISTS `pw_attention_recommend_friends`;
CREATE TABLE `pw_attention_recommend_friends` (
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户uid',
  `recommend_uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '推荐好友ID',
  `recommend_username` varchar(15) NOT NULL DEFAULT '' COMMENT '推荐好友用户名',
  `cnt` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '好友数量',
  `recommend_user` text COMMENT '推荐好友信息',
  UNIQUE KEY `idx_uid_recommenduid` (`uid`,`recommend_uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='可能认识的人缓存表';

DROP TABLE IF EXISTS `pw_attention_type`;
CREATE TABLE `pw_attention_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(30) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `idx_uid` (`uid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='关注分类表';

DROP TABLE IF EXISTS `pw_attention_type_relations`;
CREATE TABLE `pw_attention_type_relations` (
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `touid` int(10) unsigned NOT NULL DEFAULT '0',
  `typeid` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`,`touid`,`typeid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='关注分类关系表';

DROP TABLE IF EXISTS `pw_bbsinfo`;
CREATE TABLE `pw_bbsinfo` (
  `id` smallint(3) unsigned NOT NULL auto_increment COMMENT '主键ID',
  `newmember` varchar(15) NOT NULL default '' COMMENT '最新会员',
  `totalmember` mediumint(8) unsigned NOT NULL default '0' COMMENT '会员总数',
  `higholnum` mediumint(8) unsigned NOT NULL default '0' COMMENT '最高在线人数',
  `higholtime` int(10) unsigned NOT NULL default '0' COMMENT '最高在线发生日期',
  `yposts` mediumint(8) unsigned NOT NULL default '0' COMMENT '昨日发帖数',
  `hposts` mediumint(8) unsigned NOT NULL default '0' COMMENT '最高日发帖数',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='论坛信息表';

DROP TABLE IF EXISTS `pw_bbs_forum`;
CREATE TABLE `pw_bbs_forum` (
  `fid` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `parentid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `type` enum('category','forum','sub','sub2') NOT NULL DEFAULT 'forum',
  `issub` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `hassub` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `descrip` text,
  `vieworder` smallint(5) unsigned NOT NULL DEFAULT '0',
  `across` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `manager` varchar(255) NOT NULL DEFAULT '',
  `uppermanager` varchar(255) NOT NULL DEFAULT '',
  `icon` varchar(100) NOT NULL DEFAULT '',
  `logo` varchar(100) NOT NULL DEFAULT '',
  `fup` varchar(30) NOT NULL DEFAULT '',
  `fupname` varchar(255) NOT NULL DEFAULT '',
  `isshow` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `isshowsub` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `newtime` smallint(5) unsigned NOT NULL DEFAULT '0',
  `password` varchar(32) NOT NULL DEFAULT '',
  `allow_visit` varchar(255) NOT NULL DEFAULT '',
  `allow_read` varchar(255) NOT NULL DEFAULT '',
  `allow_post` varchar(255) NOT NULL DEFAULT '',
  `allow_reply` varchar(255) NOT NULL DEFAULT '',
  `allow_upload` varchar(255) NOT NULL DEFAULT '',
  `allow_download` varchar(255) NOT NULL DEFAULT '',
  `created_time` int(10) unsigned NOT NULL DEFAULT '0',
  `created_username` varchar(15) NOT NULL DEFAULT '',
  `created_userid` int(10) unsigned NOT NULL DEFAULT '0',
  `created_ip` int(10) unsigned NOT NULL DEFAULT '0',
  `style` varchar(20) NOT NULL DEFAULT '',
  PRIMARY KEY (`fid`),
  KEY `idx_issub_vieworder` (`issub`,`vieworder`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='版块基本信息表';

DROP TABLE IF EXISTS `pw_bbs_forum_extra`;
CREATE TABLE `pw_bbs_forum_extra` (
  `fid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `seo_description` varchar(255) NOT NULL DEFAULT '',
  `seo_keywords` varchar(255) NOT NULL DEFAULT '',
  `settings_basic` text,
  `settings_credit` text,
  PRIMARY KEY (`fid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='帖子扩展信息表';

DROP TABLE IF EXISTS `pw_bbs_forum_statistics`;
CREATE TABLE `pw_bbs_forum_statistics` (
  `fid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `todayposts` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `todaythreads` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `article` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `posts` int(10) unsigned NOT NULL DEFAULT '0',
  `threads` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `subposts` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `subthreads` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `lastpost_info` char(35) NOT NULL DEFAULT '',
  `lastpost_time` int(10) unsigned NOT NULL DEFAULT '0',
  `lastpost_username` varchar(15) NOT NULL DEFAULT '',
  `lastpost_tid` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`fid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='版块统计表';

DROP TABLE IF EXISTS `pw_bbs_forum_user`;
CREATE TABLE `pw_bbs_forum_user` (
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `fid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `join_time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`,`fid`),
  KEY `idx_fid_jointime` (`fid`,`join_time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='版块会员';

DROP TABLE IF EXISTS `pw_bbs_posts`;
CREATE TABLE `pw_bbs_posts` (
  `pid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `tid` int(10) unsigned NOT NULL DEFAULT '0',
  `disabled` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `ischeck` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `ifshield` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `replies` int(10) unsigned NOT NULL DEFAULT '0',
  `useubb` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `usehtml` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `aids` smallint(5) unsigned NOT NULL DEFAULT '0',
  `rpid` int(10) unsigned NOT NULL DEFAULT '0',
  `subject` varchar(100) NOT NULL DEFAULT '',
  `content` text,
  `like_count` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `sell_count` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `created_time` int(10) unsigned NOT NULL DEFAULT '0',
  `created_username` varchar(15) NOT NULL DEFAULT '',
  `created_userid` int(10) unsigned NOT NULL DEFAULT '0',
  `created_ip` varchar(40) NOT NULL DEFAULT '',
  `reply_notice` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `modified_time` int(10) unsigned NOT NULL DEFAULT '0',
  `modified_username` varchar(15) NOT NULL DEFAULT '',
  `modified_userid` int(10) unsigned NOT NULL DEFAULT '0',
  `modified_ip` varchar(40) NOT NULL DEFAULT '',
  `reminds` varchar(255) NOT NULL DEFAULT '',
  `word_version` smallint(5) unsigned NOT NULL DEFAULT '0',
  `ipfrom` varchar(255) NOT NULL DEFAULT '',
  `manage_remind` varchar(150) NOT NULL DEFAULT '',
  `topped` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`pid`),
  KEY `idx_tid_disabled_createdtime` (`tid`,`disabled`,`created_time`),
  KEY `idx_disabled_createdtime` (`disabled`,`created_time`),
  KEY `idx_createduserid_createdtime` ( `created_userid` , `created_time` )
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='帖子回复表';

DROP TABLE IF EXISTS `pw_bbs_posts_reply`;
CREATE TABLE `pw_bbs_posts_reply` (
  `pid` int(10) unsigned NOT NULL DEFAULT '0',
  `rpid` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`pid`),
  KEY `idx_rpid_pid` (`rpid`,`pid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='回复的回复';

DROP TABLE IF EXISTS `pw_bbs_posts_topped`;
CREATE TABLE `pw_bbs_posts_topped` (
  `pid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '回帖pid',
  `tid` int(10) unsigned NOT NULL COMMENT '帖子tid',
  `floor` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '回帖楼层号',
  `created_userid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '贴内置顶操作人',
  `created_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '贴内置顶时间',
  PRIMARY KEY (`pid`),
  KEY `idx_tid_createdtime` (`tid`,`created_time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='贴内置顶';

DROP TABLE IF EXISTS `pw_bbs_specialsort`;
CREATE TABLE `pw_bbs_specialsort` (
  `sort_type` char(16) NOT NULL DEFAULT '',
  `fid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `tid` int(10) unsigned NOT NULL DEFAULT '0',
  `pid` int(10) unsigned NOT NULL DEFAULT '0',
  `extra` int(10) NOT NULL DEFAULT '0',
  `created_time` int(10) unsigned NOT NULL DEFAULT '0',
  `end_time` int(10) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='帖子特殊排序表';

DROP TABLE IF EXISTS `pw_bbs_threads`;
CREATE TABLE `pw_bbs_threads` (
  `tid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `topic_type` int(10) unsigned NOT NULL DEFAULT '0',
  `subject` varchar(100) NOT NULL DEFAULT '',
  `overtime` int(10) unsigned NOT NULL DEFAULT '0',
  `highlight` varchar(64) NOT NULL DEFAULT '',
  `inspect` varchar(30) NOT NULL DEFAULT '',
  `ifshield` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `digest` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `topped` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `disabled` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `ischeck` tinyint(3) NOT NULL DEFAULT '1',
  `replies` int(10) unsigned NOT NULL DEFAULT '0',
  `hits` int(10) unsigned NOT NULL DEFAULT '0',
  `like_count` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `special` varchar(20) NOT NULL DEFAULT '0',
  `tpcstatus` int(10) unsigned NOT NULL DEFAULT '0',
  `ifupload` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `created_time` int(10) unsigned NOT NULL DEFAULT '0',
  `created_username` varchar(15) NOT NULL DEFAULT '',
  `created_userid` int(10) unsigned NOT NULL DEFAULT '0',
  `created_ip` varchar(40) NOT NULL DEFAULT '',
  `modified_time` int(10) unsigned NOT NULL DEFAULT '0',
  `modified_username` varchar(15) NOT NULL DEFAULT '',
  `modified_userid` int(10) unsigned NOT NULL DEFAULT '0',
  `modified_ip` varchar(40) NOT NULL DEFAULT '',
  `lastpost_time` int(10) unsigned NOT NULL DEFAULT '0',
  `lastpost_userid` int(10) unsigned NOT NULL DEFAULT '0',
  `lastpost_username` varchar(15) NOT NULL DEFAULT '',
  `special_sort` tinyint(4) NOT NULL DEFAULT '0',
  `reply_notice` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `reply_topped` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `thread_status` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`tid`),
  KEY `idx_fid_disabled_lastposttime` (`fid`,`disabled`,`lastpost_time`),
  KEY `idx_disabled_createdtime` (`disabled`,`created_time`),
  KEY `idx_createduserid_createdtime` ( `created_userid` , `created_time` )
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='帖子基本信息表';

DROP TABLE IF EXISTS `pw_bbs_threads_buy`;
CREATE TABLE `pw_bbs_threads_buy` (
  `tid` int(10) unsigned NOT NULL DEFAULT '0',
  `pid` int(10) unsigned NOT NULL DEFAULT '0',
  `created_userid` int(10) unsigned NOT NULL DEFAULT '0',
  `created_time` int(10) unsigned NOT NULL DEFAULT '0',
  `ctype` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `cost` mediumint(8) unsigned NOT NULL DEFAULT '0',
  KEY `idx_tid_pid_createdtime` (`tid`,`pid`,`created_time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='帖子购买记录贴表';

DROP TABLE IF EXISTS `pw_bbs_threads_cate_index`;
CREATE TABLE `pw_bbs_threads_cate_index` (
  `tid` int(10) unsigned NOT NULL DEFAULT '0',
  `cid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `fid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `disabled` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `created_time` int(10) unsigned NOT NULL DEFAULT '0',
  `lastpost_time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`tid`),
  KEY `idx_cid_lastposttime` (`cid`,`lastpost_time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='帖子索引表-分类索引';

DROP TABLE IF EXISTS `pw_bbs_threads_content`;
CREATE TABLE `pw_bbs_threads_content` (
  `tid` int(10) unsigned NOT NULL DEFAULT '0',
  `useubb` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `usehtml` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `aids` smallint(5) unsigned NOT NULL DEFAULT '0',
  `content` text,
  `sell_count` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `reminds` varchar(255) NOT NULL DEFAULT '',
  `word_version` smallint(5) unsigned NOT NULL DEFAULT '0',
  `tags` varchar(255) NOT NULL DEFAULT '',
  `ipfrom` varchar(255) NOT NULL DEFAULT '',
  `manage_remind` varchar(150) NOT NULL DEFAULT '',
  PRIMARY KEY (`tid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='帖子内容表';

DROP TABLE IF EXISTS `pw_bbs_threads_digest_index`;
CREATE TABLE `pw_bbs_threads_digest_index` (
  `tid` int(10) unsigned NOT NULL DEFAULT '0',
  `fid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `disabled` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `cid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `topic_type` int(10) unsigned NOT NULL DEFAULT '0',
  `created_time` int(10) unsigned NOT NULL DEFAULT '0',
  `lastpost_time` int(10) unsigned NOT NULL DEFAULT '0',
  `operator` varchar(15) NOT NULL DEFAULT '',
  `operator_userid` int(10) unsigned NOT NULL DEFAULT '0',
  `operator_time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`tid`),
  KEY `idx_cid_lastposttime` (`cid`,`lastpost_time`),
  KEY `idx_fid_lastposttime_topictype` (`fid`,`lastpost_time`,`topic_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='精华帖子索引表';

DROP TABLE IF EXISTS `pw_bbs_threads_hits`;
CREATE TABLE `pw_bbs_threads_hits` (
  `tid` int(10) unsigned NOT NULL DEFAULT '0',
  `hits` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`tid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8  COMMENT='帖子点击记录表';

DROP TABLE IF EXISTS `pw_bbs_threads_index`;
CREATE TABLE `pw_bbs_threads_index` (
  `tid` int(10) unsigned NOT NULL DEFAULT '0',
  `fid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `disabled` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `created_time` int(10) unsigned NOT NULL DEFAULT '0',
  `lastpost_time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`tid`),
  KEY `idx_lastposttime` (`lastpost_time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='帖子索引表-新帖索引';

DROP TABLE IF EXISTS `pw_bbs_threads_overtime`;
CREATE TABLE `pw_bbs_threads_overtime` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tid` int(10) unsigned NOT NULL DEFAULT '0',
  `m_type` enum('topped','highlight') NOT NULL,
  `overtime` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_tid_mtype` (`tid`,`m_type`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='帖子操作时间表';

DROP TABLE IF EXISTS `pw_bbs_threads_sort`;
CREATE TABLE `pw_bbs_threads_sort` (
  `fid` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '版块ID',
  `tid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '帖子ID',
  `extra` int(10) NOT NULL DEFAULT '0' COMMENT '扩展字段,如置顶1、2、3',
  `sort_type` varchar(20) NOT NULL DEFAULT '' COMMENT '排序类型',
  `created_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `end_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '到期时间',
  PRIMARY KEY (`fid`,`tid`),
  KEY `idx_tid` (`tid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='帖子排序表';

DROP TABLE IF EXISTS `pw_bbs_topic_type`;
CREATE TABLE `pw_bbs_topic_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主题分类ID',
  `fid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '版块ID',
  `name` varchar(255) NOT NULL COMMENT '主题分类名称',
  `parentid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上级主题分类ID',
  `logo` varchar(255) NOT NULL DEFAULT '' COMMENT '主题分类图标',
  `vieworder` tinyint(3) NOT NULL DEFAULT '0' COMMENT '显示排序',
  `issys` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否管理专用(1-是,0-否)',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='主题分类表';

DROP TABLE IF EXISTS `pw_bbs_topped`;
CREATE TABLE `pw_bbs_topped` (
  `fid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `tid` int(10) unsigned NOT NULL DEFAULT '0',
  `pid` int(10) unsigned NOT NULL DEFAULT '0',
  `created_time` int(10) unsigned NOT NULL DEFAULT '0',
  `end_time` int(10) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='置顶帖表';

DROP TABLE IF EXISTS `pw_cache`;
CREATE TABLE `pw_cache` (
  `cache_key` char(32) NOT NULL COMMENT '缓存键名MD5值',
  `cache_value` text COMMENT '缓存值',
  `cache_expire` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '缓存过期时间',
  PRIMARY KEY (`cache_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='缓存表';

DROP TABLE IF EXISTS `pw_common_config`;
CREATE TABLE `pw_common_config` (
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT '配置名称',
  `namespace` varchar(15) NOT NULL DEFAULT 'global' COMMENT '配置命名空间',
  `value` text COMMENT '缓存值',
  `vtype` enum('string','array','object') NOT NULL DEFAULT 'string' COMMENT '配置值类型',
  `description` text COMMENT '配置介绍',
  PRIMARY KEY (`namespace`,`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='网站配置表';

DROP TABLE IF EXISTS `pw_common_cron`;
CREATE TABLE `pw_common_cron` (
  `cron_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '计划任务ID',
  `subject` varchar(50) NOT NULL DEFAULT '' COMMENT '计划任务名称',
  `loop_type` varchar(10) NOT NULL DEFAULT '' COMMENT '循环类型month/week/day/hour/now',
  `loop_daytime` varchar(50) NOT NULL DEFAULT '' COMMENT '循环类型时间（日-时-分）',
  `cron_file` varchar(50) NOT NULL DEFAULT '' COMMENT '计划任务执行文件',
  `isopen` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '是否开启 0 否，1是，2系统任务',
  `created_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '计划任务创建时间',
  `modified_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '计划任务上次执行结束时间',
  `next_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '下一次执行时间',
  PRIMARY KEY (`cron_id`),
  KEY `idx_next_time` (`next_time`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='计划任务表';

DROP TABLE IF EXISTS `pw_common_emotion`;
CREATE TABLE `pw_common_emotion` (
  `emotion_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '表情ID',
  `category_id` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '表情分类',
  `emotion_name` varchar(20) NOT NULL DEFAULT '' COMMENT '表情名称',
  `emotion_folder` varchar(20) NOT NULL DEFAULT '' COMMENT '所属文件夹',
  `emotion_icon` varchar(50) NOT NULL DEFAULT '' COMMENT '表情图标',
  `vieworder` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `isused` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '是否使用',
  PRIMARY KEY (`emotion_id`),
  KEY `idx_catid` (`category_id`),
  KEY `idx_isused` (`isused`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='表情数据表';

DROP TABLE IF EXISTS `pw_common_emotion_category`;
CREATE TABLE `pw_common_emotion_category` (
  `category_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '分类ID',
  `category_name` varchar(20) NOT NULL DEFAULT '' COMMENT '分类名',
  `emotion_folder` varchar(20) NOT NULL DEFAULT '' COMMENT '分类文件夹',
  `emotion_apps` varchar(50) NOT NULL DEFAULT '' COMMENT '能使用的应用',
  `orderid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `isopen` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否使用',
  PRIMARY KEY (`category_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='表情分类表';

DROP TABLE IF EXISTS `pw_common_nav`;
CREATE TABLE `pw_common_nav` (
  `navid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '导航ID',
  `parentid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '导航上级ID',
  `rootid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '导航类ID',
  `type` varchar(32) NOT NULL DEFAULT '' COMMENT '所属类型',
  `sign` varchar(32) NOT NULL DEFAULT '' COMMENT '当前定位标识',
  `name` char(50) NOT NULL DEFAULT '' COMMENT '导航名称',
  `style` char(50) NOT NULL DEFAULT '' COMMENT '导航样式',
  `link` char(100) NOT NULL DEFAULT '' COMMENT '导航链接',
  `alt` char(50) NOT NULL DEFAULT '' COMMENT '链接ALT信息',
  `image` varchar(100) NOT NULL DEFAULT '' COMMENT '导航小图标',
  `target` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否新窗口打开',
  `isshow` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否使用',
  `orderid` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  PRIMARY KEY (`navid`),
  KEY `idx_type` (`type`),
  KEY `idx_rootid` (`rootid`),
  KEY `idx_orderid` (`orderid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='导航表';

DROP TABLE IF EXISTS `pw_common_process`;
CREATE TABLE `pw_common_process` (
  `flag` varchar(20) NOT NULL DEFAULT '' COMMENT '进程标记',
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '进程锁用户',
  `expired_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '过期时间',
  PRIMARY KEY (`flag`(10),`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='进程控制表';

DROP TABLE IF EXISTS `pw_credit_log`;
CREATE TABLE `pw_credit_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ctype` varchar(8) NOT NULL DEFAULT '',
  `affect` int(10) NOT NULL DEFAULT '0',
  `logtype` varchar(40) NOT NULL DEFAULT '',
  `descrip` varchar(255) NOT NULL DEFAULT '',
  `created_userid` int(10) unsigned NOT NULL DEFAULT '0',
  `created_username` varchar(15) NOT NULL DEFAULT '',
  `created_time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_createduserid_createdtime` (`created_userid`,`created_time`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='积分日志表';

DROP TABLE IF EXISTS `pw_credit_log_operate`;
CREATE TABLE `pw_credit_log_operate` (
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `operate` varchar(40) NOT NULL DEFAULT '',
  `num` smallint(5) unsigned NOT NULL DEFAULT '0',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`,`operate`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='积分操作日志表';

DROP TABLE IF EXISTS `pw_design_bak`;
CREATE TABLE `pw_design_bak` (
  `bak_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '备份类型',
  `page_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '备份页面',
  `is_snapshot` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否快照',
  `bak_info` MEDIUMTEXT COMMENT '备份信息',
  PRIMARY KEY (`page_id`,`bak_type`,`is_snapshot`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='门户备份表';

DROP TABLE IF EXISTS `pw_design_image`;
CREATE TABLE `pw_design_image` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '附件ID',
  `path` varchar(80) NOT NULL DEFAULT '' COMMENT '原图片路径',
  `thumb` varchar(80) NOT NULL DEFAULT '' COMMENT '缩略图路径',
  `width` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '缩略图宽',
  `height` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '缩略图高',
  `moduleid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '所属模块',
  `data_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '门户数据ID',
  `sign` varchar(50) NOT NULL DEFAULT '' COMMENT '标签key',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '原图片状态1正常0不正常',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='门户异步缩略图片表';

DROP TABLE IF EXISTS `pw_design_component`;
CREATE TABLE `pw_design_component` (
  `comp_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '元件ID',
  `model_flag` varchar(20) NOT NULL DEFAULT '' COMMENT '元件类型标识',
  `comp_name` varchar(50) NOT NULL DEFAULT '' COMMENT '模版元件名称',
  `comp_tpl` text COMMENT '模版代码',
  `sys_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '系统编号',
  PRIMARY KEY (`comp_id`),
  KEY `idx_modelflag` (`model_flag`(10))
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='模版元件表';

DROP TABLE IF EXISTS `pw_design_cron`;
CREATE TABLE `pw_design_cron` (
  `module_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '模块ID',
  `created_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`module_id`),
  KEY `idx_createdtime` (`created_time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='门户更新队列表';

DROP TABLE IF EXISTS `pw_design_data`;
CREATE TABLE `pw_design_data` (
  `data_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '数据ID',
  `from_type` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '数据来源',
  `from_app` varchar(20) NOT NULL DEFAULT '' COMMENT '来源应用名称',
  `from_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '数据来源ID',
  `module_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '所属模块ID',
  `standard` varchar(255) NOT NULL DEFAULT '' COMMENT '标准标签',
  `style` varchar(255) NOT NULL DEFAULT '' COMMENT '样式',
  `extend_info` text COMMENT '数据内容',
  `data_type` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '数据类型1自动 2固定 3修改',
  `is_edited` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否修改过',
  `is_reservation` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否为预订信息',
  `vieworder` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `start_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '开始时间',
  `end_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '过期时间',
  PRIMARY KEY (`data_id`),
  KEY `idx_moduleid` (`module_id`),
  KEY `idx_vieworder` (`vieworder`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='数据缓存表';

DROP TABLE IF EXISTS `pw_design_module`;
CREATE TABLE `pw_design_module` (
  `module_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '模块ID',
  `page_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '所属页面ID',
  `segment` varchar(50) NOT NULL DEFAULT '' COMMENT '模块所属片段',
  `module_struct` varchar(20) NOT NULL DEFAULT '' COMMENT '模块结构',
  `model_flag` varchar(20) NOT NULL DEFAULT '' COMMENT '所属模块分类',
  `module_name` varchar(50) NOT NULL DEFAULT '' COMMENT '模块名称',
  `module_property` text COMMENT '模块属性',
  `module_title` text COMMENT '模块标题',
  `module_style` text COMMENT '模块样式',
  `module_compid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '模版元件ID',
  `module_tpl` text COMMENT '模块模版代码',
  `module_cache` varchar(255) NOT NULL DEFAULT '' COMMENT '模块更新设置',
  `isused` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否使用',
  `module_type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '模块类型 1 拖曳2 导入3 后台添加',
  PRIMARY KEY (`module_id`),
  KEY `idx_pageid` (`page_id`),
  KEY `idx_moduletype` (`module_type`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='调用模块表';

DROP TABLE IF EXISTS `pw_design_page`;
CREATE TABLE `pw_design_page` (
  `page_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '页面ID',
  `page_type` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '页面类型',
  `page_name` varchar(50) NOT NULL DEFAULT '' COMMENT '页面名称',
  `page_router` varchar(50) NOT NULL DEFAULT '' COMMENT '页面路由信息',
  `page_unique` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '页面唯一标识',
  `is_unique` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '唯一标识的标识',
  `module_ids` TEXT  COMMENT '页面模块',
  `struct_names` TEXT  COMMENT '页面结构',
  `segments` TEXT COMMENT '页面模块片段',
  `design_lock` varchar(50) NOT NULL DEFAULT '' COMMENT '编辑加锁',
  PRIMARY KEY (`page_id`),
  KEY `idx_pagerouter` (`page_router`(10))
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='页面信息表';

DROP TABLE IF EXISTS `pw_design_permissions`;
CREATE TABLE `pw_design_permissions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID标识',
  `design_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '设计类型1页面，2模块',
  `design_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '设计类型的标识ID',
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `permissions` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '权限级别',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `idx_designtype_designid_uid` (`design_type`,`design_id`,`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='设计权限表';

DROP TABLE IF EXISTS `pw_design_portal`;
CREATE TABLE `pw_design_portal` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '标识ID',
  `pagename` varchar(50) NOT NULL DEFAULT '' COMMENT '页面名称',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT 'title信息',
  `keywords` varchar(255) NOT NULL DEFAULT '' COMMENT 'keywords信息',
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT 'description信息',
  `domain` varchar(50) NOT NULL DEFAULT '' COMMENT '二级域名',
  `cover` varchar(255) NOT NULL DEFAULT '' COMMENT '封面图片',
  `isopen` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否使用',
  `header` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否使用公共头',
  `navigate` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否使用公共导航',
  `footer` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否使用公共页脚',
  `template` varchar(50) NOT NULL DEFAULT '' COMMENT '所使用的模版名',
  `style` varchar(255) NOT NULL DEFAULT '' COMMENT '自定义样式',
  `created_uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `created_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `idx_domain` (`domain`(10))
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='自定义页面信息表';

DROP TABLE IF EXISTS `pw_design_push`;
CREATE TABLE `pw_design_push` (
  `push_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '推送ID',
  `push_from_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '来源ID',
  `push_from_model` varchar(20) NOT NULL DEFAULT '' COMMENT '来源应用',
  `module_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '所属模块ID',
  `push_standard` varchar(255) NOT NULL DEFAULT '' COMMENT '标准化标签',
  `push_style` varchar(255) NOT NULL DEFAULT '' COMMENT '样式',
  `push_orderid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `push_extend` text COMMENT '推送内容',
  `created_userid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '推送人ID',
  `author_uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '作者UID',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态显示：0 需要审核：1',
  `neednotice` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否需要发送站内信',
  `check_uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '审核人ID',
  `created_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '推送时间',
  `start_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '开始时间',
  `end_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '过期时间',
  `checked_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '审核时间',
  PRIMARY KEY (`push_id`),
  KEY `idx_end_time` (`end_time`),
  KEY `idx_status` (`status`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='信息推送表';

DROP TABLE IF EXISTS `pw_design_script`;
CREATE TABLE `pw_design_script` (
  `module_id` int(10) unsigned NOT NULL COMMENT '模块ID',
  `token` char(10) NOT NULL COMMENT '加密串',
  `view_times` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '调用次数',
  PRIMARY KEY (`module_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='模块调用管理表';

DROP TABLE IF EXISTS `pw_design_segment`;
CREATE TABLE `pw_design_segment` (
  `segment` varchar(50) NOT NULL DEFAULT '' COMMENT '片段名称',
  `page_id` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '所属页面ID',
  `segment_tpl` MEDIUMTEXT COMMENT '片段代码',
  `segment_struct` MEDIUMTEXT  COMMENT '片段结构代码',
  PRIMARY KEY (`segment`,`page_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='门户片段表';

DROP TABLE IF EXISTS `pw_design_shield`;
CREATE TABLE `pw_design_shield` (
  `shield_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '标识ID',
  `from_app` varchar(20) NOT NULL DEFAULT '' COMMENT '来源应用名称',
  `from_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '来源ID',
  `module_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '被屏蔽的模块',
  `shield_title` varchar(255) NOT NULL DEFAULT '',
  `shield_url` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`shield_id`),
  KEY `idx_formid_formapp` (`from_id`,`from_app`(5))
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='门户模块数据屏蔽表';

DROP TABLE IF EXISTS `pw_design_structure`;
CREATE TABLE `pw_design_structure` (
  `struct_name` varchar(50) NOT NULL DEFAULT '' COMMENT '结构名称',
  `struct_title` text COMMENT '结构标题',
  `struct_style` text COMMENT '结构样式',
 `segment` varchar(50) NOT NULL DEFAULT '' COMMENT '结构所属片段',
  PRIMARY KEY (`struct_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='结构数据表';

DROP TABLE IF EXISTS `pw_domain`;
CREATE TABLE `pw_domain` (
  `domain_key` varchar(100) NOT NULL DEFAULT '' COMMENT '域名标识',
  `domain_type` varchar(15) NOT NULL DEFAULT '' COMMENT '域名类型',
  `domain` varchar(15) NOT NULL DEFAULT '' COMMENT '域名',
  `root` varchar(45) NOT NULL DEFAULT '' COMMENT '根域名',
  `first` char(1) NOT NULL DEFAULT '' COMMENT '域名首字母便于更新',
  `id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '部署应用的id值',
  PRIMARY KEY (`domain_key`),
  KEY `idx_domaintype` (`domain_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='二级域名';

DROP TABLE IF EXISTS `pw_draft`;
CREATE TABLE `pw_draft` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '草稿箱id',
  `created_userid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建人',
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '标题',
  `content` text COMMENT '内容',
  `created_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `idx_uid` (`created_userid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='草稿箱';

DROP TABLE IF EXISTS `pw_frag_template`;
CREATE TABLE `pw_frag_template` (
  `tpl_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `frag_cid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `tpl_name` varchar(50) NOT NULL DEFAULT '',
  `template` text,
  PRIMARY KEY (`tpl_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `pw_hook`;
CREATE TABLE `pw_hook` (
  `name` varchar(50) NOT NULL DEFAULT '',
  `app_id` char(20) NOT NULL DEFAULT '' COMMENT '应用id',
  `app_name` varchar(100) NOT NULL DEFAULT '' COMMENT '应用名称',
  `created_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `modified_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
  `document` text COMMENT '钩子详细信息',
  PRIMARY KEY (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='钩子基本信息表';

DROP TABLE IF EXISTS `pw_hook_inject`;
CREATE TABLE `pw_hook_inject` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `app_id` char(20) NOT NULL DEFAULT '',
  `app_name` varchar(100) NOT NULL DEFAULT '',
  `hook_name` varchar(100) NOT NULL DEFAULT '' COMMENT '钩子名',
  `alias` varchar(100) NOT NULL DEFAULT '' COMMENT '挂载别名',
  `class` varchar(100) NOT NULL DEFAULT '' COMMENT '挂载类',
  `method` varchar(100) NOT NULL DEFAULT '' COMMENT '调用方法',
  `loadway` varchar(20) NOT NULL DEFAULT '' COMMENT '导入方式',
  `expression` varchar(100) NOT NULL DEFAULT '' COMMENT '条件表达式',
  `created_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `modified_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT '描述',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_hook_name` (`hook_name`,`alias`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='钩子挂载方法表';

DROP TABLE IF EXISTS `pw_invite_code`;
CREATE TABLE `pw_invite_code` (
  `code` char(32) NOT NULL DEFAULT '',
  `created_userid` int(10) NOT NULL DEFAULT '0',
  `invited_userid` int(10) NOT NULL DEFAULT '0',
  `ifused` tinyint(1) NOT NULL DEFAULT '0',
  `created_time` int(10) unsigned NOT NULL DEFAULT '0',
  `modified_time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`code`),
  KEY `idx_createduid` (`created_userid`),
  KEY `idx_inviteduid` (`invited_userid`),
  KEY `idx_createdtime` (`created_time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='邀请码记录表';

DROP TABLE IF EXISTS `pw_like_content`;
CREATE TABLE `pw_like_content` (
  `likeid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '喜欢ID',
  `typeid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '喜欢来源类型',
  `fromid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '来源ID',
  `isspecial` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否特殊设置',
  `users` varchar(255) NOT NULL DEFAULT '' COMMENT '喜欢的用户ID',
  `reply_pid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最新回复ID',
  PRIMARY KEY (`likeid`),
  KEY `idx_isspecial` (`isspecial`),
  KEY `idx_typeid_fromid` (`typeid`,`fromid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='喜欢内容表';

DROP TABLE IF EXISTS `pw_like_log`;
CREATE TABLE `pw_like_log` (
  `logid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '标识ID',
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `likeid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '喜欢ID',
  `tagids` varchar(50) NOT NULL DEFAULT '' COMMENT '分类标签ID',
  `created_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`logid`),
  KEY `idx_uid` (`uid`),
  KEY `idx_created_time` (`created_time`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='喜欢记录表';

DROP TABLE IF EXISTS `pw_like_source`;
CREATE TABLE `pw_like_source` (
  `sid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '标识ID',
  `subject` varchar(250) NOT NULL DEFAULT '' COMMENT '标题',
  `sourceUrl` varchar(50) NOT NULL DEFAULT '' COMMENT '来源URL',
  `fromApp` varchar(20) NOT NULL DEFAULT '' COMMENT '来源应用名称',
  `fromid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '来源ID',
  `like_count` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '喜欢数统计',
  PRIMARY KEY (`sid`),
  KEY `idx_fromid` (`fromid`,`fromApp`(10))
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='喜欢app来源表';

DROP TABLE IF EXISTS `pw_like_statistics`;
CREATE TABLE `pw_like_statistics` (
  `signkey` varchar(20) NOT NULL COMMENT '标识key',
  `likeid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '喜欢ID',
  `typeid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '类型ID',
  `fromid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '来源ID',
  `number` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '数量',
  KEY `idx_number` (`number`),
  KEY `idx_signkey` (`signkey`(10))
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='喜欢静态数据表';

DROP TABLE IF EXISTS `pw_like_tag`;
CREATE TABLE `pw_like_tag` (
  `tagid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '标签ID',
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `tagname` varchar(20) NOT NULL COMMENT '标签名',
  `number` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '统计数',
  PRIMARY KEY (`tagid`),
  KEY `idx_uid` (`uid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='喜欢分类表';

DROP TABLE IF EXISTS `pw_like_tag_relations`;
CREATE TABLE `pw_like_tag_relations` (
  `logid` int(10) unsigned NOT NULL COMMENT 'log标识ID',
  `tagid` int(10) unsigned NOT NULL COMMENT '标签ID',
  KEY `idx_logid` (`logid`),
  KEY `idx_tagid` (`tagid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='喜欢分类-喜欢关系表';

DROP TABLE IF EXISTS `pw_link`;
CREATE TABLE `pw_link` (
  `lid` smallint(5) unsigned NOT NULL AUTO_INCREMENT COMMENT '友情链接id',
  `vieworder` tinyint(3) NOT NULL DEFAULT '0' COMMENT '排序',
  `name` varchar(15) NOT NULL DEFAULT '' COMMENT '名称',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '链接',
  `descrip` varchar(255) NOT NULL DEFAULT '' COMMENT '描述',
  `logo` varchar(100) NOT NULL DEFAULT '' COMMENT 'logo',
  `iflogo` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否有logo',
  `ifcheck` tinyint(3) NOT NULL DEFAULT '0' COMMENT '是否审核',
  `contact` varchar(100) NOT NULL DEFAULT '' COMMENT '联系方式',
  PRIMARY KEY (`lid`),
  KEY `idx_ifcheck_vieworder` (`ifcheck`,`vieworder`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='友情链接表';

DROP TABLE IF EXISTS `pw_link_relations`;
CREATE TABLE `pw_link_relations` (
  `lid` smallint(5) unsigned NOT NULL AUTO_INCREMENT COMMENT '友情链接id',
  `typeid` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '分类id',
  PRIMARY KEY (`lid`,`typeid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='友情链接分类关系表';

DROP TABLE IF EXISTS `pw_link_type`;
CREATE TABLE `pw_link_type` (
  `typeid` smallint(5) unsigned NOT NULL AUTO_INCREMENT COMMENT '友情链接分类ID',
  `typename` varchar(6) NOT NULL DEFAULT '' COMMENT '分类名称',
  `vieworder` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '顺序',
  PRIMARY KEY (`typeid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='友情链接分类表';

DROP TABLE IF EXISTS `pw_log`;
CREATE TABLE `pw_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `typeid` tinyint(3) unsigned NOT NULL DEFAULT 0 COMMENT '操作类型ID',
  `created_userid` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '操作者UID',
  `created_time` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '操作时间',
  `created_username` varchar(15) NOT NULL DEFAULT '' COMMENT '操作者名字',
  `operated_uid` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '被操作者UID',
  `operated_username` varchar(15) NOT NULL DEFAULT '' COMMENT '被操作者名字',
  `ip` varchar(40) NOT NULL DEFAULT '' COMMENT '操作IP',
  `fid` smallint(6) unsigned NOT NULL DEFAULT 0 COMMENT '版块ID',
  `tid` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '帖子ID',
  `pid` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '帖子回复ID',
  `extends` varchar(100) NOT NULL DEFAULT '' COMMENT '扩展信息',
  `content` text COMMENT '操作日志内容',
  PRIMARY KEY (`id`),
  KEY `idx_tid_pid` (`tid`, `pid`),
  KEY `idx_fid` (`fid`),
  KEY `idx_created_time` (`created_time`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='前台管理日志表';

DROP TABLE IF EXISTS `pw_log_login`;
CREATE TABLE `pw_log_login` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `uid` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '用户ID',
  `username` varchar(15) NOT NULL DEFAULT '' COMMENT '用户名字',
  `typeid` tinyint(3) unsigned NOT NULL DEFAULT 0 COMMENT '错误类型',
  `created_time` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '尝试时间',
  `ip` varchar(40) NOT NULL DEFAULT '' COMMENT '尝试IP',
  PRIMARY KEY (`id`),
  KEY `idx_username` (`username`),
  KEY `idx_ip` (`ip`),
  KEY `idx_created_time` (`created_time`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='前台用户登录错误日志表';

DROP TABLE IF EXISTS `pw_medal_info`;
CREATE TABLE `pw_medal_info` (
  `medal_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '勋章ID',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '勋章名称',
  `path` varchar(50) NOT NULL DEFAULT '' COMMENT '勋章路径',
  `image` varchar(50) NOT NULL DEFAULT '' COMMENT '勋章图片(系统勋章带路径)',
  `icon` varchar(50) NOT NULL DEFAULT '' COMMENT '勋章图标(系统勋章带路径)',
  `descrip` varchar(255) NOT NULL DEFAULT '' COMMENT '勋章简介',
  `medal_type` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '勋章类型',
  `receive_type` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '勋章获取类型',
  `medal_gids` varchar(50) NOT NULL DEFAULT '' COMMENT '用户组',
  `award_type` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '勋章类型',
  `award_condition` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '勋章条件',
  `expired_days` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '有效期',
  `isopen` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '是否开启',
  `vieworder` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  PRIMARY KEY (`medal_id`),
  KEY `idx_orderid` (`vieworder`),
  KEY `idx_isopen` (`isopen`),
  KEY `idx_award_type` (`award_type`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='勋章信息表';

DROP TABLE IF EXISTS `pw_medal_log`;
CREATE TABLE `pw_medal_log` (
  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '勋章记录ID',
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `medal_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '勋章ID',
  `award_status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '勋章状态：1,进行2，申请3，领取4,显示',
  `created_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `expired_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '过期时间',
  `log_order` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '用户勋章排序',
  PRIMARY KEY (`log_id`),
  UNIQUE KEY `idx_uid_medalid` (`uid`,`medal_id`),
  KEY `idx_expired_time` (`expired_time`),
  KEY `idx_log_order` (`log_order`),
  KEY `idx_awardstatus` (`award_status`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='勋章记录表';

DROP TABLE IF EXISTS `pw_medal_user`;
CREATE TABLE `pw_medal_user` (
  `uid` int(10) unsigned NOT NULL COMMENT '用户ID',
  `medals` varchar(255) NOT NULL DEFAULT '' COMMENT '拥有的勋章ID',
  `counts` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '勋章总数',
  `expired_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最近的过期时间',
  PRIMARY KEY (`uid`),
  KEY `idx_counts` (`counts`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='勋章用户-统计表';

DROP TABLE IF EXISTS `pw_message_config`;
CREATE TABLE `pw_message_config` (
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户uid',
  `privacy` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '关注人才能发私信',
  `notice_types` varchar(255) NOT NULL DEFAULT '' COMMENT '通知忽略类型',
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='消息用户配置表';

DROP TABLE IF EXISTS `pw_message_notices`;
CREATE TABLE `pw_message_notices` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '通知id',
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户uid',
  `typeid` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '类型id',
  `param` int(10) NOT NULL DEFAULT '0' COMMENT '应用类型id',
  `is_read` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否已读',
  `is_ignore` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否忽略',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '标题',
  `extend_params` text COMMENT '扩展内容',
  `created_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `modified_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
  PRIMARY KEY (`id`),
  KEY `idx_uid_read_modifiedtime` (`uid`,`is_read`,`modified_time`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='通知表';

DROP TABLE IF EXISTS `pw_online_guest`;
CREATE TABLE `pw_online_guest` (
  `ip` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户IP',
  `created_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `modify_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `fid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '版块ID',
  `tid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '贴子ID',
  `request` char(50) NOT NULL DEFAULT '' COMMENT '当前请求信息',
  PRIMARY KEY (`ip`,`created_time`),
  KEY `idx_fid` (`fid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='在线-游客表';

DROP TABLE IF EXISTS `pw_online_statistics`;
CREATE TABLE `pw_online_statistics` (
  `signkey` char(20) NOT NULL DEFAULT '' COMMENT '统计标识',
  `number` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '统计数量',
  `created_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`signkey`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='在线-数据统计表';

DROP TABLE IF EXISTS `pw_online_user`;
CREATE TABLE `pw_online_user` (
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `username` char(15) NOT NULL DEFAULT '' COMMENT '用户名',
  `modify_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `created_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `gid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户组',
  `fid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '版块ID',
  `tid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '贴子ID',
  `request` char(50) NOT NULL DEFAULT '' COMMENT '当前请求信息',
  PRIMARY KEY (`uid`),
  KEY `idx_fid` (`fid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='在线-用户表';

DROP TABLE IF EXISTS `pw_pay_order`;
CREATE TABLE `pw_pay_order` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_no` char(30) NOT NULL DEFAULT '',
  `price` decimal(8,2) NOT NULL DEFAULT '0.00',
  `number` smallint(5) unsigned NOT NULL DEFAULT '0',
  `state` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `payemail` varchar(60) NOT NULL DEFAULT '',
  `paymethod` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `paytype` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `buy` int(10) unsigned NOT NULL DEFAULT '0',
  `created_userid` int(10) unsigned NOT NULL DEFAULT '0',
  `created_time` int(10) unsigned NOT NULL DEFAULT '0',
  `extra_1` int(10) unsigned NOT NULL DEFAULT '0',
  `extra_2` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `idx_orderno` (`order_no`),
  KEY `idx_createduserid` (`created_userid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='支付订单表';

DROP TABLE IF EXISTS `pw_recycle_reply`;
CREATE TABLE `pw_recycle_reply` (
  `pid` int(10) unsigned NOT NULL DEFAULT '0',
  `tid` int(10) unsigned NOT NULL DEFAULT '0',
  `fid` int(10) unsigned NOT NULL DEFAULT '0',
  `operate_time` int(10) unsigned NOT NULL DEFAULT '0',
  `operate_username` varchar(15) NOT NULL,
  `reason` text,
  PRIMARY KEY (`pid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='回复回收站';

DROP TABLE IF EXISTS `pw_recycle_topic`;
CREATE TABLE `pw_recycle_topic` (
  `tid` int(10) unsigned NOT NULL DEFAULT '0',
  `fid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `operate_time` int(10) unsigned NOT NULL DEFAULT '0',
  `operate_username` varchar(15) NOT NULL,
  `reason` text,
  PRIMARY KEY (`tid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='主题回收站';

DROP TABLE IF EXISTS `pw_remind`;
CREATE TABLE `pw_remind` (
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户uid',
  `touid` varchar(255) NOT NULL DEFAULT '' COMMENT '最近提醒人',
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='@最近提醒表';

DROP TABLE IF EXISTS `pw_report`;
CREATE TABLE `pw_report` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '举报id',
  `type` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '举报类型',
  `type_id` int(10) NOT NULL DEFAULT '0' COMMENT '举报应用id',
  `content` varchar(100) NOT NULL DEFAULT '' COMMENT '内容',
  `content_url` varchar(255) NOT NULL DEFAULT '' COMMENT '内容链接',
  `author_userid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '作者',
  `created_userid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '举报人',
  `created_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '举报时间',
  `reason` varchar(255) NOT NULL DEFAULT '' COMMENT '原因',
  `ifcheck` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否审核',
  `operate_userid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '举报处理人',
  `operate_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '举报处理时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='举报表';

DROP TABLE IF EXISTS `pw_seo`;
CREATE TABLE `pw_seo` (
  `mod` varchar(15) NOT NULL DEFAULT '' COMMENT '模块名',
  `page` varchar(20) NOT NULL DEFAULT '' COMMENT '页面名',
  `param` varchar(20) NOT NULL DEFAULT '' COMMENT '参数名',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '名称',
  `keywords` varchar(255) NOT NULL DEFAULT '' COMMENT '关键词',
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT '描述',
  PRIMARY KEY (`mod`,`page`,`param`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='seo';

DROP TABLE IF EXISTS `pw_space`;
CREATE TABLE `pw_space` (
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `space_name` varchar(50) NOT NULL DEFAULT '' COMMENT '空间名称',
  `space_descrip` varchar(255) NOT NULL DEFAULT '' COMMENT '空间描述',
  `space_domain` varchar(20) NOT NULL DEFAULT '' COMMENT '二级哉域名',
  `space_style` varchar(20) NOT NULL DEFAULT '' COMMENT '空间风格',
  `back_image` varchar(255) NOT NULL DEFAULT '' COMMENT '背景设置',
  `visit_count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '访问统计',
  `visitors` TEXT  COMMENT '来访者',
  `tovisitors` TEXT  COMMENT '我的访问记录',
  `space_privacy` tinyint(4) NOT NULL DEFAULT '0' COMMENT '隐私等级',
  PRIMARY KEY (`uid`),
  KEY `idx_space_domain` (`space_domain`(10))
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='个人空间信息表';

DROP TABLE IF EXISTS `pw_space_domain`;
CREATE TABLE `pw_space_domain` (
  `domain` varchar(15) NOT NULL DEFAULT '' COMMENT '空间域名',
  `uid` INT(10) NOT NULL DEFAULT 0 COMMENT '用户id',
  PRIMARY KEY  (`domain`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT = '空间域名表';

DROP TABLE IF EXISTS `pw_style`;
CREATE TABLE `pw_style` (
  `app_id` char(20) NOT NULL DEFAULT '',
  `iscurrent` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否默认',
  `style_type` char(10) NOT NULL DEFAULT '' COMMENT '风格类型',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '名称',
  `alias` varchar(100) NOT NULL DEFAULT '' COMMENT '应用别名',
  `logo` varchar(100) NOT NULL DEFAULT '' COMMENT '图标',
  `author_name` varchar(30) NOT NULL DEFAULT '' COMMENT '作者名',
  `author_icon` varchar(100) NOT NULL DEFAULT '' COMMENT '作者头像',
  `author_email` varchar(200) NOT NULL DEFAULT '' COMMENT '作者email',
  `website` varchar(200) NOT NULL DEFAULT '' COMMENT '作者网站',
  `version` varchar(50) NOT NULL DEFAULT '' COMMENT '应用版本',
  `pwversion` varchar(50) NOT NULL DEFAULT '',
  `created_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `modified_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT '描述',
  PRIMARY KEY (`app_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='主题风格表';

DROP TABLE IF EXISTS `pw_tag`;
CREATE TABLE `pw_tag` (
  `tag_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '话题id',
  `parent_tag_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上级话题id',
  `tag_name` char(60) NOT NULL DEFAULT '' COMMENT '话题名称',
  `tag_logo` varchar(255) NOT NULL DEFAULT '' COMMENT '话题logo',
  `ifhot` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '允许热门',
  `excerpt` varchar(255) NOT NULL DEFAULT '' COMMENT '摘要',
  `content_count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '内容数',
  `attention_count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '关注数',
  `hits` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '点击数',
  `created_userid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建人',
  `created_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `iflogo` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否有logo',
  `seo_title` varchar(255) NOT NULL DEFAULT '' COMMENT 'seo标题',
  `seo_description` varchar(255) NOT NULL DEFAULT '' COMMENT 'seo描述',
  `seo_keywords` varchar(255) NOT NULL DEFAULT '' COMMENT 'seo关键字',
  PRIMARY KEY (`tag_id`),
  UNIQUE KEY `idx_tagname` (`tag_name`),
  KEY `idx_parenttagid` (`parent_tag_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='话题表';

DROP TABLE IF EXISTS `pw_tag_attention`;
CREATE TABLE `pw_tag_attention` (
  `uid` int(10) unsigned NOT NULL COMMENT '用户uid',
  `tag_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '话题id',
  `last_read_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '关注时间',
  PRIMARY KEY (`tag_id`,`uid`),
  KEY `idx_uid_lastreadtime` (`uid`,`last_read_time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='话题关注表';

DROP TABLE IF EXISTS `pw_tag_category`;
CREATE TABLE `pw_tag_category` (
  `category_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT COMMENT '分类id',
  `category_name` char(20) NOT NULL DEFAULT '' COMMENT '分类名称',
  `alias` varchar(15) NOT NULL DEFAULT '' COMMENT '别名',
  `vieworder` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '顺序',
  `tag_count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '话题数',
  `seo_title` varchar(255) NOT NULL DEFAULT '' COMMENT 'seo标题',
  `seo_description` varchar(255) NOT NULL DEFAULT '' COMMENT 'seo描述',
  `seo_keywords` varchar(255) NOT NULL DEFAULT '' COMMENT 'seo关键字',
  PRIMARY KEY (`category_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='话题分类表';

DROP TABLE IF EXISTS `pw_tag_category_relation`;
CREATE TABLE `pw_tag_category_relation` (
  `tag_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '话题id',
  `category_id` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '分类id',
  PRIMARY KEY (`category_id`,`tag_id`),
  KEY `idx_tagid` (`tag_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='话题分类关系表';

DROP TABLE IF EXISTS `pw_tag_record`;
CREATE TABLE `pw_tag_record` (
  `tag_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '话题id',
  `is_reply` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否回复',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  KEY `idx_tagid` (`tag_id`),
  KEY `idx_updatetime` (`update_time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='话题排行统计表';

DROP TABLE IF EXISTS `pw_tag_relation`;
CREATE TABLE `pw_tag_relation` (
  `tag_id` int(10) unsigned NOT NULL COMMENT '话题id',
  `content_tag_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '内容id',
  `type_id` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '应用分类id',
  `param_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '应用id',
  `ifcheck` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '是否审核',
  `created_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`type_id`,`param_id`,`content_tag_id`),
  KEY `idx_tagid_typeid` (`tag_id`,`type_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='话题内容关系表';

DROP TABLE IF EXISTS `pw_task`;
CREATE TABLE `pw_task` (
  `taskid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '任务ID',
  `pre_task` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '前置任务ID',
  `is_auto` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否是自动任务标识',
  `is_display_all` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否显示给所有用户',
  `view_order` smallint(6) NOT NULL DEFAULT '0' COMMENT '顺序',
  `is_open` tinyint(3) NOT NULL DEFAULT '0' COMMENT '是否开启状态',
  `start_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '开始的时间',
  `end_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '结束时间',
  `period` smallint(6) NOT NULL DEFAULT '0' COMMENT '是否是周期任务',
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '标题',
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT '描述',
  `icon` varchar(200) NOT NULL DEFAULT '' COMMENT '图标路径',
  `user_groups` varchar(255) NOT NULL DEFAULT '-1' COMMENT '可以申请任务的用户组',
  `reward` varchar(255) NOT NULL DEFAULT '' COMMENT '奖励',
  `conditions` varchar(255) NOT NULL DEFAULT '' COMMENT '完成条件',
  PRIMARY KEY (`taskid`),
  KEY `idx_pretask` (`pre_task`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='任务';

DROP TABLE IF EXISTS `pw_task_cache`;
CREATE TABLE `pw_task_cache` (
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `task_ids` varchar(200) NOT NULL DEFAULT '' COMMENT '该用户完成任务的最后ID记录及周期任务ID记录',
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='任务缓存';

DROP TABLE IF EXISTS `pw_task_group`;
CREATE TABLE `pw_task_group` (
  `taskid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '任务ID',
  `gid` int(10) NOT NULL DEFAULT '0' COMMENT '用户组ID',
  `is_auto` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否是周期任务',
  `end_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '结束时间',
  PRIMARY KEY (`gid`,`taskid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='任务索引';

DROP TABLE IF EXISTS `pw_task_user`;
CREATE TABLE `pw_task_user` (
  `taskid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '任务ID',
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `task_status` tinyint(3) NOT NULL DEFAULT '0' COMMENT '任务状态',
  `is_period` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否是周期任务',
  `step` varchar(100) NOT NULL DEFAULT '' COMMENT '任务完成的进度信息',
  `created_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '申请任务时间',
  `finish_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '完成任务时间',
  PRIMARY KEY (`uid`,`taskid`),
  KEY `idx_uid_taskstatus` (`uid`,`task_status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='任务用户';

DROP TABLE IF EXISTS `pw_user`;
CREATE TABLE `pw_user` (
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `username` varchar(15) NOT NULL DEFAULT '' COMMENT '用户名字',
  `email` varchar(40) NOT NULL DEFAULT '' COMMENT 'Email地址',
  `password` char(32) NOT NULL DEFAULT '' COMMENT '随机密码',
  `status` smallint(6) unsigned NOT NULL DEFAULT '0' COMMENT '状态',
  `groupid` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '当前用户组ID',
  `memberid` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '会员组ID',
  `regdate` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '注册时间',
  `realname` varchar(50) NOT NULL DEFAULT '' COMMENT '真实姓名',
  `groups` varchar(255) NOT NULL DEFAULT '' COMMENT '用户附加组的ID缓存字段',
  PRIMARY KEY (`uid`),
  UNIQUE KEY `idx_username` (`username`),
  KEY `idx_email` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='用户基本表';

DROP TABLE IF EXISTS `pw_user_active_code`;
CREATE TABLE `pw_user_active_code` (
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `email` varchar(80) NOT NULL DEFAULT '' COMMENT 'Email地址',
  `code` varchar(10) NOT NULL DEFAULT '' COMMENT '激活码',
  `send_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '发送时间',
  `active_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '激活时间',
  `typeid` tinyint(1) NOT NULL DEFAULT '0' COMMENT '类型-注册邮箱激活码或是找回密码',
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='用户邮箱激活码表';

DROP TABLE IF EXISTS `pw_user_ban`;
CREATE TABLE `pw_user_ban` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `typeid` char(20) NOT NULL DEFAULT '' COMMENT '类型',
  `fid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '版块ID---未用',
  `end_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '结束时间',
  `created_userid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '执行者ID',
  `created_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '开始时间',
  `reason` varchar(80) NOT NULL DEFAULT '' COMMENT '操作原因',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_uid_typeid_fid` (`uid`,`typeid`,`fid`),
  KEY `idx_createdUid` (`created_userid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='用户禁止记录表';

DROP TABLE IF EXISTS `pw_user_behavior`;
CREATE TABLE `pw_user_behavior` (
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户UID',
  `behavior` char(20) NOT NULL DEFAULT '' COMMENT '行为标识',
  `number` int(10) NOT NULL DEFAULT '0' COMMENT '行为统计',
  `expired_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '过期时间',
  `extend_info` varchar(255) NOT NULL DEFAULT '' COMMENT '额外信息',
  PRIMARY KEY (`uid`,`behavior`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='用户行为统计表';

DROP TABLE IF EXISTS `pw_user_belong`;
CREATE TABLE `pw_user_belong` (
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `gid` mediumint(8) NOT NULL DEFAULT '0' COMMENT '用户组ID',
  `endtime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '有效期',
  UNIQUE KEY `idx_uid_gid` (`uid`,`gid`),
  KEY `idx_gid` (`gid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='用户所属用户组表';

DROP TABLE IF EXISTS `pw_user_data`;
CREATE TABLE `pw_user_data` (
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `lastvisit` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后访问时间',
  `lastloginip` varchar(20) NOT NULL DEFAULT '' COMMENT '最后登录IP',
  `lastpost` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后发帖时间',
  `lastactivetime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后活动时间',
  `onlinetime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '在线时长',
  `trypwd` varchar(16) NOT NULL DEFAULT '' COMMENT '尝试的登录错误信息，trydate|trynum',
  `postcheck` varchar(16) NOT NULL DEFAULT '' COMMENT '发帖检查',
  `postnum` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '发帖数',
  `digest` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '精华数',
  `todaypost` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '今天发帖数',
  `todayupload` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '今日上传个数',
  `findpwd` varchar(26) NOT NULL DEFAULT '' COMMENT '找回密码尝试错误次数,trydate|trynum',
  `follows` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '关注数',
  `fans` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '粉丝数',
  `message_tone` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '是否有新消息',
  `messages` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '私信数',
  `notices` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '消息数',
  `likes` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '喜欢次数',
  `punch` varchar(200) NOT NULL DEFAULT '' COMMENT '打卡相关',
  `credit1` int(10) NOT NULL DEFAULT '0' COMMENT '积分字段1',
  `credit2` int(10) NOT NULL DEFAULT '0' COMMENT '积分字段2',
  `credit3` int(10) NOT NULL DEFAULT '0' COMMENT '积分字段3',
  `credit4` int(10) NOT NULL DEFAULT '0' COMMENT '积分字段4',
  `credit5` int(10) NOT NULL DEFAULT '0',
  `credit6` int(10) NOT NULL DEFAULT '0',
  `credit7` int(10) NOT NULL DEFAULT '0',
  `credit8` int(10) NOT NULL DEFAULT '0',
  `join_forum` varchar(255) NOT NULL DEFAULT '' COMMENT '加入的版块',
  `recommend_friend` varchar(255) NOT NULL DEFAULT '' COMMENT '推荐朋友',
  `last_credit_affect_log` varchar(255) NOT NULL DEFAULT '' COMMENT '最后积分变动内容',
  `medal_ids` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='用户扩展数据表';

DROP TABLE IF EXISTS `pw_user_education`;
CREATE TABLE `pw_user_education` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `schoolid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '学校ID',
  `degree` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '学历ID',
  `start_time` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '时间',
  PRIMARY KEY (`id`),
  KEY `idx_uid_startTime` (`uid`,`start_time`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='用户教育信息表';

DROP TABLE IF EXISTS `pw_user_groups`;
CREATE TABLE `pw_user_groups` (
  `gid` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户组ID',
  `name` varchar(64) NOT NULL DEFAULT '' COMMENT '用户组名字',
  `type` enum('default','member','system','special','vip') NOT NULL COMMENT '用户组类型',
  `image` varchar(32) NOT NULL DEFAULT '' COMMENT '用户组图标',
  `points` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户组需要的点',
  PRIMARY KEY (`gid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='用户组表';

DROP TABLE IF EXISTS `pw_user_info`;
CREATE TABLE `pw_user_info` (
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `gender` tinyint(1) NOT NULL DEFAULT '0' COMMENT '性别',
  `byear` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '出生年份',
  `bmonth` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '出生月份',
  `bday` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '出生日期',
  `location` int(10) NOT NULL DEFAULT '0' COMMENT '居住地ID',
  `location_text` varchar(100) NOT NULL DEFAULT '',
  `hometown` int(10) NOT NULL DEFAULT '0' COMMENT '家庭ID',
  `hometown_text` varchar(100) NOT NULL DEFAULT '',
  `homepage` varchar(75) NOT NULL DEFAULT '' COMMENT '主页',
  `qq` varchar(12) NOT NULL DEFAULT '' COMMENT 'QQ 号码',
  `msn` varchar(40) NOT NULL DEFAULT '' COMMENT 'MSN号码',
  `aliww` varchar(30) NOT NULL DEFAULT '' COMMENT '阿里旺旺号码',
  `mobile` varchar(16) NOT NULL DEFAULT '' COMMENT '手机号码',
  `alipay` varchar(30) NOT NULL DEFAULT '' COMMENT '支付宝帐号',
  `bbs_sign` text COMMENT '个性签名',
  `profile` text COMMENT '个人简介',
  `regreason` varchar(200) NOT NULL DEFAULT '' COMMENT '注册原因',
  `telphone` varchar(20) NOT NULL DEFAULT '' COMMENT '电话号码',
  `address` varchar(100) NOT NULL DEFAULT '' COMMENT '邮寄地址',
  `zipcode` varchar(10) NOT NULL DEFAULT '' COMMENT '邮政编码',
  `secret` varchar(500) NOT NULL DEFAULT '' COMMENT '隐私设置',
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='用户扩展基本信息表二';

DROP TABLE IF EXISTS `pw_user_login_ip_recode`;
CREATE TABLE `pw_user_login_ip_recode` (
  `ip` varchar(20) NOT NULL DEFAULT '' COMMENT 'IP地址',
  `last_time` varchar(10) NOT NULL DEFAULT '' COMMENT '最后访问时间',
  `error_count` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '错误次数',
  PRIMARY KEY (`ip`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='用户IP登录记录表-用户IP登录限制';

DROP TABLE IF EXISTS `pw_user_mobile`;
CREATE TABLE `pw_user_mobile` (
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户uid',
  `mobile` bigint(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户手机号码',
  PRIMARY KEY (`uid`),
  UNIQUE KEY `idx_mobile` (`mobile`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='用户手机验证表';

DROP TABLE IF EXISTS `pw_user_mobile_verify`;
CREATE TABLE `pw_user_mobile_verify` (
  `mobile` bigint(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户手机号码',
  `code` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '验证码',
  `expired_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '过期时间',
  `number` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`mobile`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='用户手机验证表';

DROP TABLE IF EXISTS `pw_user_permission_groups`;
CREATE TABLE `pw_user_permission_groups` (
  `gid` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '用户组ID',
  `rkey` varchar(64) NOT NULL DEFAULT '' COMMENT '权限点',
  `rtype` enum('basic','system','systemforum') NOT NULL DEFAULT 'basic' COMMENT '权限类型',
  `rvalue` text COMMENT '权限值',
  `vtype` enum('string','array') NOT NULL DEFAULT 'string' COMMENT '权限值类型',
  PRIMARY KEY (`gid`,`rkey`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='用户组权限表';

DROP TABLE IF EXISTS `pw_user_register_check`;
CREATE TABLE `pw_user_register_check` (
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `ifchecked` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否已经审核',
  `ifactived` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否已经激活',
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='用户注册审核记录表';

DROP TABLE IF EXISTS `pw_user_register_ip`;
CREATE TABLE `pw_user_register_ip` (
  `ip` varchar(20) NOT NULL DEFAULT '' COMMENT 'IP地址',
  `last_regdate` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后注册时间',
  `num` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '次数',
  PRIMARY KEY (`ip`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='登录的IP统计表';

DROP TABLE IF EXISTS `pw_user_tag`;
CREATE TABLE `pw_user_tag` (
  `tag_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '个性标签ID',
  `name` varchar(20) NOT NULL DEFAULT '' COMMENT '个性标签名字',
  `ifhot` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否是热门标签',
  `used_count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '被使用次数',
  PRIMARY KEY (`tag_id`),
  UNIQUE KEY `idx_name` (`name`),
  KEY `idx_usedcount` (`used_count`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='用户个人标签基本信息表';

DROP TABLE IF EXISTS `pw_user_tag_relation`;
CREATE TABLE `pw_user_tag_relation` (
  `tag_id` int(10) unsigned NOT NULL COMMENT '个性标签ID',
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `created_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`uid`,`tag_id`),
  KEY `idx_createdtime` (`created_time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='用户个人标签与用户的关系表';

DROP TABLE IF EXISTS `pw_user_work`;
CREATE TABLE `pw_user_work` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `company` varchar(100) NOT NULL DEFAULT '' COMMENT '公司名字',
  `starty` smallint(4) NOT NULL DEFAULT '0' COMMENT '开始年份',
  `endy` smallint(4) NOT NULL DEFAULT '0' COMMENT '结束年份',
  `startm` tinyint(2) NOT NULL DEFAULT '0' COMMENT '开始月份',
  `endm` tinyint(2) NOT NULL DEFAULT '0' COMMENT '结束月份',
  PRIMARY KEY (`id`),
  KEY `idx_uid_starty_startm` (`uid`,`starty`,`startm`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='用户工作经历表';

DROP TABLE IF EXISTS `pw_upgrade_log`;
CREATE TABLE `pw_upgrade_log` (
  `id` varchar(25) NOT NULL DEFAULT '' COMMENT '主键id',
  `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '日志类型',
  `data` text COMMENT '内容',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='更新日志表';

DROP TABLE IF EXISTS `pw_weibo`;
CREATE TABLE `pw_weibo` (
  `weibo_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `src_id` int(10) unsigned NOT NULL DEFAULT '0',
  `content` text,
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `comments` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `extra` text,
  `like_count` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `created_userid` int(10) unsigned NOT NULL DEFAULT '0',
  `created_username` varchar(15) NOT NULL DEFAULT '',
  `created_time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`weibo_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='微薄表';

DROP TABLE IF EXISTS `pw_weibo_comment`;
CREATE TABLE `pw_weibo_comment` (
  `comment_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weibo_id` int(10) unsigned NOT NULL DEFAULT '0',
  `content` text,
  `extra` text,
  `created_userid` int(10) unsigned NOT NULL DEFAULT '0',
  `created_username` varchar(15) NOT NULL DEFAULT '',
  `created_time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`comment_id`),
  KEY `idx_weiboid_createdtime` (`weibo_id`,`created_time`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='微薄评论表';

DROP TABLE IF EXISTS `pw_windid_application`;
CREATE TABLE `pw_windid_application` (
  `app_id` char(20) NOT NULL DEFAULT '' COMMENT '应用id',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '名称',
  `alias` varchar(100) NOT NULL DEFAULT '' COMMENT '别名',
  `logo` varchar(100) NOT NULL DEFAULT '' COMMENT '应用logo',
  `author_name` varchar(30) NOT NULL DEFAULT '' COMMENT '作者名',
  `author_icon` varchar(100) NOT NULL DEFAULT '' COMMENT '作者头像',
  `author_email` varchar(200) NOT NULL DEFAULT '' COMMENT '作者email',
  `website` varchar(200) NOT NULL DEFAULT '' COMMENT '开发者网站',
  `version` varchar(50) NOT NULL DEFAULT '' COMMENT '应用版本',
  `pwversion` varchar(50) NOT NULL DEFAULT '',
  `created_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `modified_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT '描述',
  PRIMARY KEY (`app_id`),
  UNIQUE KEY `alias` (`alias`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='本地应用信息表';

DROP TABLE IF EXISTS `pw_windid_application_log`;
CREATE TABLE `pw_windid_application_log` (
  `app_id` char(20) NOT NULL DEFAULT '' COMMENT '应用id',
  `log_type` char(10) NOT NULL DEFAULT '' COMMENT '日志类型',
  `data` text COMMENT '日志内容',
  `created_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `modified_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
  UNIQUE KEY `app_id` (`app_id`,`log_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='应用安装日志表';

DROP TABLE IF EXISTS `pw_windid_hook`;
CREATE TABLE `pw_windid_hook` (
  `name` varchar(50) NOT NULL DEFAULT '',
  `app_id` char(20) NOT NULL DEFAULT '' COMMENT '应用id',
  `app_name` varchar(100) NOT NULL DEFAULT '' COMMENT '应用名称',
  `created_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `modified_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
  `document` text COMMENT '钩子详细信息',
  PRIMARY KEY (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='钩子基本信息表';

DROP TABLE IF EXISTS `pw_windid_hook_inject`;
CREATE TABLE `pw_windid_hook_inject` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `app_id` char(20) NOT NULL DEFAULT '',
  `app_name` varchar(100) NOT NULL DEFAULT '',
  `hook_name` varchar(100) NOT NULL DEFAULT '' COMMENT '钩子名',
  `alias` varchar(100) NOT NULL DEFAULT '' COMMENT '挂载别名',
  `class` varchar(100) NOT NULL DEFAULT '' COMMENT '挂载类',
  `method` varchar(100) NOT NULL DEFAULT '' COMMENT '调用方法',
  `loadway` varchar(20) NOT NULL DEFAULT '' COMMENT '导入方式',
  `expression` varchar(100) NOT NULL DEFAULT '' COMMENT '条件表达式',
  `created_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `modified_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT '描述',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_hook_name` (`hook_name`,`alias`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='钩子挂载方法表';

DROP TABLE IF EXISTS `pw_windid_admin_auth`;
CREATE TABLE `pw_windid_admin_auth` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `uid` int(10) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `username` varchar(15) NOT NULL DEFAULT '' COMMENT '用户名',
  `roles` varchar(255) NOT NULL DEFAULT '' COMMENT '角色',
  `created_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `modified_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后修改时间',
  PRIMARY KEY (`id`),
  KEY `idx_uid` (`uid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='用户权限角色表';

DROP TABLE IF EXISTS `pw_windid_admin_config`;
CREATE TABLE `pw_windid_admin_config` (
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT '配置名称',
  `namespace` varchar(15) NOT NULL DEFAULT 'global' COMMENT '配置命名空间',
  `value` text COMMENT '缓存值',
  `vtype` enum('string','array','object') NOT NULL DEFAULT 'string' COMMENT '配置值类型',
  `description` text COMMENT '配置介绍',
  PRIMARY KEY (`namespace`,`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='网站配置表';

DROP TABLE IF EXISTS `pw_windid_admin_custom`;
CREATE TABLE `pw_windid_admin_custom` (
  `username` varchar(15) NOT NULL,
  `custom` text COMMENT '常用菜单项',
  PRIMARY KEY (`username`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='后台常用菜单表';

DROP TABLE IF EXISTS `pw_windid_admin_role`;
CREATE TABLE `pw_windid_admin_role` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `name` varchar(15) NOT NULL DEFAULT '' COMMENT '角色名',
  `auths` text COMMENT '权限点',
  `created_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `modified_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后修改时间',
  PRIMARY KEY (`id`),
  KEY `idx_name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='后台用户角色表';


DROP TABLE IF EXISTS `pw_windid_app`;
CREATE TABLE `pw_windid_app` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL DEFAULT '',
  `siteurl` varchar(128) NOT NULL DEFAULT '',
  `siteip` varchar(20) NOT NULL DEFAULT '',
  `secretkey` varchar(50) NOT NULL DEFAULT '',
  `apifile` varchar(128) NOT NULL DEFAULT '' COMMENT '通知接收文件',
  `charset`  varchar(16) NOT NULL DEFAULT '' COMMENT '客户端编码',
  `issyn` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `isnotify` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='应用数据表';

DROP TABLE IF EXISTS `pw_windid_area`;
CREATE TABLE `pw_windid_area` (
  `areaid` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '地址ID',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '地区名字',
  `joinname` varchar(100) NOT NULL DEFAULT '' COMMENT '地区路径的cache地址',
  `parentid` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '上级路径ID',
  `vieworder` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '顺序',
  PRIMARY KEY (`areaid`),
  KEY `idx_name` (`name`),
  KEY `idx_parentid_vieworder` (`parentid`,`vieworder`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='统一地区库';

DROP TABLE IF EXISTS `pw_windid_config`;
CREATE TABLE `pw_windid_config` (
  `name` varchar(30) NOT NULL COMMENT '配置名字',
  `namespace` varchar(15) NOT NULL DEFAULT 'global' COMMENT '配置命名空间',
  `value` text COMMENT '值',
  `vtype` enum('string','array','object') NOT NULL DEFAULT 'string' COMMENT '配置值类型',
  `descrip` text COMMENT '描述',
  PRIMARY KEY (`namespace`,`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='windid配置表';

DROP TABLE IF EXISTS `pw_windid_message`;
CREATE TABLE `pw_windid_message` (
  `message_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '消息id',
  `from_uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '发信人',
  `to_uid` int(10) unsigned NOT NULL DEFAULT '0',
  `content` text COMMENT '内容',
  `created_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '时间',
  PRIMARY KEY (`message_id`),
  KEY `idx_fromuid_touid` (`from_uid`,`to_uid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='消息内容表';

DROP TABLE IF EXISTS `pw_windid_message_dialog`;
CREATE TABLE `pw_windid_message_dialog` (
  `dialog_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '对话id',
  `to_uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '收信人',
  `from_uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '发信人',
  `unread_count` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '未读数',
  `message_count` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '总对话数量',
  `last_message` text COMMENT '最新对话',
  `modified_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
  PRIMARY KEY (`dialog_id`),
  UNIQUE KEY `idx_touid_fromuid` (`to_uid`,`from_uid`),
  KEY `idx_touid_modifiedtime` (`to_uid`,`modified_time`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='消息对话表';

DROP TABLE IF EXISTS `pw_windid_message_relation`;
CREATE TABLE `pw_windid_message_relation` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '关系id',
  `dialog_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '对话id',
  `message_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '私信id',
  `is_read` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否已读',
  `is_send` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否为发送者私信',
  PRIMARY KEY (`id`),
  KEY `idx_dialogid` (`dialog_id`),
  KEY `idx_messageid` ( `message_id` ),
  KEY `idx_isread` ( `is_read` ),
  KEY `idx_issend` ( `is_send` )

) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='消息关系表';

DROP TABLE IF EXISTS `pw_windid_notify`;
CREATE TABLE `pw_windid_notify` (
  `nid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `appid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `operation` varchar(50) NOT NULL DEFAULT '',
  `param` text COMMENT '消息参数',
  `timestamp` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`nid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='通知队列表';

DROP TABLE IF EXISTS `pw_windid_notify_log`;
CREATE TABLE `pw_windid_notify_log` (
  `logid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nid` int(10) unsigned NOT NULL DEFAULT '0',
  `appid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `complete` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `send_num` int(10) unsigned NOT NULL DEFAULT '0',
  `reason` varchar(16) NOT NULL DEFAULT '',
  PRIMARY KEY (`logid`),
  KEY `idx_complete` (`complete`),
  KEY `idx_appid` (`appid`),
  KEY `idx_nid` (`nid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='通知发送记录表';

DROP TABLE IF EXISTS `pw_windid_school`;
CREATE TABLE `pw_windid_school` (
  `schoolid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '学校ID',
  `name` varchar(32) NOT NULL DEFAULT '' COMMENT '学校名字',
  `areaid` int(10) NOT NULL DEFAULT '0' COMMENT '地区ID',
  `typeid` tinyint(3) NOT NULL DEFAULT '0' COMMENT '类型：大学/高中/初中',
  `first_char` char(1) NOT NULL DEFAULT '' COMMENT '学校名字的首字母',
  PRIMARY KEY (`schoolid`),
  KEY `idx_areaid_firstchar` (`areaid`,`first_char`),
  KEY `idx_name_firstchar` (`name`,`first_char`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='统一的windid学校库';

DROP TABLE IF EXISTS `pw_windid_user`;
CREATE TABLE `pw_windid_user` (
  `uid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户ID',
  `username` varchar(15) NOT NULL DEFAULT '' COMMENT '用户名字',
  `email` varchar(80) NOT NULL DEFAULT '' COMMENT 'Email',
  `password` char(32) NOT NULL DEFAULT '' COMMENT '密码',
  `salt` char(6) NOT NULL DEFAULT '' COMMENT '盐值',
  `safecv` char(8) NOT NULL DEFAULT '' COMMENT '安全问题',
  `regdate` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '注册时间',
  `regip` varchar(20) NOT NULL DEFAULT '' COMMENT '注册IP',
  PRIMARY KEY (`uid`),
  UNIQUE KEY `idx_username` (`username`),
  KEY `idx_email` (`email`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='windid用户基本信息表';

DROP TABLE IF EXISTS `pw_windid_user_black`;
CREATE TABLE `pw_windid_user_black` (
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户uid',
  `blacklist` text COMMENT '黑名单',
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='用户黑名单';

DROP TABLE IF EXISTS `pw_windid_user_data`;
CREATE TABLE `pw_windid_user_data` (
  `uid` int(10) unsigned NOT NULL COMMENT '用户ID',
  `messages` smallint(6) unsigned NOT NULL DEFAULT '0' COMMENT '用户消息数',
  `credit1` int(10) NOT NULL DEFAULT '0' COMMENT '积分1',
  `credit2` int(10) NOT NULL DEFAULT '0' COMMENT '积分2',
  `credit3` int(10) NOT NULL DEFAULT '0' COMMENT '积分3',
  `credit4` int(10) NOT NULL DEFAULT '0' COMMENT '积分4',
  `credit5` int(10) NOT NULL DEFAULT '0',
  `credit6` int(10) NOT NULL DEFAULT '0',
  `credit7` int(10) NOT NULL DEFAULT '0',
  `credit8` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='windid用户数据';

DROP TABLE IF EXISTS `pw_windid_user_info`;
CREATE TABLE `pw_windid_user_info` (
  `uid` int(10) unsigned NOT NULL COMMENT '用户ID',
  `realname` varchar(20) NOT NULL DEFAULT '',
  `icon` varchar(100) NOT NULL DEFAULT '' COMMENT '头像---未用',
  `gender` tinyint(1) NOT NULL DEFAULT '0' COMMENT '性别',
  `byear` smallint(4) unsigned NOT NULL DEFAULT '0' COMMENT '出生年份',
  `bmonth` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '出生月份',
  `bday` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '出生日期',
  `hometown` int(10) NOT NULL DEFAULT '0' COMMENT '家庭地址ID',
  `location` int(10) NOT NULL DEFAULT '0' COMMENT '居住地ID',
  `homepage` varchar(128) NOT NULL DEFAULT '' COMMENT '主页',
  `qq` varchar(12) NOT NULL DEFAULT '' COMMENT 'QQ ',
  `aliww` varchar(30) NOT NULL DEFAULT '' COMMENT '阿里旺旺',
  `mobile` varchar(16) NOT NULL DEFAULT '' COMMENT '手机号码',
  `alipay` varchar(80) NOT NULL DEFAULT '' COMMENT '支付宝',
  `msn` varchar(80) NOT NULL DEFAULT '' COMMENT 'MSN',
  `profile` varchar(250) NOT NULL DEFAULT '',
  PRIMARY KEY (`uid`),
  KEY `idx_bday` (`bday`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='windid用户扩展基本信息表二';

DROP TABLE IF EXISTS `pw_word`;
CREATE TABLE `pw_word` (
  `word_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '敏感词自增长ID',
  `word_type` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '敏感词类型',
  `word` varchar(100) NOT NULL DEFAULT '' COMMENT '敏感词',
  `word_replace` varchar(100) NOT NULL DEFAULT '' COMMENT '敏感词替换',
  `word_from` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '敏感词来源',
  `created_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '敏感词创建时间',
  PRIMARY KEY (`word_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='敏感词表';
