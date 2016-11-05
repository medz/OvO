<?php

! defined('ACLOUD_PATH') && exit('Forbidden');
class ACloudSysRouter
{
    public function run()
    {
        list($method) = ACloudSysCoreS::gp(array('method'));
        $method = strtolower(str_replace('.', '_', $method));
        $controlService = ACloudSysCoreCommon::loadSystemClass('control', 'verify.service');
        $control = in_array($method, array('apply_verify', 'apply_initkey', 'apply_checkkey', 'apply_success')) ? true : $controlService->doubleControl($_POST);
        $output = ($control && $method && method_exists($this, $method)) ? $this->$method () : $this->sys_error();
        print_r($output);
        exit();
    }

    private function sys_error()
    {
        return ACloudSysCoreCommon::simpleResponse(ACloudSysCoreDefine::ACLOUD_HTTP_FAIL, 'sys error');
    }

    private function config_addApp()
    {
        list($app_id, $app_name, $app_token) = ACloudSysCoreS::gp(array('app_id', 'app_name', 'app_token'));
        $initService = ACloudSysCoreCommon::loadSystemClass('apps', 'config.service');
        $fields = array('app_id' => $app_id, 'app_name' => ACloudSysCoreCommon::convertFromUTF8($app_name), 'app_token' => $app_token);
        if (! ($app = $initService->addApp($fields))) {
            return ACloudSysCoreCommon::simpleResponse(ACloudSysCoreDefine::ACLOUD_HTTP_FAIL, 'config_addApp fail');
        }

        return ACloudSysCoreCommon::simpleResponse(ACloudSysCoreDefine::ACLOUD_HTTP_OK, 'config_addApp success');
    }

    private function config_deleteAllApp()
    {
        $initService = ACloudSysCoreCommon::loadSystemClass('apps', 'config.service');
        if (! ($result = $initService->deleteAllApp())) {
            return ACloudSysCoreCommon::simpleResponse(ACloudSysCoreDefine::ACLOUD_HTTP_FAIL, 'config_deleteAllApp fail');
        }

        return ACloudSysCoreCommon::simpleResponse(ACloudSysCoreDefine::ACLOUD_HTTP_OK, 'config_deleteAllApp success');
    }

    private function config_updateApp()
    {
        list($app_id, $app_name, $app_token) = ACloudSysCoreS::gp(array('app_id', 'app_name', 'app_token'));
        $initService = ACloudSysCoreCommon::loadSystemClass('apps', 'config.service');
        $initService->updateApp(array('app_name' => ACloudSysCoreCommon::convertFromUTF8($app_name), 'app_token' => $app_token), $app_id);
        $app = $initService->getApp($app_id);
        if (! $app || $app ['app_token'] != $app_token) {
            return ACloudSysCoreCommon::simpleResponse(ACloudSysCoreDefine::ACLOUD_HTTP_FAIL, 'config_updateApp fail');
        }

        return ACloudSysCoreCommon::simpleResponse(ACloudSysCoreDefine::ACLOUD_HTTP_OK, 'config_updateApp success');
    }

    private function config_deleteApp()
    {
        list($app_id) = ACloudSysCoreS::gp(array('app_id'));
        $initService = ACloudSysCoreCommon::loadSystemClass('apps', 'config.service');
        $initService->deleteApp($app_id);
        $app = $initService->getApp($app_id);
        if ($app) {
            return ACloudSysCoreCommon::simpleResponse(ACloudSysCoreDefine::ACLOUD_HTTP_FAIL, 'config_deleteApp fail');
        }

        return ACloudSysCoreCommon::simpleResponse(ACloudSysCoreDefine::ACLOUD_HTTP_OK, 'config_deleteApp success');
    }

    private function config_getApp()
    {
        list($app_id) = ACloudSysCoreS::gp(array('app_id'));
        $initService = ACloudSysCoreCommon::loadSystemClass('apps', 'config.service');
        $app = $initService->getApp($app_id);
        if (! $app) {
            return ACloudSysCoreCommon::simpleResponse(ACloudSysCoreDefine::ACLOUD_HTTP_FAIL, 'config_getApp fail');
        }

        return ACloudSysCoreCommon::simpleResponse(ACloudSysCoreDefine::ACLOUD_HTTP_OK, $app);
    }

    private function config_getApps()
    {
        $initService = ACloudSysCoreCommon::loadSystemClass('apps', 'config.service');
        $apps = $initService->getApps();
        if (! $apps) {
            return ACloudSysCoreCommon::simpleResponse(ACloudSysCoreDefine::ACLOUD_HTTP_FAIL, 'config_getApps fail');
        }

        return ACloudSysCoreCommon::simpleResponse(ACloudSysCoreDefine::ACLOUD_HTTP_OK, $apps);
    }

    private function config_addApi()
    {
        list($name, $template, $argument, $argumentType, $fields, $status, $category, $createdTime, $modifiedTime) = ACloudSysCoreS::gp(array('name', 'template', 'argument', 'argumentType', 'fields', 'status', 'category', 'createdTime', 'modifiedTime'));
        $configApiService = ACloudSysCoreCommon::loadSystemClass('apis', 'config.service');
        $fields = array('name' => $name, 'template' => $template, 'argument' => $argument, 'argument_type' => $argumentType, 'fields' => $fields, 'status' => $status, 'category' => $category, 'created_time' => $createdTime, 'modified_time' => $modifiedTime);
        if (! ($result = $configApiService->addApi($fields))) {
            return ACloudSysCoreCommon::simpleResponse(ACloudSysCoreDefine::ACLOUD_HTTP_FAIL, 'config_addApi fail');
        }

        return ACloudSysCoreCommon::simpleResponse(ACloudSysCoreDefine::ACLOUD_HTTP_OK, 'config_addApi success');
    }

    private function config_updateApi()
    {
        list($name, $template, $argument, $argumentType, $fields, $status, $category, $createdTime, $modifiedTime) = ACloudSysCoreS::gp(array('name', 'template', 'argument', 'argumentType', 'fields', 'status', 'category', 'createdTime', 'modifiedTime'));
        $configApiService = ACloudSysCoreCommon::loadSystemClass('apis', 'config.service');
        $fields = array('template' => $template, 'argument' => $argument, 'argument_type' => $argumentType, 'fields' => $fields, 'status' => $status, 'category' => $category, 'created_time' => $createdTime, 'modified_time' => $modifiedTime);
        if (! ($result = $configApiService->updateApiConfigByApiName($name, $fields))) {
            return ACloudSysCoreCommon::simpleResponse(ACloudSysCoreDefine::ACLOUD_HTTP_FAIL, 'config_updateApi fail');
        }

        return ACloudSysCoreCommon::simpleResponse(ACloudSysCoreDefine::ACLOUD_HTTP_OK, 'config_updateApi success');
    }

    private function config_deleteApi()
    {
        list($name) = ACloudSysCoreS::gp(array('name'));
        $configApiService = ACloudSysCoreCommon::loadSystemClass('apis', 'config.service');
        if (! ($result = $configApiService->deleteApiConfigByApiName($name))) {
            return ACloudSysCoreCommon::simpleResponse(ACloudSysCoreDefine::ACLOUD_HTTP_FAIL, 'config_deleteApi fail');
        }

        return ACloudSysCoreCommon::simpleResponse(ACloudSysCoreDefine::ACLOUD_HTTP_OK, 'config_deleteApi success');
    }

    private function config_getApi()
    {
        list($name) = ACloudSysCoreS::gp(array('name'));
        $configApiService = ACloudSysCoreCommon::loadSystemClass('apis', 'config.service');
        $apis = $configApiService->getApiConfigByApiName($name);
        if (! $apis) {
            return ACloudSysCoreCommon::simpleResponse(ACloudSysCoreDefine::ACLOUD_HTTP_FAIL, 'config_getApi fail');
        }

        return ACloudSysCoreCommon::simpleResponse(ACloudSysCoreDefine::ACLOUD_HTTP_OK, $apis);
    }

    private function config_getApis()
    {
        $configApiService = ACloudSysCoreCommon::loadSystemClass('apis', 'config.service');
        $apis = $configApiService->getApis();
        if (! $apis) {
            return ACloudSysCoreCommon::simpleResponse(ACloudSysCoreDefine::ACLOUD_HTTP_FAIL, 'config_getApis fail');
        }

        return ACloudSysCoreCommon::simpleResponse(ACloudSysCoreDefine::ACLOUD_HTTP_OK, $apis);
    }

    private function config_addTableSetting()
    {
        list($name, $status, $category, $primaryKey, $createdTime, $modifiedTime) = ACloudSysCoreS::gp(array('name', 'status', 'category', 'primaryKey', 'createdTime', 'modifiedTime'));
        $tableSettingsService = ACloudSysCoreCommon::loadSystemClass('table.settings', 'config.service');
        $fields = array('name' => $name, 'status' => $status, 'category' => $category, 'primary_key' => $primaryKey, 'created_time' => $createdTime, 'modified_time' => $modifiedTime);
        if (! ($result = $tableSettingsService->addTableSetting($fields))) {
            return ACloudSysCoreCommon::simpleResponse(ACloudSysCoreDefine::ACLOUD_HTTP_FAIL, 'config_addTableSetting fail');
        }

        return ACloudSysCoreCommon::simpleResponse(ACloudSysCoreDefine::ACLOUD_HTTP_OK, 'config_addTableSetting success');
    }

    private function config_updateTableSetting()
    {
        list($name, $status, $category, $primaryKey, $createdTime, $modifiedTime) = ACloudSysCoreS::gp(array('name', 'status', 'category', 'primaryKey', 'createdTime', 'modifiedTime'));
        $tableSettingsService = ACloudSysCoreCommon::loadSystemClass('table.settings', 'config.service');
        $fields = array('status' => $status, 'category' => $category, 'primary_key' => $primaryKey, 'created_time' => $createdTime, 'modified_time' => $modifiedTime);
        if (! ($result = $tableSettingsService->updateTableSettingByTableName($name, $fields))) {
            return ACloudSysCoreCommon::simpleResponse(ACloudSysCoreDefine::ACLOUD_HTTP_FAIL, 'config_updateTableSetting fail');
        }

        return ACloudSysCoreCommon::simpleResponse(ACloudSysCoreDefine::ACLOUD_HTTP_OK, 'config_updateTableSetting success');
    }

    private function config_deleteTableSetting()
    {
        list($name) = ACloudSysCoreS::gp(array('name'));
        $tableSettingsService = ACloudSysCoreCommon::loadSystemClass('table.settings', 'config.service');
        if (! ($result = $tableSettingsService->deleteTableSettingByTableName($name))) {
            return ACloudSysCoreCommon::simpleResponse(ACloudSysCoreDefine::ACLOUD_HTTP_FAIL, 'config_deleteTableSetting fail');
        }

        return ACloudSysCoreCommon::simpleResponse(ACloudSysCoreDefine::ACLOUD_HTTP_OK, 'config_deleteTableSetting success');
    }

    private function config_getTableSetting()
    {
        list($name) = ACloudSysCoreS::gp(array('name'));
        $tableSettingsService = ACloudSysCoreCommon::loadSystemClass('table.settings', 'config.service');
        $tableSetting = $tableSettingsService->getSettingByTableName($name);
        if (! $tableSetting) {
            return ACloudSysCoreCommon::simpleResponse(ACloudSysCoreDefine::ACLOUD_HTTP_FAIL, 'config_getTableSetting fail');
        }

        return ACloudSysCoreCommon::simpleResponse(ACloudSysCoreDefine::ACLOUD_HTTP_OK, $tableSetting);
    }

    private function config_getTableSettings()
    {
        $tableSettingsService = ACloudSysCoreCommon::loadSystemClass('table.settings', 'config.service');
        $tableSettings = $tableSettingsService->getTableSettings();
        if (! $tableSettings) {
            return ACloudSysCoreCommon::simpleResponse(ACloudSysCoreDefine::ACLOUD_HTTP_FAIL, 'config_getTableSettings fail');
        }

        return ACloudSysCoreCommon::simpleResponse(ACloudSysCoreDefine::ACLOUD_HTTP_OK, $tableSettings);
    }

    private function config_setExtra()
    {
        list($ekey, $evalue, $etype) = ACloudSysCoreS::gp(array('ekey', 'evalue', 'etype'));
        $extrasService = ACloudSysCoreCommon::loadSystemClass('extras', 'config.service');
        if (! ($extra = $extrasService->setExtra($ekey, $evalue, $etype))) {
            return ACloudSysCoreCommon::simpleResponse(ACloudSysCoreDefine::ACLOUD_HTTP_FAIL, 'config_setExtra fail');
        }

        return ACloudSysCoreCommon::simpleResponse(ACloudSysCoreDefine::ACLOUD_HTTP_OK, $extra);
    }

    private function config_getExtra()
    {
        list($ekey) = ACloudSysCoreS::gp(array('ekey'));
        $extrasService = ACloudSysCoreCommon::loadSystemClass('extras', 'config.service');
        if (! ($evalue = $extrasService->getExtra($ekey))) {
            return ACloudSysCoreCommon::simpleResponse(ACloudSysCoreDefine::ACLOUD_HTTP_FAIL, 'config_getExtra fail');
        }

        return ACloudSysCoreCommon::simpleResponse(ACloudSysCoreDefine::ACLOUD_HTTP_OK, $evalue);
    }

    private function config_getExtras()
    {
        $extrasService = ACloudSysCoreCommon::loadSystemClass('extras', 'config.service');
        if (! ($extras = $extrasService->getExtras())) {
            return ACloudSysCoreCommon::simpleResponse(ACloudSysCoreDefine::ACLOUD_HTTP_FAIL, 'config_getExtras fail');
        }

        return ACloudSysCoreCommon::simpleResponse(ACloudSysCoreDefine::ACLOUD_HTTP_OK, $extras);
    }

    private function config_deleteAllExtras()
    {
        $extrasService = ACloudSysCoreCommon::loadSystemClass('extras', 'config.service');
        if (! ($result = $extrasService->deleteAllExtras())) {
            return ACloudSysCoreCommon::simpleResponse(ACloudSysCoreDefine::ACLOUD_HTTP_FAIL, 'config_deleteExtras fail');
        }

        return ACloudSysCoreCommon::simpleResponse(ACloudSysCoreDefine::ACLOUD_HTTP_OK, 'config_deleteExtras success');
    }

    private function config_addAppConfig()
    {
        list($app_id, $app_key, $app_value, $app_type) = ACloudSysCoreS::gp(array('app_id', 'app_key', 'app_value', 'app_type'));
        $appConfigService = ACloudSysCoreCommon::loadSystemClass('app.configs', 'config.service');
        if (! ($config = $appConfigService->addAppConfig(array('app_id' => $app_id, 'app_key' => $app_key, 'app_value' => $app_value, 'app_type' => $app_type)))) {
            return ACloudSysCoreCommon::simpleResponse(ACloudSysCoreDefine::ACLOUD_HTTP_FAIL, 'config_addAppConfig fail');
        }

        return ACloudSysCoreCommon::simpleResponse(ACloudSysCoreDefine::ACLOUD_HTTP_OK, $config);
    }

    private function config_getAppConfig()
    {
        list($app_id, $app_key) = ACloudSysCoreS::gp(array('app_id', 'app_key'));
        $appConfigService = ACloudSysCoreCommon::loadSystemClass('app.configs', 'config.service');
        if (! ($config = $appConfigService->getAppConfig($app_id, $app_key))) {
            return ACloudSysCoreCommon::simpleResponse(ACloudSysCoreDefine::ACLOUD_HTTP_FAIL, 'config_addAppConfig fail');
        }

        return ACloudSysCoreCommon::simpleResponse(ACloudSysCoreDefine::ACLOUD_HTTP_OK, $config);
    }

    private function config_getAppConfigsByAppId()
    {
        list($app_id) = ACloudSysCoreS::gp(array('app_id'));
        $appConfigService = ACloudSysCoreCommon::loadSystemClass('app.configs', 'config.service');
        if (! ($configs = $appConfigService->getAppConfigsByAppId($app_id))) {
            return ACloudSysCoreCommon::simpleResponse(ACloudSysCoreDefine::ACLOUD_HTTP_FAIL, 'config_getAppConfigsByAppId fail');
        }

        return ACloudSysCoreCommon::simpleResponse(ACloudSysCoreDefine::ACLOUD_HTTP_OK, $configs);
    }

    private function config_updateAppConfig()
    {
        list($app_id, $app_key, $app_value) = ACloudSysCoreS::gp(array('app_id', 'app_key', 'app_value'));
        $appConfigService = ACloudSysCoreCommon::loadSystemClass('app.configs', 'config.service');
        if (! ($config = $appConfigService->updateAppConfig($app_id, $app_key, $app_value))) {
            return ACloudSysCoreCommon::simpleResponse(ACloudSysCoreDefine::ACLOUD_HTTP_FAIL, 'config_updateAppConfig fail');
        }

        return ACloudSysCoreCommon::simpleResponse(ACloudSysCoreDefine::ACLOUD_HTTP_OK, $config);
    }

    private function config_deleteAppConfig()
    {
        list($app_id, $app_key) = ACloudSysCoreS::gp(array('app_id', 'app_key'));
        $appConfigService = ACloudSysCoreCommon::loadSystemClass('app.configs', 'config.service');
        if (! ($config = $appConfigService->deleteAppConfig($app_id, $app_key))) {
            return ACloudSysCoreCommon::simpleResponse(ACloudSysCoreDefine::ACLOUD_HTTP_FAIL, 'config_deleteAppConfig fail');
        }

        return ACloudSysCoreCommon::simpleResponse(ACloudSysCoreDefine::ACLOUD_HTTP_OK, $config);
    }

    private function config_deleteAllAppConfig()
    {
        $appConfigService = ACloudSysCoreCommon::loadSystemClass('app.configs', 'config.service');
        if (! ($result = $appConfigService->deleteAllAppConfig())) {
            return ACloudSysCoreCommon::simpleResponse(ACloudSysCoreDefine::ACLOUD_HTTP_FAIL, 'config_deleteAllAppConfig fail');
        }

        return ACloudSysCoreCommon::simpleResponse(ACloudSysCoreDefine::ACLOUD_HTTP_OK, 'config_deleteAllAppConfig success');
    }

    private function config_deleteAppConfigByAppId()
    {
        list($app_id) = ACloudSysCoreS::gp(array('app_id'));
        $appConfigService = ACloudSysCoreCommon::loadSystemClass('app.configs', 'config.service');
        if (! ($config = $appConfigService->deleteAppConfigByAppId($app_id))) {
            return ACloudSysCoreCommon::simpleResponse(ACloudSysCoreDefine::ACLOUD_HTTP_FAIL, 'config_deleteAppConfigByAppId fail');
        }

        return ACloudSysCoreCommon::simpleResponse(ACloudSysCoreDefine::ACLOUD_HTTP_OK, $config);
    }

    private function config_getAppConfigs()
    {
        $appConfigService = ACloudSysCoreCommon::loadSystemClass('app.configs', 'config.service');
        if (! ($configs = $appConfigService->getAppConfigs())) {
            return ACloudSysCoreCommon::simpleResponse(ACloudSysCoreDefine::ACLOUD_HTTP_FAIL, 'config_getAppConfigs fail');
        }

        return ACloudSysCoreCommon::simpleResponse(ACloudSysCoreDefine::ACLOUD_HTTP_OK, $configs);
    }

    private function apply_verify()
    {
        $controlService = ACloudSysCoreCommon::loadSystemClass('control', 'verify.service');
        if (! $controlService->ipControl()) {
            return ACloudSysCoreCommon::simpleResponse(ACloudSysCoreDefine::ACLOUD_HTTP_FAIL, 'apply_verify_ipcontrol_fail');
        }

        $applyService = ACloudSysCoreCommon::loadSystemClass('apply', 'open.service');
        $bool = $applyService->verifying($_POST);
        if (! $bool) {
            return ACloudSysCoreCommon::simpleResponse(ACloudSysCoreDefine::ACLOUD_HTTP_FAIL, 'apply_verify_fail ');
        }

        return ACloudSysCoreCommon::simpleResponse(ACloudSysCoreDefine::ACLOUD_HTTP_OK, 'apply_verify_success ');
    }

    private function apply_initKey()
    {
        $controlService = ACloudSysCoreCommon::loadSystemClass('control', 'verify.service');
        if (! $controlService->ipControl()) {
            return ACloudSysCoreCommon::simpleResponse(ACloudSysCoreDefine::ACLOUD_HTTP_FAIL, 'apply_initkey_ipcontrol_fail');
        }
        $initService = ACloudSysCoreCommon::loadSystemClass('init', 'open.service');
        list($bool, $message) = $initService->initSecretKey(array_merge($_GET, $_POST));
        if (! $bool) {
            return ACloudSysCoreCommon::simpleResponse(ACloudSysCoreDefine::ACLOUD_HTTP_FAIL, $message);
        }

        return ACloudSysCoreCommon::simpleResponse(ACloudSysCoreDefine::ACLOUD_HTTP_OK, 'apply_initkey_success');
    }

    private function apply_checkKey()
    {
        $controlService = ACloudSysCoreCommon::loadSystemClass('control', 'verify.service');
        if (! $controlService->ipControl()) {
            return ACloudSysCoreCommon::simpleResponse(ACloudSysCoreDefine::ACLOUD_HTTP_FAIL, 'apply_checkkey_ipcontrol_fail');
        }
        $initService = ACloudSysCoreCommon::loadSystemClass('init', 'open.service');
        list($bool, $message) = $initService->checkSecretKey(array_merge($_GET, $_POST));
        if (! $bool) {
            return ACloudSysCoreCommon::simpleResponse(ACloudSysCoreDefine::ACLOUD_HTTP_FAIL, $message);
        }

        return ACloudSysCoreCommon::simpleResponse(ACloudSysCoreDefine::ACLOUD_HTTP_OK, 'apply_checkkey_success');
    }

    private function apply_success()
    {
        $applyService = ACloudSysCoreCommon::loadSystemClass('apply', 'open.service');
        if (! $applyService->applySuccess()) {
            return ACloudSysCoreCommon::simpleResponse(ACloudSysCoreDefine::ACLOUD_HTTP_FAIL, 'apply_success_fail');
        }

        return ACloudSysCoreCommon::simpleResponse(ACloudSysCoreDefine::ACLOUD_HTTP_OK, 'apply_success_success');
    }

    private function env_checkFunctions()
    {
        $openService = ACloudSysCoreCommon::loadSystemClass('env', 'open.service');

        return ACloudSysCoreCommon::simpleResponse(ACloudSysCoreDefine::ACLOUD_HTTP_OK, $openService->checkFunctions());
    }

    private function env_getNetWorkSpeed()
    {
        $openService = ACloudSysCoreCommon::loadSystemClass('env', 'open.service');

        return ACloudSysCoreCommon::simpleResponse(ACloudSysCoreDefine::ACLOUD_HTTP_OK, $openService->getNetWorkSpeed());
    }

    private function env_getServerInfo()
    {
        $openService = ACloudSysCoreCommon::loadSystemClass('env', 'open.service');

        return ACloudSysCoreCommon::simpleResponse(ACloudSysCoreDefine::ACLOUD_HTTP_OK, $openService->getServerInfo());
    }

    private function env_getFilesInfo()
    {
        $openService = ACloudSysCoreCommon::loadSystemClass('env', 'open.service');

        return ACloudSysCoreCommon::simpleResponse(ACloudSysCoreDefine::ACLOUD_HTTP_OK, $openService->getFilesInfo());
    }

    private function dataflow_crawlTable()
    {
        list($tableName, $page, $perpage) = ACloudSysCoreS::gp(array('tablename', 'page', 'perpage'));
        $dataFlowService = ACloudSysCoreCommon::loadSystemClass('crawler', 'dataflow.service');

        return $dataFlowService->crawlTable($tableName, $page, $perpage);
    }

    private function dataflow_crawlTableMaxId()
    {
        list($tableName) = ACloudSysCoreS::gp(array('tablename'));
        $dataFlowService = ACloudSysCoreCommon::loadSystemClass('crawler', 'dataflow.service');

        return $dataFlowService->crawlTableMaxId($tableName);
    }

    private function dataflow_crawlPostMaxId()
    {
        $dataFlowService = ACloudSysCoreCommon::loadSystemClass('crawler', 'dataflow.service');

        return $dataFlowService->crawlPostMaxId();
    }

    private function dataflow_crawlTableByIdRange()
    {
        list($tableName, $startId, $endId) = ACloudSysCoreS::gp(array('tablename', 'startid', 'endid'));
        $dataFlowService = ACloudSysCoreCommon::loadSystemClass('crawler', 'dataflow.service');

        return $dataFlowService->crawlTableByIdRange($startId, $endId, $tableName);
    }

    private function dataflow_crawlThreadDelta()
    {
        list($startTime, $endTime, $page, $perpage) = ACloudSysCoreS::gp(array('starttime', 'endtime', 'page', 'perpage'));
        $dataFlowService = ACloudSysCoreCommon::loadSystemClass('crawler', 'dataflow.service');

        return $dataFlowService->crawlThreadDelta($startTime, $endTime, $page, $perpage);
    }

    private function dataflow_crawlThreadDeltaCount()
    {
        list($startTime, $endTime) = ACloudSysCoreS::gp(array('starttime', 'endtime'));
        $dataFlowService = ACloudSysCoreCommon::loadSystemClass('crawler', 'dataflow.service');

        return $dataFlowService->crawlThreadDeltaCount($startTime, $endTime);
    }

    private function dataflow_crawlSqlLog()
    {
        list($startTime, $endTime, $page, $perpage) = ACloudSysCoreS::gp(array('starttime', 'endtime', 'page', 'perpage'));
        $dataFlowService = ACloudSysCoreCommon::loadSystemClass('crawler', 'dataflow.service');

        return $dataFlowService->crawlSqlLog($startTime, $endTime, $page, $perpage);
    }

    private function dataflow_crawlSqlLogCount()
    {
        list($startTime, $endTime) = ACloudSysCoreS::gp(array('starttime', 'endtime'));
        $dataFlowService = ACloudSysCoreCommon::loadSystemClass('crawler', 'dataflow.service');

        return $dataFlowService->crawlSqlLogCount($startTime, $endTime);
    }

    private function dataflow_deleteSqlLog()
    {
        list($startTime, $endTime) = ACloudSysCoreS::gp(array('starttime', 'endtime'));
        $dataFlowService = ACloudSysCoreCommon::loadSystemClass('crawler', 'dataflow.service');

        return $dataFlowService->deleteSqlLog($startTime, $endTime);
    }

    private function dataflow_crawlDeletedId()
    {
        list($type, $startId, $endId) = ACloudSysCoreS::gp(array('type', 'startid', 'endid'));
        $dataFlowService = ACloudSysCoreCommon::loadSystemClass('crawler', 'dataflow.service');

        return $dataFlowService->crawlDeletedId($type, $startId, $endId);
    }

    private function dataflow_crawlThreadRange()
    {
        list($startId, $endId) = ACloudSysCoreS::gp(array('startid', 'endid'));
        $dataFlowService = ACloudSysCoreCommon::loadSystemClass('crawler', 'dataflow.service');

        return $dataFlowService->crawlThreadRange($startId, $endId);
    }

    private function dataflow_crawlMemberRange()
    {
        list($startId, $endId) = ACloudSysCoreS::gp(array('startid', 'endid'));
        $dataFlowService = ACloudSysCoreCommon::loadSystemClass('crawler', 'dataflow.service');

        return $dataFlowService->crawlMemberRange($startId, $endId);
    }

    private function dataflow_crawlPostRange()
    {
        list($startId, $endId) = ACloudSysCoreS::gp(array('startid', 'endid'));
        $dataFlowService = ACloudSysCoreCommon::loadSystemClass('crawler', 'dataflow.service');

        return $dataFlowService->crawlPostRange($startId, $endId);
    }

    private function dataflow_crawlAttachRange()
    {
        list($startId, $endId) = ACloudSysCoreS::gp(array('startid', 'endid'));
        $dataFlowService = ACloudSysCoreCommon::loadSystemClass('crawler', 'dataflow.service');

        return $dataFlowService->crawlAttachRange($startId, $endId);
    }

    private function dataflow_crawlForumRange()
    {
        list($startId, $endId) = ACloudSysCoreS::gp(array('startid', 'endid'));
        $dataFlowService = ACloudSysCoreCommon::loadSystemClass('crawler', 'dataflow.service');

        return $dataFlowService->crawlForumRange($startId, $endId);
    }

    private function dataflow_crawlDiaryRange()
    {
        list($startId, $endId) = ACloudSysCoreS::gp(array('startid', 'endid'));
        $dataFlowService = ACloudSysCoreCommon::loadSystemClass('crawler', 'dataflow.service');

        return $dataFlowService->crawlDiaryRange($startId, $endId);
    }

    private function dataflow_crawlColonyRange()
    {
        list($startId, $endId) = ACloudSysCoreS::gp(array('startid', 'endid'));
        $dataFlowService = ACloudSysCoreCommon::loadSystemClass('crawler', 'dataflow.service');

        return $dataFlowService->crawlColonyRange($startId, $endId);
    }

    private function app_onlineInstall()
    {
        require_once Wind::getRealPath('ACLOUD_VER:common.ACloudVerCommonFactory');
        list($appId) = ACloudSysCoreS::gp(array('appid'));
        $commonFactory = ACloudVerCommonFactory::getInstance();
        $commonApplication = $commonFactory->getVersionCommonApplication();
        list($bool, $response) = $commonApplication->onlineInstall($appId);
        if ($bool === - 1) {
            return ACloudSysCoreCommon::simpleResponse(ACloudSysCoreDefine::ACLOUD_HTTP_FAIL, $response);
        }

        return ACloudSysCoreCommon::simpleResponse(ACloudSysCoreDefine::ACLOUD_HTTP_OK, $response);
    }

    private function app_localInstall()
    {
        require_once Wind::getRealPath('ACLOUD_VER:common.ACloudVerCommonFactory');
        list($appId, $hash) = ACloudSysCoreS::gp(array('appid', 'hash'));
        $commonFactory = ACloudVerCommonFactory::getInstance();
        $commonApplication = $commonFactory->getVersionCommonApplication();
        list($bool, $response) = $commonApplication->localInstall($appId, $hash);
        if ($bool === - 1) {
            return ACloudSysCoreCommon::simpleResponse(ACloudSysCoreDefine::ACLOUD_HTTP_FAIL, $response);
        }

        return ACloudSysCoreCommon::simpleResponse(ACloudSysCoreDefine::ACLOUD_HTTP_OK, $response);
    }

    private function app_uninstall()
    {
        require_once Wind::getRealPath('ACLOUD_VER:common.ACloudVerCommonFactory');
        list($appId) = ACloudSysCoreS::gp(array('appid'));
        $commonFactory = ACloudVerCommonFactory::getInstance();
        $commonApplication = $commonFactory->getVersionCommonApplication();
        list($bool, $response) = $commonApplication->uninstallApp($appId);
        if ($bool === - 1) {
            return ACloudSysCoreCommon::simpleResponse(ACloudSysCoreDefine::ACLOUD_HTTP_FAIL, $response);
        }

        return ACloudSysCoreCommon::simpleResponse(ACloudSysCoreDefine::ACLOUD_HTTP_OK, $response);
    }

    private function app_update()
    {
        require_once Wind::getRealPath('ACLOUD_VER:common.ACloudVerCommonFactory');
        list($appId) = ACloudSysCoreS::gp(array('appid'));
        $commonFactory = ACloudVerCommonFactory::getInstance();
        $commonApplication = $commonFactory->getVersionCommonApplication();
        list($bool, $response) = $commonApplication->updateApp($appId);
        if ($bool === - 1) {
            return ACloudSysCoreCommon::simpleResponse(ACloudSysCoreDefine::ACLOUD_HTTP_FAIL, $response);
        }

        return ACloudSysCoreCommon::simpleResponse(ACloudSysCoreDefine::ACLOUD_HTTP_OK, $response);
    }

    private function storage_getAttachDirectories()
    {
        $dataFlowService = ACloudSysCoreCommon::loadSystemClass('crawler', 'dataflow.service');

        return $dataFlowService->getAttachDirectoriesForStorage();
    }

    private function storage_getAttaches()
    {
        list($directory) = ACloudSysCoreS::gp(array('dir'));
        $dataFlowService = ACloudSysCoreCommon::loadSystemClass('crawler', 'dataflow.service');

        return $dataFlowService->getAttachesForStorage($directory);
    }
}
