-- TABLENAME pw_seo
-- Fields mod 模式
-- Fields page 页面
-- Fileds param 参数 
-- Fields value 值
DROP TABLE IF EXISTS `pw_seo`;
CREATE TABLE `pw_seo` (
`mod` varchar(15) NOT NULL DEFAULT '',
`page` varchar(20) NOT NULL DEFAULT '',
`param` varchar(20) NOT NULL DEFAULT '',
`title` varchar(255) NOT NULL DEFAULT '',
`keywords` varchar(255) NOT NULL DEFAULT '',
`description` varchar(255) NOT NULL DEFAULT '',
PRIMARY KEY (`mod`, `page`,`param`)
)ENGINE=MYISAM DEFAULT CHARSET=utf8;