<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );

class ACloudSysConfigServiceAppConfigs {
	
	public function addAppConfig($fields) {
		$type = ($fields ['app_type']) ? $fields ['app_type'] : (is_array ( $fields ['app_value'] ) ? 2 : 1);
		$value = is_array ( $fields ['app_value'] ) ? serialize ( $fields ['app_value'] ) : $fields ['app_value'];
		$data = array ();
		$data ['app_id'] = $fields ['app_id'];
		$data ['app_key'] = $fields ['app_key'];
		$data ['app_value'] = $value;
		$data ['app_type'] = intval ( $type );
		$fields ['created_time'] = $fields ['modified_time'] = time ();
		return $this->getAppConfigsDao ()->insert ( $fields );
	}
	
	public function getAppConfig($appId, $appKey) {
		return $this->getAppConfigsDao ()->get ( $appId, $appKey );
	}
	
	public function getAppConfigsByAppId($appId) {
		return $this->getAppConfigsDao ()->getsByAppId ( $appId );
	}
	
	public function updateAppConfig($appId, $appKey, $appValue, $appType = 1) {
		return $this->getAppConfigsDao ()->update ( array ('app_value' => $appValue, 'app_type' => (in_array ( $appType, array (1, 2 ) ) ? $appType : 1), 'modified_time' => time () ), $appId, $appKey );
	}
	
	public function deleteAppConfig($appId, $appKey) {
		return $this->getAppConfigsDao ()->delete ( $appId, $appKey );
	}
	
	public function deleteAppConfigByAppId($appId) {
		return $this->getAppConfigsDao ()->deleteAppConfigByAppId ( $appId );
	}
	
	public function deleteAllAppConfig(){
		return $this->getAppConfigsDao ()->deleteAll ();
	}
	
	public function getAppConfigs() {
		return $this->getAppConfigsDao ()->gets ();
	}
	
	private function getAppConfigsDao() {
		return ACloudSysCoreCommon::loadSystemClass ( 'app.configs', 'config.dao' );
	}
}