<?php

class AppSearchConfigDo
{
    /**
     * 获取搜索用户组权限配置.
     *
     * @param unknown_type $config
     *
     * @return multitype:multitype:string multitype:string boolean
     */
    public function getPermissionConfig($config)
    {
        $config += array(
            'app_search_open'          => array('radio', 'basic', '易搜索', ''),
            'app_search_time_interval' => array('input', 'basic', '易搜索间隔', '请输入用户两次搜索的时间间隔', ''),
        );

        return $config;
    }

    /**
     * 获取搜索用户组根权限配置.
     *
     * @param array $config
     *
     * @return multitype:multitype:string
     */
    public function getPermissionCategoryConfig($config)
    {
        $searchconfig = array(
            'other' => array(
                'sub' => array(
                    'search' => array(
                        'name'  => '易搜索',
                        'items' => array(
                            'app_search_open', 'app_search_time_interval',
                        ),
                    ),
                ),
            ),
        );

        return WindUtility::mergeArray($config, $searchconfig);
    }

    public function getAdminMenu($config)
    {
        $config += array(
            'app_search' => array('易搜索', 'app/manage/*?app=search', '', '', 'appcenter'),
            );

        return $config;
    }
}
