<?php

! defined('ACLOUD_PATH') && exit('Forbidden');

class ACloudVerCommonAd extends ACloudVerCommonBase
{
    public function getAdType()
    {
        $typeArray = $this->getAdDs()->getAdType();

        return $this->buildResponse(0, $typeArray);
    }

    public function addAdPosition($id, $identifier, $type, $width, $height, $status, $schedule)
    {
        $result = $this->getAdService()->addAdPosition($id, $identifier, $type, $width, $height, $status, $schedule);
        if ($result instanceof PwError) {
            return $this->buildResponse(- 1, $result->getError());
        }

        return $this->buildResponse(0, 'success');
    }

    public function editAdPosition($id, $identifier, $type, $width, $height, $status, $schedule, $showType, $condition)
    {
        $result = $this->getAdService()->editAdPosition($id, $identifier, $type, $width, $height, $status, $schedule, $showType, $condition);
        if ($result instanceof PwError) {
            return $this->buildResponse(- 1, $result->getError());
        }

        return $this->buildResponse(0, 'success');
    }

    public function changeAdPositionStatus($id, $status)
    {
        $result = $this->getAdService()->changeAdPositionStatus($id, $status);
        if ($result instanceof PwError) {
            return $this->buildResponse(- 1, $result->getError());
        }

        return $this->buildResponse(0, 'success');
    }

    public function getModes()
    {
        $result = $this->getAdDs()->getModes();
        if ($result instanceof PwError) {
            return $this->buildResponse(- 1, $result->getError());
        }

        return $this->buildResponse(0, $result);
    }

    public function getDefaultPosition()
    {
        $result = $this->getAdDs()->getDefaultPosition();
        if ($result instanceof PwError) {
            return $this->buildResponse(- 1, $result->getError());
        }

        return $this->buildResponse(0, $result);
    }

    public function getInstalledPosition()
    {
        $result = $this->getAdService()->getInstalledPosition();
        if ($result instanceof PwError) {
            return $this->buildResponse(- 1, $result->getError());
        }

        return $this->buildResponse(0, $result);
    }

    public function getPages()
    {
        $result = $this->getAdDs()->getPages();
        if ($result instanceof PwError) {
            return $this->buildResponse(- 1, $result->getError());
        }

        return $this->buildResponse(0, $result);
    }

    public function getPortals()
    {
        $result = $this->getAdService()->getPortals();
        if ($result instanceof PwError) {
            return $this->buildResponse(- 1, $result->getError());
        }

        return $this->buildResponse(0, $result);
    }

    private function getAdDs()
    {
        return Wekit::load('SRV:advertisement.PwAd');
    }

    private function getAdService()
    {
        return Wekit::load('SRV:advertisement.srv.PwAdService');
    }
}
