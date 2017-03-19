drop table if exists `pw_domain`;
create table `pw_domain` (
`domain_key` VARCHAR(100) NOT NULL DEFAULT '',
`domain_type` VARCHAR(15) NOT NULL DEFAULT '',
`domain` VARCHAR(15) NOT NULL DEFAULT '',
`root` VARCHAR(45) NOT NULL DEFAULT '',
`first` char(1) NOT NULL DEFAULT '',
PRIMARY KEY (`domain_key`)
)ENGINE=MYISAM DEFAULT CHARSET=utf8;