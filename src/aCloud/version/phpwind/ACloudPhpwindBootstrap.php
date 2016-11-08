<?php

error_reporting(E_ERROR | E_PARSE);
! defined('ACLOUD_PATH') && exit('Forbidden');
require_once Wind::getRealPath(sprintf('ACLOUD:version.%s.core.ACloudVerCoreDao', ACloudSysCoreDefine::ACLOUD_VERSION));
ACloudSysCoreCommon::setGlobal(ACloudSysCoreDefine::ACLOUD_OBJECT_DAO, new ACloudVerCoreDao());
