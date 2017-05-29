INSERT INTO `pw_common_emotion` (`emotion_id`, `category_id`, `emotion_name`, `emotion_folder`, `emotion_icon`, `vieworder`, `isused`) VALUES
(1, 1, '弹', 'taodoll', '01.gif', 0, 1),
(2, 1, '抱抱', 'taodoll', '02.gif', 0, 1),
(3, 1, '晕', 'taodoll', '03.gif', 0, 1),
(4, 1, '美味', 'taodoll', '04.gif', 0, 1),
(5, 1, '烦', 'taodoll', '05.gif', 0, 1),
(6, 1, '擦口水', 'taodoll', '06.gif', 0, 1),
(7, 1, '思考', 'taodoll', '07.gif', 0, 1),
(8, 1, '心跳', 'taodoll', '08.gif', 0, 1),
(9, 1, '汗', 'taodoll', '09.gif', 0, 1),
(10, 1, '呸', 'taodoll', '10.gif', 0, 1),
(11, 1, '吐舌头', 'taodoll', '11.gif', 0, 1),
(12, 1, '加油', 'taodoll', '12.gif', 0, 1),
(13, 1, '吐', 'taodoll', '13.gif', 0, 1),
(14, 1, '大哭', 'taodoll', '14.gif', 0, 1),
(15, 1, '亲', 'taodoll', '15.gif', 0, 1),
(16, 1, '委屈', 'taodoll', '16.gif', 0, 1),
(17, 1, '眼镜', 'taodoll', '17.gif', 0, 1),
(18, 1, '抠鼻子', 'taodoll', '18.gif', 0, 1),
(19, 1, '臭美', 'taodoll', '19.gif', 0, 1),
(20, 1, '无奈', 'taodoll', '20.gif', 0, 1),
(21, 1, '槌子', 'taodoll', '21.gif', 0, 1),
(22, 1, '哇', 'taodoll', '22.gif', 0, 1),
(23, 1, '抱一抱', 'taodoll', '23.gif', 0, 1),
(24, 1, '不爽', 'taodoll', '24.gif', 0, 1),
(25, 1, '鼻血', 'taodoll', '25.gif', 0, 1),
(26, 1, '帅', 'taodoll', '26.gif', 0, 1);

INSERT INTO `pw_common_nav` (`navid`, `parentid`, `rootid`, `type`, `sign`, `name`, `style`, `link`, `alt`, `target`, `isshow`, `orderid`) VALUES
(1, 0, 1, 'main', 'default|index|run|', '首页', '', 'index.php', '', 0, 0, 1),
(2, 0, 2, 'main', 'bbs|index|run|', '论坛', '|||', 'index.php?m=bbs', '', 0, 1, 2),
(3, 0, 3, 'main', 'bbs|forumlist|run|', '版块', '', 'index.php?m=bbs&c=forumlist', '', 0, 1, 3),
(4, 0, 4, 'main', 'like|like|run|', '喜欢', '|||', 'index.php?m=like&c=like', '', 0, 1, 4),
(5, 0, 5, 'main', '', 'phpwind Fans', '|||', 'https://github.com/medz/phpwind/releases', '', 1, 1, 7),
(6, 0, 6, 'main', 'tag|index|run|', '话题', '|||', 'index.php?m=tag', '', 0, 1, 5),
(7, 0, 7, 'main', 'appcenter|index|run|', '应用', '', 'index.php?m=appcenter', '', 0, 1, 6),
(8, 0, 8, 'my', 'space', '我的空间', '', 'index.php?m=space', '', 0, 1, 1),
(9, 0, 9, 'my', 'fresh', '我的关注', '', 'index.php?m=my&c=fresh', '', 0, 1, 2),
(10, 0, 10, 'my', 'forum', '我的版块', '', 'index.php?m=bbs&c=forum&a=my', '', 0, 1, 3),
(11, 0, 11, 'my', 'article', '我的帖子', '', 'index.php?m=my&c=article', '', 0, 1, 4),
(12, 0, 12, 'my', 'vote', '我的投票', '', 'index.php?m=vote&c=my', '', 0, 1, 5),
(13, 0, 13, 'my', 'task', '我的任务', '', 'index.php?m=task', '', 0, 1, 6),
(14, 0, 14, 'my', 'medal', '我的勋章', '', 'index.php?m=medal', '', 0, 1, 7),
(15, 0, 15, 'bottom', '', 'phpwind fans', '', 'https://github.com/medz/phpwind', '', 0, 1, 1),
(16, 0, 16, 'bottom', '', '联系我们', '|||', 'http://phpwind.com/contact.html', '', 0, 1, 2),
(17, 0, 17, 'bottom', '', '程序建议', '', 'http://www.phpwind.net/thread/155', '', 0, 1, 3),
(18, 0, 18, 'bottom', '', '问题反馈', '', 'http://www.phpwind.net/thread/155', '', 0, 1, 4);

INSERT INTO `pw_medal_info` (`medal_id`, `name`, `path`, `image`, `icon`, `descrip`, `medal_type`, `receive_type`, `medal_gids`, `award_type`, `award_condition`, `expired_days`, `isopen`, `vieworder`) VALUES
(1, '社区居民', '', 'big/shequjumin.gif', 'icon/shequjumin.gif', '注册用户登录后即可获得此勋章', 1, 1, '', 10, 1, 0, 1, 0),
(2, '社区明星', '', 'big/shequmingxing.gif', 'icon/shequmingxing.gif', '提高自身活跃度，增加100个粉丝即可获得此勋章', 1, 1, '', 5, 100, 0, 1, 0),
(3, '最爱沙发', '', 'big/zuiaishafa.gif', 'icon/zuiaishafa.gif', '坐沙发什么的最爽，赶紧去抢100个沙发吧', 1, 1, '', 4, 100, 0, 1, 0),
(4, '忠实会员', '', 'big/zhongshihuiyuan.gif', 'icon/zhongshihuiyuan.gif', '连续7天登录即可获得此勋章，如连续3天不登录则收回此勋章', 1, 1, '', 1, 7, 3, 1, 0),
(5, '喜欢达人', '', 'big/xihuandaren.gif', 'icon/xihuandaren.gif', '努力发好帖，获得100个喜欢', 2, 1, '', 6, 100, 0, 1, 0),
(6, '优秀斑竹', '', 'big/youxiubanzhu.gif', 'icon/youxiubanzhu.gif', '兢兢业业的斑竹，为网站做出了不可磨灭的贡献', 2, 2, '4,5,3', 0, 0, 0, 1, 0),
(7, '社区劳模', '', 'big/shequlaomo.gif', 'icon/shequlaomo.gif', '劳动最光荣，连续7天发主题，连续3天不发帖则收回此勋章', 2, 1, '8,9,10,11,12,13,14,4,5,3,15,16', 3, 7, 3, 1, 0),
(8, 'VIP会员', '', 'big/viphuiyuan.gif', 'icon/viphuiyuan.gif', '尊贵的身份象征，网站高级会员', 2, 2, '', 0, 0, 0, 1, 0),
(9, '原创写手', '', 'big/yuanchuangxieshou.gif', 'icon/yuanchuangxieshou.gif', '做人就要做自己，发表30个主题帖即可获得此勋章', 2, 1, '8,9,10,11,12,13,14,4,5,3,15,16', 7, 30, 0, 1, 0),
(10, '荣誉会员', '', 'big/rongyuhuiyuan.gif', 'icon/rongyuhuiyuan.gif', '为网站的发展做出卓越贡献的会员', 2, 2, '', 0, 0, 0, 1, 0),
(11, '追星一族', '', 'big/zhuixingyizu.gif', 'icon/zhuixingyizu.gif', '狂热的追星一族，关注100个用户即可获得', 2, 1, '', 8, 100, 0, 1, 0);

INSERT INTO `pw_task` (`taskid`, `pre_task`, `is_auto`, `is_display_all`, `view_order`, `is_open`, `start_time`, `end_time`, `period`, `title`, `description`, `icon`, `user_groups`, `reward`, `conditions`) VALUES
(1, 0, 1, 1, 0, 1, 0, 4197024000, 0, '发布一个帖子', '去版块发布一个帖子', '', '8,9,10,11,12,13,14,3,4,5,15,16', 'a:5:{s:4:"type";s:6:"credit";s:3:"key";s:12:"id-name-unit";s:5:"value";s:12:"2-威望-点";s:3:"num";s:2:"10";s:8:"descript";s:11:"10点威望";}', 'a:5:{s:4:"type";s:3:"bbs";s:5:"child";s:10:"postThread";s:3:"fid";s:1:"2";s:3:"num";s:1:"1";s:3:"url";s:18:"bbs/post/run?fid=2";}'),
(2, 0, 0, 0, 9, 1, 0, 4197024000, 0, '增加自己的3个粉丝', '增加自己的3个粉丝', '', '-1', 'a:5:{s:4:"type";s:6:"credit";s:3:"key";s:12:"id-name-unit";s:5:"value";s:12:"2-威望-点";s:3:"num";s:2:"10";s:8:"descript";s:11:"10点威望";}', 'a:4:{s:4:"type";s:6:"member";s:5:"child";s:4:"fans";s:3:"num";d:3;s:3:"url";s:11:"my/fans/run";}'),
(3, 0, 0, 0, 5, 1, 0, 4197024000, 0, '回复二个帖子', '回复二个帖子', '', '-1', 'a:5:{s:4:"type";s:6:"credit";s:3:"key";s:12:"id-name-unit";s:5:"value";s:12:"2-威望-点";s:3:"num";s:2:"10";s:8:"descript";s:11:"10点威望";}', 'a:5:{s:4:"type";s:3:"bbs";s:5:"child";s:5:"reply";s:3:"tid";s:1:"1";s:3:"url";s:18:"bbs/read/run?tid=1";s:3:"num";s:1:"2";}'),
(4, 0, 1, 0, 6, 1, 0, 4197024000, 0, '喜欢一个帖子', '去喜欢一个帖子', '', '-1', 'a:5:{s:4:"type";s:6:"credit";s:3:"key";s:12:"id-name-unit";s:5:"value";s:12:"2-威望-点";s:3:"num";s:2:"10";s:8:"descript";s:11:"10点威望";}', 'a:5:{s:4:"type";s:3:"bbs";s:5:"child";s:4:"like";s:3:"fid";s:1:"2";s:3:"num";s:1:"1";s:3:"url";s:20:"bbs/thread/run?fid=2";}');

INSERT INTO `pw_task_group` (`taskid`, `gid`, `is_auto`, `end_time`) VALUES
(1, -1, 1, 4197024000),
(2, -1, 0, 4197024000),
(3, -1, 0, 4197024000),
(4, -1, 1, 4197024000);
