<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );

class ACloudSysConfigServiceApis {
	
	public function addApi($fields) {
		$fields = $this->checkFields ( $fields );
		if (! ACloudSysCoreS::isArray ( $fields ) || ! $fields ['name'] || ! $fields ['template'])
			return false;
		(! isset ( $fields ['created_time'] ) || ! $fields ['created_time']) && $fields ['created_time'] = time ();
		(! isset ( $fields ['modified_time'] ) || ! $fields ['modified_time']) && $fields ['modified_time'] = time ();
		return $this->getApisDao ()->insert ( $fields );
	}
	
	public function getApiConfigByApiName($apiName) {
		$apiName = trim ( $apiName );
		if (! $apiName)
			return array ();
		return $this->getApisDao ()->get ( $apiName );
	}
	
	public function updateApiConfigByApiName($apiName, $fields) {
		list ( $apiName, $fields ) = array (trim ( $apiName ), $this->checkFields ( $fields ) );
		if (! $apiName || ! ACloudSysCoreS::isArray ( $fields ))
			return false;
		return $this->getApisDao ()->update ( $fields, $apiName );
	}
	
	public function deleteApiConfigByApiName($apiName) {
		$apiName = trim ( $apiName );
		if (! $apiName)
			return false;
		return $this->getApisDao ()->delete ( $apiName );
	}
	
	public function getApis() {
		return $this->getApisDao ()->gets ();
	}
	
	private function checkFields($fields) {
		$result = array ();
		isset ( $fields ['id'] ) && $result ['id'] = intval ( $fields ['id'] );
		isset ( $fields ['name'] ) && $result ['name'] = trim ( $fields ['name'] );
		isset ( $fields ['template'] ) && $result ['template'] = trim ( $fields ['template'] );
		isset ( $fields ['argument'] ) && $result ['argument'] = trim ( $fields ['argument'] );
		isset ( $fields ['argument_type'] ) && $result ['argument_type'] = trim ( $fields ['argument_type'] );
		isset ( $fields ['fields'] ) && $result ['fields'] = trim ( $fields ['fields'] );
		isset ( $fields ['status'] ) && $result ['status'] = intval ( $fields ['status'] );
		isset ( $fields ['category'] ) && $result ['category'] = intval ( $fields ['category'] );
		isset ( $fields ['created_time'] ) && $result ['created_time'] = intval ( $fields ['created_time'] );
		isset ( $fields ['modified_time'] ) && $result ['modified_time'] = intval ( $fields ['modified_time'] );
		return $result;
	}
	
	private function getApisDao() {
		return ACloudSysCoreCommon::loadSystemClass ( 'apis', 'config.dao' );
	}
}