<?php

Wind::import('APPS:appcenter.service.srv.iPwInstall');

class AppSearchInstall implements iPwInstall
{
    /* (non-PHPdoc)
     * @see iPwInstall::install()
     */
    public function install($install)
    {
        $defaultConfig = array(
                'isopen' => array('value' => 0),
            );
        /* @var $service PwConfig */
        $service = Wekit::load('config.PwConfig');
        $service->setConfigs('search', $defaultConfig);
        $service->setConfigs('site', array('search.isopen' => 1));
        $this->NregisteResource($install);

        return true;
    }
    /**
     * 注册静态资源
     *
     * @param  PwInstallApplication $install
     * @return PwError              true
     */
    public function NregisteResource($install)
    {
    }

    /* (non-PHPdoc)
     * @see iPwInstall::backUp()
     */
    public function backUp($install)
    {
        //  Auto-generated method stub
    }

    /* (non-PHPdoc)
     * @see iPwInstall::revert()
     */
    public function revert($install)
    {
        //  Auto-generated method stub
    }

    /* (non-PHPdoc)
     * @see iPwInstall::unInstall()
     */
    public function unInstall($install)
    {
        /* @var $ds PwConfig */
        $ds = Wekit::load('config.PwConfig');
        $ds->deleteConfig('search');

        return true;
    }

    /* (non-PHPdoc)
     * @see iPwInstall::rollback()
     */
    public function rollback($install)
    {
        //  Auto-generated method stub
    }
}
