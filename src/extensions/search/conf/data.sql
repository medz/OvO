CREATE TABLE `pw_app_search_record` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '搜索记录主键',
  `created_userid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建人',
  `created_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `search_type` tinyint(3) NOT NULL DEFAULT '1' COMMENT '搜索类型',
  `keywords` varchar(150) NOT NULL DEFAULT '' COMMENT '关键字',
  `num` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '搜索次数',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_uid_type_keywords` (`created_userid`,`search_type`,`keywords`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='搜索记录';

CREATE TABLE IF NOT EXISTS `pw_app_search` (
  `keywords` varchar(150) NOT NULL DEFAULT '' COMMENT '关键字',
  `search_type` tinyint(3) NOT NULL DEFAULT '1' COMMENT '搜索类型',
  `num` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '搜索次数',
  PRIMARY KEY (`keywords`,`search_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


INSERT INTO `pw_user_permission_groups` (`gid`, `rkey`, `rtype`, `rvalue`, `vtype`) VALUES
(3, 'app_search_open', 'basic', '1', 'string'),
(4, 'app_search_open', 'basic', '1', 'string'),
(5, 'app_search_open', 'basic', '1', 'string'),
(8, 'app_search_open', 'basic', '1', 'string'),
(8, 'app_search_time_interval', 'basic', '3', 'string'),
(9, 'app_search_open', 'basic', '1', 'string'),
(9, 'app_search_time_interval', 'basic', '3', 'string'),
(10, 'app_search_open', 'basic', '1', 'string'),
(10, 'app_search_time_interval', 'basic', '3', 'string'),
(11, 'app_search_open', 'basic', '1', 'string'),
(11, 'app_search_time_interval', 'basic', '3', 'string'),
(12, 'app_search_open', 'basic', '1', 'string'),
(12, 'app_search_time_interval', 'basic', '3', 'string'),
(13, 'app_search_open', 'basic', '1', 'string'),
(13, 'app_search_time_interval', 'basic', '3', 'string'),
(14, 'app_search_open', 'basic', '1', 'string'),
(14, 'app_search_time_interval', '', '3', 'string'),
(15, 'app_search_open', 'basic', '1', 'string'),
(15, 'app_search_time_interval', 'basic', '3', 'string'),
(16, 'app_search_open', 'basic', '1', 'string'),
(16, 'app_search_time_interval', 'basic', '3', 'string');