DROP TABLE IF EXISTS `pw_album`;
create table `pw_album` (
`albumid` smallint(6) UNSIGNED NOT NULL AUTO_INCREMENT,
`created_uid` int(10) NOT NULL DEFAULT 0,
`privacy` tinyint(1) NOT NULL DEFAULT 0,
`album_type` tinyint(1) NOT NULL DEFAULT 1,
`photo_num`smallint(6) UNSIGNED NOT NULL DEFAULT 0,
`created_time` int(10) UNSIGNED NOT NULL DEFAULT 0,
`modified_time` int(10) UNSIGNED NOT NULL DEFAULT 0,
`name` varchar(30) NOT NULL DEFAULT '',
`cover` varchar(100) NOT NULL DEFAULT '',
`descrip` varchar(255) NOT NULL DEFAULT '',
PRIMARY KEY (`albumid`),
KEY `idx_createduid_createdtime` (`created_uid`, `created_time`)
)ENGINE=MYISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `pw_album_photo`;
create table `pw_album_photo` (
`photoid` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
`albumid` smallint(6) NOT NULL DEFAULT 0,
`ifthumb` tinyint(1) NOT NULL DEFAULT 0,
`comment_num` smallint(6) UNSIGNED NOT NULL DEFAULT 0,
`created_time` int(10) UNSIGNED NOT NULL DEFAULT 0,
`name` varchar(50) NOT NULL DEFAULT '',
`path` varchar(100) NOT NULL DEFAULT '',
`descrip` varchar(255) NOT NULL DEFAULT '',
PRIMARY KEY (`photoid`),
KEY `idx_albumid_createdtime` (`albumid`, `created_time`)
)ENGINE=MYISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `pw_album_photo_comment`;
create table `pw_album_photo_comment` (
`commentid` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
`photoid` int(10) UNSIGNED NOT NULL DEFAULT 0,
`created_uid` int(10) UNSIGNED NOT NULL DEFAULT 0,
`created_time` int(10) UNSIGNED NOT NULL DEFAULT 0,
`created_username` varchar(15) not null default '',
`content` varchar(255) not null default '',
PRIMARY KEY (`commentid`),
KEY `idx_photoid_createdtime` (`photoid`, `created_time`)
)ENGINE=MYISAM DEFAULT CHARSET=utf8;


INSERT INTO `pw_admin_role` (`id`, `name`, `auths`, `created_time`, `modified_time`) VALUES
(1, '管理员', 'config_site,config_register,config_verifycode,config_nav,config_credit,config_attachment,config_watermark,config_emotion,config_rewrite,config_domain,config_email,config_pay,u_groups,u_upgrade,u_forbidden,u_manage,u_check,contents_tag,contents_message,contents_report,bbs_article,bbs_recycle,contents_user_tag,bbs_configbbs,bbs_setforum,bbs_setbbs,design_page,design_component,design_module,design_push,design_permissions,database_backup,cache_m,link_link,operations_link,operations_punch,announce_announce,cron_operations', 1340275489, 1340275489);

INSERT INTO `pw_application` (`id`, `app_id`, `app_name`, `app_type`, `logo`, `author`, `icon`, `email`, `website`, `version`, `release_data`, `pw_version`, `charset`, `created_time`, `modified_time`, `descript`) VALUES
(28, '200813401764428792', 'nextwind', 'style', 'images/preview.jpg', '龙文', 'http://www.phpwind.net/u-htm-uid-1793211.html', 'yanchixia@aliyun-inc.com', '', '1.0', 0, '9.0', 'utf-8', 1340176442, 1340176442, '新版样式'),
(29, '200813401764423292', 'default', 'style', 'images/preview.jpg', '龙文', 'http://www.phpwind.net/u-htm-uid-1793211.html', 'yanchixia@aliyun-inc.com', '', '1.0', 0, '9.0', 'utf-8', 1340176442, 1340176442, '传统个人空间样式');

INSERT INTO `pw_bbs_forum` (`fid`, `parentid`, `type`, `issub`, `hassub`, `name`, `descrip`, `vieworder`, `across`, `manager`, `uppermanager`, `icon`, `logo`, `fup`, `isshow`, `isshowsub`, `newtime`, `allow_visit`, `allow_read`, `allow_post`, `allow_reply`, `allow_upload`, `allow_download`, `created_time`, `created_username`, `created_userid`, `created_ip`) VALUES
(1, 0, 'category', 0, 1, '默认分类', NULL, 0, 0, '', '', '', '', '', 1, 0, 0, '', '', '', '', '', '', 0, '', 0, 0),
(2, 1, 'forum', 0, 0, '默认版块', NULL, 0, 0, '', '', '', '', '1', 1, 0, 0, '7,8,9,10,11,12,13,14,3,4,5', '7,8,9,10,11,12,13,14,3,4,5', '7,8,9,10,11,12,13,14,3,4,5', '7,8,9,10,11,12,13,14,3,4,5', '7,8,9,10,11,12,13,14,3,4,5', '7,8,9,10,11,12,13,14,3,4,5', 0, '', 0, 0);

INSERT INTO `pw_bbs_forum_extra` (`fid`, `seo_description`, `seo_keywords`, `settings_basic`, `settings_credit`) VALUES
(1, '', '', NULL, NULL),
(2, '', '', 'a:24:{s:16:"numofthreadtitle";s:0:"";s:13:"threadperpage";s:0:"";s:11:"readperpage";s:0:"";s:18:"minlengthofcontent";s:0:"";s:8:"locktime";s:0:"";s:8:"edittime";s:0:"";s:12:"contentcheck";i:0;s:7:"ifthumb";i:0;s:10:"thumbwidth";s:0:"";s:11:"thumbheight";s:0:"";s:8:"anticopy";i:0;s:11:"copycontent";s:0:"";s:5:"water";i:0;s:8:"allowrob";i:0;s:9:"allowhide";i:0;s:9:"allowsell";i:0;s:11:"allowencode";i:0;s:9:"anonymous";i:0;s:9:"allowtype";i:1;s:9:"typeorder";a:5:{i:0;i:0;i:1;i:0;i:2;i:0;i:3;i:0;i:4;i:0;}s:7:"jumpurl";s:0:"";s:10:"topic_type";i:0;s:16:"force_topic_type";i:0;s:14:"thread_visible";i:0;}', 'a:5:{s:10:"post_topic";a:3:{i:1;s:0:"";i:2;s:0:"";i:3;s:0:"";}s:10:"post_reply";a:3:{i:1;s:0:"";i:2;s:0:"";i:3;s:0:"";}s:12:"digest_topic";a:3:{i:1;s:0:"";i:2;s:0:"";i:3;s:0:"";}s:10:"upload_att";a:3:{i:1;s:0:"";i:2;s:0:"";i:3;s:0:"";}s:12:"download_att";a:3:{i:1;s:0:"";i:2;s:0:"";i:3;s:0:"";}}');

INSERT INTO `pw_bbs_forum_statistics` (`fid`, `todayposts`, `todaythreads`, `article`, `posts`, `threads`, `subposts`, `subthreads`, `lastpost_info`, `lastpost_time`, `lastpost_username`, `lastpost_tid`) VALUES
(1, 0, 0, 0, 0, 0, 0, 0, '', 0, '', 0),
(2, 0, 0, 0, 0, 0, 0, 0, '', 0, '', 0);

