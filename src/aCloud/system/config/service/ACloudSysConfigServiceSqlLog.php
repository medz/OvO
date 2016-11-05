<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );

class ACloudSysConfigServiceSqlLog {
	
	public function getSqlLogsByTimestamp($startTime, $endTime, $page, $perpage) {
		list ( $startTime, $endTime, $page, $perpage ) = array (intval ( $startTime ), intval ( $endTime ), intval ( $page ), intval ( $perpage ) );
		if ($startTime > $endTime)
			return array ();
		$page < 1 && $page = 1;
		$perpage < 1 && $perpage = 100;
		$startTime < 0 && $startTime = 0;
		$offset = ($page - 1) * $perpage;
		return $this->getSqlLogDao ()->getSqlLogsByTimestamp ( $startTime, $endTime, $offset, $perpage );
	}
	
	public function countSqlLogsByTimestamp($startTime, $endTime) {
		list ( $startTime, $endTime ) = array (intval ( $startTime ), intval ( $endTime ) );
		if ($startTime > $endTime)
			return 0;
		return $this->getSqlLogDao ()->countSqlLogsByTimestamp ( $startTime, $endTime );
	}
	
	public function deleteSqlLogByTimestamp($startTime, $endTime) {
		list ( $startTime, $endTime ) = array (intval ( $startTime ), intval ( $endTime ) );
		if ($startTime > $endTime)
			return false;
		return $this->getSqlLogDao ()->deleteSqlLogByTimestamp ( $startTime, $endTime );
	}
	
	public function getAllSqlLogs() {
		return $this->getSqlLogDao ()->getAllSqlLogs ();
	}
	
	public function addSqlLog($fields) {
		$fields = $this->checkFields ( $fields );
		if (! ACloudSysCoreS::isArray ( $fields ))
			return false;
		(! isset ( $fields ['created_time'] ) || ! $fields ['created_time']) && $fields ['created_time'] = time ();
		return $this->getSqlLogDao ()->insert ( $fields );
	}
	
	public function deleteSqlLogsByIds($ids) {
		if (! ACloudSysCoreS::isArray ( $ids ))
			return false;
		return $this->getSqlLogDao ()->deleteSqlLogsByIds ( $ids );
	}
	
	private function checkFields($fields) {
		$result = array ();
		isset ( $fields ['id'] ) && $result ['id'] = intval ( $fields ['id'] );
		isset ( $fields ['log'] ) && $result ['log'] = trim ( $fields ['log'] );
		isset ( $fields ['created_time'] ) && $result ['created_time'] = intval ( $fields ['created_time'] );
		return $result;
	}
	
	private function getSqlLogDao() {
		return ACloudSysCoreCommon::loadSystemClass ( 'sql.log', 'config.dao' );
	}
}