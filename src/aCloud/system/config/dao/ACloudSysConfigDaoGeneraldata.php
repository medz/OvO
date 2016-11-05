<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );
require_once Wind::getRealPath ( 'ACLOUD:system.core.ACloudSysCoreDao' );
class ACloudSysConfigDaoGeneralData extends ACloudSysCoreDao {
	
	public function executeSql($sql) {
		$sql = trim ( $sql );
		if (! $sql)
			return false;
		return $this->fetchAll ( $sql );
	}
}