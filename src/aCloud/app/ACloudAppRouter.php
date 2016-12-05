<?php

!defined('ACLOUD_PATH') && exit('Forbidden');
class ACloudAppRouter
{
    public function getAppsByPage($page)
    {
        $apps = $this->getApps();
        $tmp = array();
        foreach ($apps as $app => $config) {
            if (isset($config['page']) && in_array($page, $config['page'])) {
                $tmp[] = $app;
            }
        }

        return $tmp;
    }

    public function getApps()
    {
        $apps = array();
        $apps['search'] = array('page' => array('search', 'searcher'));

        return $apps;
    }
}
