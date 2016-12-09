<?php

!defined('ACLOUD_PATH') && exit('Forbidden');
define('ACLOUD_API_ILLEGAL_CALL', 10000);

class ACloudApiRouter
{
    public function run()
    {
        list($method) = ACloudSysCoreS::gp(array('method'));
        $this->registerSystemParams();
        $request = $_GET + $_POST;
        unset($request['m']);
        unset($request['c']);
        if (!ACloudSysCoreCommon::loadSystemClass('control', 'verify.service')->apiControl($request)) {
            $this->outputControlError();
        }
        $result = ACloudSysCoreCommon::loadSystemClass('api', 'core.proxy')->call($method, $request);
        $this->output($result);
    }

    private function outputControlError()
    {
        $response = ACloudSysCoreCommon::loadSystemClass('response');
        $response->setErrorCode(ACLOUD_API_ILLEGAL_CALL);
        $response->setResponseData('Illegal Call');

        return $this->output($response->getOutputData());
    }

    private function registerSystemParams()
    {
        list($format) = ACloudSysCoreS::gp(array('format'));
        ACloudSysCoreCommon::setGlobal('acloud_api_output_format', $format);

        return true;
    }

    private function output($data)
    {
        print_r($data);
        exit();
    }
}
