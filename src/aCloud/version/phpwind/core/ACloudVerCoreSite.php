<?php

! defined('ACLOUD_PATH') && exit('Forbidden');
class ACloudVerCoreSite
{
    public function execute()
    {
        $data = array();
        $data ['site_version'] = WIND_VERSION;
        $data ['site_url'] = PUBLIC_URL ? PUBLIC_URL : $_SERVER ['SERVER_NAME'];

        $data = Wekit::config('site');
        $data ['site_charset'] = Wekit::app()->charset;
        $data ['site_name'] = $data ['info.name'];
        $data ['site_time'] = time();

        return $data;
    }
}
