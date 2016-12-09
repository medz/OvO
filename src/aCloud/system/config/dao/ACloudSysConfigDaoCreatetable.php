<?php

!defined('ACLOUD_PATH') && exit('Forbidden');
require_once Wind::getRealPath('ACLOUD:system.core.ACloudSysCoreDao');
class ACloudSysConfigDaoCreatetable extends ACloudSysCoreDao
{
    public function initTables()
    {
        $result = $this->fetchOne("SHOW TABLES LIKE '{{acloud_extras}}'");
        if ($result) {
            return true;
        }
        $this->createTables();
        $this->createTableRows();

        return true;
    }

    public function createTables()
    {
        $sqls = $this->_getTables();
        foreach ($sqls as $tableName => $sql) {
            $result = $this->fetchOne("SHOW TABLES LIKE '{$tableName}'");
            if ($result) {
                continue;
            }
            $this->query($sql);
        }

        return true;
    }

    public function checkTables()
    {
        $sqls = $this->_getTables();
        $tmp = array();
        foreach ($sqls as $tableName => $sql) {
            $result = $this->fetchOne("SHOW TABLES LIKE '{$tableName}'");
            $tableName = str_replace(array('{{', '}}'), '', $tableName);
            $tmp[$tableName] = ($result) ? 1 : 0;
        }

        return $tmp;
    }

    public function createTableRows()
    {
        $rows = $this->_getTableRows();
        foreach ($rows as $tableName => $sqls) {
            $result = $this->fetchOne("SHOW TABLES LIKE '{$tableName}'");
            if ($result) {
                foreach ($sqls as $sql) {
                    $this->query($sql);
                }
            }
        }

        return true;
    }

    private function _getTables()
    {
        $db = $this->getDB();
        $version = ($db->server_info() >= '4.1') ? 'ENGINE=MyISAM' : 'TYPE=MyISAM';

        return array('{{acloud_keys}}' => "create table {{acloud_keys}}(
					id int(10) unsigned not null auto_increment,
					key1 char(128) not null default '',
					key2 char(128) not null default '',
					key3 char(128) not null default '',
					key4 char(128) not null default '',
					key5 char(128) not null default '',
					key6 char(128) not null default '',
					created_time int(10) unsigned not null default '0',
					modified_time int(10) unsigned not null default '0',
					primary key (id)
				)$version", '{{acloud_apps}}' => "create table {{acloud_apps}}(
					app_id char(22) not null default '',
					app_name varchar(60) not null default '',
					app_token char(128) not null default '',
					created_time int(10) not null default '0',
					modified_time int(10) not null default '0',
					primary key (app_id)
				)$version", '{{acloud_app_configs}}' => "create table {{acloud_app_configs}}(
					app_id char(22) not null default '',
					app_key varchar(30) not null default '',
					app_value text,
					app_type tinyint(3) not null default '1',
					created_time int(10) not null default '0',
					modified_time int(10) not null default '0',
					unique key (app_id,app_key)
				)$version", '{{acloud_extras}}' => "create table  {{acloud_extras}}(
					ekey varchar(100) not null default '',
					evalue text,
					etype tinyint(3) not null default '1',
					created_time int(10) unsigned not null default '0',
					modified_time int(10) unsigned not null default '0',
					primary key (ekey)
				)$version", '{{acloud_sql_log}}' => "create table {{acloud_sql_log}} (
				   id int(10) unsigned not null AUTO_INCREMENT,
				   log text,
				   created_time int(10) unsigned not null default '0',
				   primary key (id)
				)$version", '{{acloud_apis}}' => "create table {{acloud_apis}} (
				  id int(10) unsigned not null AUTO_INCREMENT,
				  name varchar(255) not null default '',
				  template text,
				  argument varchar(255) not null default '',
				  argument_type varchar(255) not null default '',
				  fields varchar(255) not null default '',
				  status tinyint(3) not null default '0',
				  category tinyint(3) not null default '0',
				  created_time int(10) not null default '0',
				  modified_time int(10) unsigned not null default '0',
				  primary key (id),
				  unique key idx_name (name)
				)$version", '{{acloud_table_settings}}' => "create table {{acloud_table_settings}} (
				   name varchar(255) not null default '',
				   status tinyint(3) not null default '0',
				   category tinyint(3) not null default '0',
				   primary_key varchar(20) not null default '',
				   created_time int(10) unsigned not null default '0',
				   modified_time int(10) unsigned not null default '0',
				   primary key (name)
				)$version");
    }

    private function _getTableRows()
    {
        $sqls = '';
        $sqls['{{acloud_keys}}'][] = "REPLACE INTO {{acloud_keys}} (id,key1,key2,key3,key4,key5,key6,created_time,modified_time) VALUES (1,'','','','','','',1330586406,1330586406)";
        $sqls['{{acloud_keys}}'][] = "REPLACE INTO {{acloud_keys}} (id,key1,key2,key3,key4,key5,key6,created_time,modified_time) VALUES (2,'','','','','','',1330586406,1330586406)";
        $sqls['{{acloud_extras}}'][] = "REPLACE INTO {{acloud_extras}}  SET `ekey` = 'ac_isopen' , `evalue` = '0'";
        $sqls['{{acloud_extras}}'][] = "REPLACE INTO {{acloud_extras}}  SET `ekey` = 'ac_ipcontrol' , `evalue` = '1'";
        $sqls['{{acloud_extras}}'][] = "REPLACE INTO {{acloud_extras}}  SET `ekey` = 'ac_apply_step' , `evalue` = '0'";
        $sqls['{{acloud_extras}}'][] = "REPLACE INTO {{acloud_extras}}  SET `ekey` = 'ac_apply_siteurl' , `evalue` = ''";
        $sqls['{{acloud_extras}}'][] = "REPLACE INTO {{acloud_extras}}  SET `ekey` = 'ac_apply_lasttime' , `evalue` = '0'";
        $sqls['{{acloud_apis}}'][] = "REPLACE INTO {{acloud_apis}} (`name`, `template`, `argument`, `argument_type`, `fields`, `status`, `category`, `created_time`, `modified_time`) VALUES ('customized.thread.get', 'getByTid', 'tid', '', '', '1', '1', '1331123657', '1331123657'),('customized.thread.getByUid', 'getByUid', 'uid,offset,limit', '', '', '1', '1', '1331123657', '1331123657'),('customized.thread.latest.gets', 'getLatestThread', 'fids,offset,limit', '', '', '1', '1', '1331123657', '1331123657'),('customized.thread.latest.favoritesForum.gets', 'getLatestThreadByFavoritesForum', 'uid,offset,limit', '', '', '1', '1', '1331123657', '1331123657'),('customized.thread.latest.followUser.gets', 'getLatestThreadByFollowUser', 'uid,offset,limit', '', '', '1', '1', '1331123657', '1331123657'),('customized.thread.img.latest.gets', 'getLatestImgThread', 'fids,offset,limit', '', '', '1', '1', '1331123657', '1331123657'),('customized.thread.img.get', 'getThreadImgs', 'tid', '', '', '1', '1', '1331123657', '1331123657'),('customized.thread.topped.getByFid', 'getToppedThreadByFid', 'fid,offset,limit', '', '', '1', '1', '1331123657', '1331123657'),('customized.thread.getByFid', 'getThreadByFid', 'fid,offset,limit', '', '', '1', '1', '1331123657', '1331123657'),('customized.thread.at.gets', 'getAtThreadByUid', 'uid,offset,limit', '', '', '1', '1', '1331123657', '1331123657'),('customized.thread.getbyTopic', 'getThreadByTopic', 'topic,offset,limit', '', '', '1', '1', '1331123657', '1331123657'),('customized.thread.send', 'postThread', 'uid,fid,subject,content', '', '', '1', '1', '1331123657', '1331123657'),('customized.post.gets', 'getPost', 'tid,sort,offset,limit', '', '', '1', '1', '1331123657', '1331123657'),('customized.post.getByUid', 'getPostByUid', 'uid,offset,limit', '', '', '1', '1', '1331123657', '1331123657'),('customized.post.getByTidAndUid', 'getPostByTidAndUid', 'tid,uid,offset,limit', '', '', '1', '1', '1331123657', '1331123657'),('customized.post.send', 'sendPost', 'tid,uid,title,content', '', '', '1', '1', '1331123657', '1331123657'),('customized.user.get', 'getByUid', 'uid', '', '', '1', '1', '1331123657', '1331123657'),('customized.user.icon.update', 'updateIcon', 'uid', '', '', '1', '1', '1331123657', '1331123657'),('customized.user.favoritesForum.gets', 'getFavoritesForumByUid', 'uid', '', '', '1', '1', '1331123657', '1331123657'),('customized.user.favoritesforum.add', 'addFavoritesForumByUid', 'uid,fid', '', '', '1', '1', '1331123657', '1331123657'),('customized.user.favoritesforum.delete', 'deleteFavoritesForumByUid', 'uid,fid', '', '', '1', '1', '1331123657', '1331123657'),('customized.user.login', 'userLogin', 'username,password', '', '', '1', '1', '1331123657', '1331123657'),('customized.user.register', 'userRegister', 'username,password,email', '', '', '1', '1', '1331123657', '1331123657'),('customized.user.updateemail', 'updateEmail', 'uid,email', '', '', '1', '1', '1331123657', '1331123657'),('customized.forum.all.get', 'getAllForum', '', '', '', '1', '1', '1331123657', '1331123657'),('customized.forum.get', 'getForumByFid', 'fid', '', '', '1', '1', '1331123657', '1331123657'),('customized.forum.child.getByFid', 'getChildForumByFid', 'fid', '', '', '1', '1', '1331123657', '1331123657'),('customized.message.unread.count', 'countUnreadMessage', 'uid', '', '', '1', '1', '1331123657', '1331123657'),('customized.message.gets', 'getMessageByUid', 'uid,offset,limit', '', '', '1', '1', '1331123657', '1331123657'),('customized.message.send', 'sendMessage', 'fromuid,touid,title,content', '', '', '1', '1', '1331123657', '1331123657'),('customized.message.reply', 'replyMessage', 'messageid,relationid,uid,content', '', '', '1', '1', '1331123657', '1331123657'),('customized.message.get', 'getMessageAndReply', 'messageid,relationid,uid,offset,limit', '', '', '1', '1', '1331123657', '1331123657'),('customized.message.postmythread.gets', 'getReplyThreadMessage', 'uid,offset,limit', '', '', '1', '1', '1331123657', '1331123657'),('customized.friend.all.gets', 'getAllFriend', 'uid,offset,limit', '', '', '1', '1', '1331123657', '1331123657'),('customized.friend.all.search', 'searchAllFriend', 'uid,keyword,offset,limit', '', '', '1', '1', '1331123657', '1331123657'),('customized.friend.follow.gets', 'getFollowByUid', 'uid,offset,limit', '', '', '1', '1', '1331123657', '1331123657'),('customized.friend.follow.add', 'addFollowByUid', 'uid,uid2', '', '', '1', '1', '1331123657', '1331123657'),('customized.friend.follow.delete', 'deleteFollowByUid', 'uid,uid2', '', '', '1', '1', '1331123657', '1331123657'),('customized.friend.fan.gets', 'getFanByUid', 'uid,offset,limit', '', '', '1', '1', '1331123657', '1331123657'),('common.permissions.user.isbanned', 'isUserBanned', 'uid', '', '', '1', '0', '1331123657', '1331123657'),('common.permissions.user.readforum', 'readForum', 'uid,fid', '', '', '1', '0', '1331123657', '1331123657'),('common.search.hotwords.gets', 'getHotwords', '', '', '', '1', '0', '1331123657', '1331123657')";
        $sqls['{{acloud_apis}}'][] = "REPLACE INTO {{acloud_apis}} (`name`, `template`, `argument`, `argument_type`, `fields`, `status`, `category`, `created_time`, `modified_time`) VALUES ('customized.user.getByName', 'getByName', 'username', '', '', '1', '1', '1331123657', '1331123657')";

        $sqls['{{acloud_apis}}'][] = "REPLACE INTO {{acloud_apis}} (`name`, `template`, `argument`, `argument_type`, `fields`, `status`, `category`, `created_time`, `modified_time`) VALUES ('common.site.partitions.get', 'getTablePartitions', 'type', '', '', '1', '0', '1331123657', '1331123657')";
        $sqls['{{acloud_apis}}'][] = "REPLACE INTO {{acloud_apis}} (`name`, `template`, `argument`, `argument_type`, `fields`, `status`, `category`, `created_time`, `modified_time`) VALUES ('common.user.ban', 'banUser', 'uid', '', '', '1', '0', '1331123657', '1331123657')";
        $sqls['{{acloud_apis}}'][] = "REPLACE INTO {{acloud_apis}} (`name`, `template`, `argument`, `argument_type`, `fields`, `status`, `category`, `created_time`, `modified_time`) VALUES ('common.thread.shield', 'shieldThread', 'tid,fid', '', '', '1', '0', '1331123657', '1331123657')";
        $sqls['{{acloud_apis}}'][] = "REPLACE INTO {{acloud_apis}} (`name`, `template`, `argument`, `argument_type`, `fields`, `status`, `category`, `created_time`, `modified_time`) VALUES ('common.post.shield', 'shieldPost', 'pid,tid', '', '', '1', '0', '1331123657', '1331123657')";
        $sqls['{{acloud_apis}}'][] = "REPLACE INTO {{acloud_apis}} (`name`, `template`, `argument`, `argument_type`, `fields`, `status`, `category`, `created_time`, `modified_time`) VALUES ('common.attach.img.gets', 'getImgAttaches', 'aids', '', '', '1', '0', '1331123657', '1331123657')";
        $sqls['{{acloud_apis}}'][] = "REPLACE INTO {{acloud_apis}} (`name`, `template`, `argument`, `argument_type`, `fields`, `status`, `category`, `created_time`, `modified_time`) VALUES ('common.user.getIcons', 'getIconsByUids', 'uids', '', '', '1', '0', '1331123657', '1331123657')";
        $sqls['{{acloud_apis}}'][] = "REPLACE INTO {{acloud_apis}} (`name`, `template`, `argument`, `argument_type`, `fields`, `status`, `category`, `created_time`, `modified_time`) VALUES ('common.site.field.check', 'checkTableField', 'table,field', '', '', '1', '0', '1331123657', '1331123657')";
        $sqls['{{acloud_apis}}'][] = "REPLACE INTO {{acloud_apis}} (`name`, `template`, `argument`, `argument_type`, `fields`, `status`, `category`, `created_time`, `modified_time`) VALUES ('customized.thread.mobile.send', 'postMobileThread', 'uid,fid,subject,content,mobiletype', '', '', '1', '1', '1331123657', '1331123657')";
        $sqls['{{acloud_apis}}'][] = "REPLACE INTO {{acloud_apis}} (`name`, `template`, `argument`, `argument_type`, `fields`, `status`, `category`, `created_time`, `modified_time`) VALUES ('customized.post.mobile.send', 'sendMobilePost', 'tid,uid,title,content,mobiletype', '', '', '1', '1', '1331123657', '1331123657')";

        $sqls['{{acloud_table_settings}}'][] = "REPLACE INTO {{acloud_table_settings}} (`name`, `status`, `category`, `primary_key`, `created_time`, `modified_time`) VALUES ('prefix_bbs_threads', '1', '1', 'tid', '1331123657', '1331123657')";
        $sqls['{{acloud_table_settings}}'][] = "REPLACE INTO {{acloud_table_settings}} (`name`, `status`, `category`, `primary_key`, `created_time`, `modified_time`) VALUES ('prefix_bbs_threads_content', '1', '1', 'tid', '1331123657', '1331123657')";
        $sqls['{{acloud_table_settings}}'][] = "REPLACE INTO {{acloud_table_settings}} (`name`, `status`, `category`, `primary_key`, `created_time`, `modified_time`) VALUES ('prefix_bbs_forum', '1', '1', 'fid', '1331123657', '1331123657')";
        $sqls['{{acloud_table_settings}}'][] = "REPLACE INTO {{acloud_table_settings}} (`name`, `status`, `category`, `primary_key`, `created_time`, `modified_time`) VALUES ('prefix_bbs_forum_statistics', '1', '1', 'fid', '1331123657', '1331123657')";
        $sqls['{{acloud_table_settings}}'][] = "REPLACE INTO {{acloud_table_settings}} (`name`, `status`, `category`, `primary_key`, `created_time`, `modified_time`) VALUES ('prefix_bbs_posts', '1', '1', 'pid', '1331123657', '1331123657')";
        $sqls['{{acloud_table_settings}}'][] = "REPLACE INTO {{acloud_table_settings}} (`name`, `status`, `category`, `primary_key`, `created_time`, `modified_time`) VALUES ('prefix_attachs', '1', '1', 'aid', '1331123657', '1331123657')";
        $sqls['{{acloud_table_settings}}'][] = "REPLACE INTO {{acloud_table_settings}} (`name`, `status`, `category`, `primary_key`, `created_time`, `modified_time`) VALUES ('prefix_attachs_thread', '1', '1', 'aid', '1331123657', '1331123657')";
        $sqls['{{acloud_table_settings}}'][] = "REPLACE INTO {{acloud_table_settings}} (`name`, `status`, `category`, `primary_key`, `created_time`, `modified_time`) VALUES ('prefix_user', '1', '1', 'uid', '1331123657', '1331123657')";
        $sqls['{{acloud_table_settings}}'][] = "REPLACE INTO {{acloud_table_settings}} (`name`, `status`, `category`, `primary_key`, `created_time`, `modified_time`) VALUES ('prefix_user_data', '1', '1', 'uid', '1331123657', '1331123657')";
        $sqls['{{acloud_table_settings}}'][] = "REPLACE INTO {{acloud_table_settings}} (`name`, `status`, `category`, `primary_key`, `created_time`, `modified_time`) VALUES ('prefix_user_info', '1', '1', 'uid', '1331123657', '1331123657')";

        return $sqls;
    }
}
