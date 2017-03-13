<?php


class installBoot extends bootstrap
{
    public function getConfigService()
    {
        return Wekit::load('config.PwConfig');
    }

    public function getConfig()
    {
        return ['components' => [], 'site' => ['debug' => 0]];
    }

    public function getTime()
    {
        return time();
    }

    /* (non-PHPdoc)
     * @see bootstrap::getLoginUser()
     */
    public function getLoginUser()
    {
        return null;
    }
}
