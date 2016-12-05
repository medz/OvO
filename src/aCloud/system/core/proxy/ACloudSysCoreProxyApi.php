<?php

!defined('ACLOUD_PATH') && exit('Forbidden');
class ACloudSysCoreProxyApi
{
    public function call($api, $request)
    {
        $api = trim($api);
        if (!$api) {
            return $this->buildResponse(10000);
        }
        $apiConfigService = ACloudSysCoreCommon::loadSystemClass('apis', 'config.service');
        $apiConfig = $apiConfigService->getApiConfigByApiName($api);
        if (!ACloudSysCoreS::isArray($apiConfig)) {
            return $this->buildResponse(10001);
        }
        if (!$apiConfig['status']) {
            return $this->buildResponse(10002);
        }
        list($apiClass, $method, $arguments) = $this->getClassAndMethodAndArguments($apiConfig, $request);
        if (!$apiClass) {
            return $this->buildResponse(10003);
        }
        list($errorCode, $data) = call_user_func_array(array($apiClass, $method), $arguments);

        return $this->buildResponse($errorCode, $data);
    }

    public function getClassAndMethodAndArguments($apiConfig, $request)
    {
        return !$apiConfig['category'] ? $this->getCommonClassAndMethodAndArguments($apiConfig, $request) : ($apiConfig['category'] == 1 ? $this->getCustomizedClassAndMethodAndArguments($apiConfig, $request) : $this->getGeneralClassAndMethodAndArguments($apiConfig, $request));
    }

    public function getCommonClassAndMethodAndArguments($apiConfig, $request)
    {
        list(, $module) = explode('.', $apiConfig['name']);
        $classPath = Wind::getRealPath(sprintf('ACLOUD:api.common.%s.ACloudApiCommon%s', ACloudSysCoreDefine::ACLOUD_API_VERSION, ucfirst($module)));
        $className = sprintf('ACloudApiCommon%s', ucfirst($module));

        return $this->getRealClassAndMethodAndArguments($classPath, $className, $apiConfig['template'], $apiConfig['argument'], $request);
    }

    public function getCustomizedClassAndMethodAndArguments($apiConfig, $request)
    {
        list(, $module) = explode('.', $apiConfig['name']);
        $classPath = Wind::getRealPath(sprintf('ACLOUD:api.customized.%s.ACloudApiCustomized%s', ACloudSysCoreDefine::ACLOUD_API_VERSION, ucfirst($module)));
        $className = sprintf('ACloudApiCustomized%s', ucfirst($module));

        return $this->getRealClassAndMethodAndArguments($classPath, $className, $apiConfig['template'], $apiConfig['argument'], $request);
    }

    public function getGeneralClassAndMethodAndArguments($apiConfig, $request)
    {
        $classPath = Wind::getRealPath(sprintf('ACLOUD:api.common.%s.ACloudApiCommonGeneralApi', ACloudSysCoreDefine::ACLOUD_API_VERSION));
        list($className, $method) = array('ACloudApiCommonGeneralApi', 'get');
        list($apiClass, $method) = $this->getRealClassAndMethodAndArguments($classPath, $className, $method);
        if (!$apiClass) {
            return array('', '', array());
        }

        return array($apiClass, $method, array($apiConfig, $request));
    }

    public function getRealClassAndMethodAndArguments($classPath, $className, $method, $arguments, $request)
    {
        if (!is_file($classPath)) {
            return array('', '', array());
        }
        require_once ACloudSysCoreS::escapePath($classPath);
        if (!class_exists($className)) {
            return array('', '', array());
        }
        $apiClass = new $className ();
        if (!method_exists($apiClass, $method)) {
            return array('', '', array());
        }
        $arguments = $arguments ? explode(',', $arguments) : array();
        $arguments = $this->buildRequestArguments($arguments, $request);

        return array($apiClass, $method, $arguments);
    }

    public function buildRequestArguments($arguments, $request)
    {
        $result = array();
        $charset = ACloudSysCoreCommon::getGlobal('g_charset');

        foreach ($arguments as $arg) {
            $result[] = isset($request[$arg]) ? ACloudSysCoreCommon::convert($request[$arg], $charset, 'UTF-8') : null;
        }

        return $result;
    }

    public function buildResponse($errorCode, $responseData = array())
    {
        $response = ACloudSysCoreCommon::loadSystemClass('response');
        $response->setErrorCode($errorCode);
        $response->setResponseData($responseData);

        return $response->getOutputData();
    }
}
