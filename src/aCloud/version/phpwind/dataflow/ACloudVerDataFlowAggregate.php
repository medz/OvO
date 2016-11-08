<?php

! defined('ACLOUD_PATH') && exit('Forbidden');
define('AC_THREAD_SIG', 1);
define('AC_DIARY_SIG', 2);
define('AC_MEMBER_SIG', 3);
define('AC_FORUM_SIG', 4);
define('AC_COLONY_SIG', 5);
define('AC_POST_SIG', 6);
define('AC_ATTACH_SIG', 8);
define('AC_DELETE_SIG', 100);
class ACloudVerDataFlowAggregate
{
    public static function getMonitorTables()
    {
        $tableConfigs = ACloudVerDataFlowAggregate::getTableConfigs();

        return array_keys($tableConfigs);
    }

    public static function getTypeByTableName($tableName)
    {
        $tableConfigs = ACloudVerDataFlowAggregate::getTableConfigs();

        return isset($tableConfigs [$tableName]) ? $tableConfigs [$tableName] : null;
    }

    public static function getTableConfigs()
    {
        return array('bbs_threads' => AC_THREAD_SIG, 'user' => AC_MEMBER_SIG, 'bbs_forum' => AC_FORUM_SIG, 'bbs_posts' => AC_POST_SIG, 'attachs' => AC_ATTACH_SIG);
    }

    public static function getDeleteSig()
    {
        return AC_DELETE_SIG;
    }
}
