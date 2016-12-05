<?php

!defined('ACLOUD_PATH') && exit('Forbidden');
require_once Wind::getRealPath('ACLOUD_VER:common.ACloudVerCommonBase');
class ACloudVerCommonFactory
{
    private $service = array();

    public function getInstance()
    {
        static $instance = null;
        if (!is_null($instance)) {
            return $instance;
        }
        $instance = new self();

        return $instance;
    }

    public function getVersionCommonPermissions()
    {
        if (!isset($this->service['VersionCommonPermissions']) || !$this->service['VersionCommonPermissions']) {
            require_once Wind::getRealPath('ACLOUD_VER:common.ACloudVerCommonPermissions');
            $this->service['VersionCommonPermissions'] = new ACloudVerCommonPermissions();
        }

        return $this->service['VersionCommonPermissions'];
    }

    public function getVersionCommonSearch()
    {
        if (!isset($this->service['VersionCommonSearch']) || !$this->service['VersionCommonSearch']) {
            require_once Wind::getRealPath('ACLOUD_VER:common.ACloudVerCommonSearch');
            $this->service['VersionCommonSearch'] = new ACloudVerCommonSearch();
        }

        return $this->service['VersionCommonSearch'];
    }

    public function getVersionCommonSite()
    {
        if (!isset($this->service['VersionCommonSite']) || !$this->service['VersionCommonSite']) {
            require_once Wind::getRealPath('ACLOUD_VER:common.ACloudVerCommonSite');
            $this->service['VersionCommonSite'] = new ACloudVerCommonSite();
        }

        return $this->service['VersionCommonSite'];
    }

    public function getVersionCommonUser()
    {
        if (!isset($this->service['VersionCommonUser']) || !$this->service['VersionCommonUser']) {
            require_once Wind::getRealPath('ACLOUD_VER:common.ACloudVerCommonUser');
            $this->service['VersionCommonUser'] = new ACloudVerCommonUser();
        }

        return $this->service['VersionCommonUser'];
    }

    public function getVersionCommonForum()
    {
        if (!isset($this->service['VersionCommonForum']) || !$this->service['VersionCommonForum']) {
            require_once Wind::getRealPath('ACLOUD_VER:common.ACloudVerCommonForum');
            $this->service['VersionCommonForum'] = new ACloudVerCommonForum();
        }

        return $this->service['VersionCommonForum'];
    }

    public function getVersionCommonThread()
    {
        if (!isset($this->service['VersionCommonThread']) || !$this->service['VersionCommonThread']) {
            require_once Wind::getRealPath('ACLOUD_VER:common.ACloudVerCommonThread');
            $this->service['VersionCommonThread'] = new ACloudVerCommonThread();
        }

        return $this->service['VersionCommonThread'];
    }

    public function getVersionCommonPost()
    {
        if (!isset($this->service['VersionCommonPost']) || !$this->service['VersionCommonPost']) {
            require_once Wind::getRealPath('ACLOUD_VER:common.ACloudVerCommonPost');
            $this->service['VersionCommonPost'] = new ACloudVerCommonPost();
        }

        return $this->service['VersionCommonPost'];
    }

    public function getVersionCommonAttach()
    {
        if (!isset($this->service['VersionCommonAttach']) || !$this->service['VersionCommonAttach']) {
            require_once Wind::getRealPath('ACLOUD_VER:common.ACloudVerCommonAttach');
            $this->service['VersionCommonAttach'] = new ACloudVerCommonAttach();
        }

        return $this->service['VersionCommonAttach'];
    }

    public function getVersionCommonDiary()
    {
        if (!isset($this->service['VersionCommonDiary']) || !$this->service['VersionCommonDiary']) {
            require_once Wind::getRealPath('ACLOUD_VER:common.ACloudVerCommonDiary');
            $this->service['VersionCommonDiary'] = new ACloudVerCommonDiary();
        }

        return $this->service['VersionCommonDiary'];
    }

    public function getVersionCommonColony()
    {
        if (!isset($this->service['VersionCommonColony']) || !$this->service['VersionCommonColony']) {
            require_once Wind::getRealPath('ACLOUD_VER:common.ACloudVerCommonColony');
            $this->service['VersionCommonColony'] = new ACloudVerCommonColony();
        }

        return $this->service['VersionCommonColony'];
    }

    public function getVersionCommonApplication()
    {
        if (!isset($this->service['VersionCommonApplication']) || !$this->service['VersionCommonApplication']) {
            require_once Wind::getRealPath('ACLOUD_VER:common.ACloudVerCommonApplication');
            $this->service['VersionCommonApplication'] = new ACloudVerCommonApplication();
        }

        return $this->service['VersionCommonApplication'];
    }

    public function getVersionCommonMessage()
    {
        if (!isset($this->service['VersionCommonMessage']) || !$this->service['VersionCommonMessage']) {
            require_once Wind::getRealPath('ACLOUD_VER:common.ACloudVerCommonMessage');
            $this->service['VersionCommonMessage'] = new ACloudVerCommonMessage();
        }

        return $this->service['VersionCommonMessage'];
    }

    public function getVersionCommonFriend()
    {
        if (!isset($this->service['VersionCommonFriend']) || !$this->service['VersionCommonFriend']) {
            require_once Wind::getRealPath('ACLOUD_VER:common.ACloudVerCommonFriend');
            $this->service['VersionCommonFriend'] = new ACloudVerCommonFriend();
        }

        return $this->service['VersionCommonFriend'];
    }

    public function getVersionCommonAd()
    {
        if (!isset($this->service['VersionCommonAd']) || !$this->service['VersionCommonAd']) {
            require_once Wind::getRealPath('ACLOUD_VER:common.ACloudVerCommonAd');
            $this->service['VersionCommonAd'] = new ACloudVerCommonAd();
        }

        return $this->service['VersionCommonAd'];
    }

    public function getVersionCommonUtility()
    {
        if (!isset($this->service['VersionCommonUtility']) || !$this->service['VersionCommonUtility']) {
            require_once Wind::getRealPath('ACLOUD_VER:common.ACloudVerCommonUtility');
            $this->service['VersionCommonUtility'] = new ACloudVerCommonUtility();
        }

        return $this->service['VersionCommonUtility'];
    }
}
