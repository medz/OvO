<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );

class ACloudVerCommonBase {
	
	public function __construct() {
		$daoObject = ACloudSysCoreCommon::getGlobal ( ACloudSysCoreDefine::ACLOUD_OBJECT_DAO );
		$daoObject->getDB ();
	}
	
	public function getDeletedId($startId, $endId) {
		list ( $tableName, $primaryKey ) = $this->getPrimaryKeyAndTable ();
		if (! $tableName)
			return array ();
		list ( $existIds, $allIds ) = array ($this->getIdsFromTable ( $startId, $endId ), range ( $startId, $endId ) );
		if (! ACloudSysCoreS::isArray ( $existIds ))
			return $this->formatDeleteIds ( $allIds );
		return $this->formatDeleteIds ( array_diff ( $allIds, $existIds ) );
	}
	
	public function getIdsFromTable($startId, $endId) {
		list ( $tableName, $primaryKey ) = $this->getPrimaryKeyAndTable ();
		$result = $tmpResult = array ();
		$sql = sprintf ( "SELECT %s FROM %s WHERE %s >= %s AND %s <= %s", ACloudSysCoreS::sqlMetaData ( $primaryKey ), ACloudSysCoreS::sqlMetaData ( '{{' . $tableName . '}}' ), ACloudSysCoreS::sqlMetaData ( $primaryKey ), ACloudSysCoreS::sqlEscape ( $startId ), ACloudSysCoreS::sqlMetaData ( $primaryKey ), ACloudSysCoreS::sqlEscape ( $endId ) );
		$query = Wind::getComponent ( 'db' )->query ( $sql );
		$tmpResult = $query->fetchAll ( null, PDO::FETCH_ASSOC );
		if (! ACloudSysCoreS::isArray ( $tmpResult ))
			return array ();
		foreach ( $tmpResult as $value ) {
			$result [] = $value [$primaryKey];
		}
		return $result;
	}
	
	public function formatDeleteIds($ids) {
		if (! ACloudSysCoreS::isArray ( $ids ))
			return array ();
		list ( $tableName, $primaryKey ) = $this->getPrimaryKeyAndTable ();
		$result = array ();
		foreach ( $ids as $id ) {
			$result [] = array ($primaryKey => intval ( $id ) );
		}
		return $result;
	}
	
	public function buildResponse($errorCode, $responseData = array()) {
		if ($errorCode == 0)
			return array ($errorCode, $responseData );
		$resource = Wind::getComponent ( 'i18n' );
		if (! is_array ( $responseData ))
			return array ($errorCode, $resource->getMessage ( $responseData ) );
		/* $message = array_shift ( $responseData );
		foreach ( $responseData as $key => $value ) {
			$var = is_array ( $value ) ? $value : array ();
			$message = $resource->getMessage ( $message, $var );
		} */
		list($message, $var) = $responseData;
		$message = $resource->getMessage ( $message, $var );
		return array ($errorCode, $message );
	}
}