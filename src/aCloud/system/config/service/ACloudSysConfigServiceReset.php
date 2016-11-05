<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );

class ACloudSysConfigServiceReSet {
	
	public function resetConfig() {
		ACloudSysCoreCommon::loadSystemClass ( 'extras', 'config.dao' )->deleteAll ();
		ACloudSysCoreCommon::loadSystemClass ( 'apps', 'config.dao' )->deleteAll ();
		ACloudSysCoreCommon::loadSystemClass ( 'app.configs', 'config.dao' )->deleteAll ();
		return true;
	}
}