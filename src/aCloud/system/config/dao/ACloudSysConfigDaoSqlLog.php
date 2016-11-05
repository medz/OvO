<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );
require_once Wind::getRealPath ( 'ACLOUD:system.core.ACloudSysCoreDao' );
class ACloudSysConfigDaoSqlLog extends ACloudSysCoreDao {
	
	private $tablename = "{{acloud_sql_log}}";
	
	public function insert($fields) {
		$sql = sprintf ( "INSERT INTO %s %s", $this->tablename, $this->buildClause ( $fields ) );
		return $this->query ( $sql );
	}
	
	public function update($fields, $id) {
		$sql = sprintf ( "UPDATE %s %s WHERE id = %s", $this->tablename, $this->buildClause ( $fields ), ACloudSysCoreS::sqlEscape ( $id ) );
		return $this->query ( $sql );
	}
	
	public function get($id) {
		return $this->fetchOne ( sprintf ( "SELECT * FROM %s WHERE id = %s", $this->tablename, ACloudSysCoreS::sqlEscape ( $id ) ) );
	}
	
	public function delete($id) {
		return $this->query ( sprintf ( "DELETE FROM %s WHERE id = %s", $this->tablename, ACloudSysCoreS::sqlEscape ( $id ) ) );
	}
	
	public function getSqlLogsByTimestamp($startTime, $endTime, $offset, $perpage) {
		$sqlCondition = $endTime > 0 ? ' AND created_time <= ' . ACloudSysCoreS::sqlEscape ( $endTime ) : '';
		return $this->fetchAll ( sprintf ( "SELECT * FROM %s WHERE created_time >= %s $sqlCondition %s", $this->tablename, ACloudSysCoreS::sqlEscape ( $startTime ), ACloudSysCoreS::sqlLimit ( $offset, $perpage ) ), 'id' );
	}
	
	public function countSqlLogsByTimestamp($startTime, $endTime) {
		$sqlCondition = $endTime > 0 ? ' AND created_time <= ' . ACloudSysCoreS::sqlEscape ( $endTime ) : '';
		return $this->getField ( sprintf ( "SELECT COUNT(*) as count FROM %s WHERE created_time >= %s $sqlCondition", $this->tablename, ACloudSysCoreS::sqlEscape ( $startTime ) ) );
	}
	
	public function deleteSqlLogByTimestamp($startTime, $endTime) {
		return $this->query ( sprintf ( "DELETE FROM %s WHERE created_time >= %s AND created_time <= %s", $this->tablename, ACloudSysCoreS::sqlEscape ( $startTime ), ACloudSysCoreS::sqlEscape ( $endTime ) ) );
	}
	
	public function getAllSqlLogs() {
		return $this->fetchAll ( sprintf ( "SELECT * FROM %s", $this->tablename ), 'id' );
	}
	
	public function deleteSqlLogsByIds($ids) {
		if (! ACloudSysCoreS::isArray ( $ids ))
			return false;
		return $this->query ( sprintf ( "DELETE FROM %s WHERE id IN (%s)", $this->tablename, ACloudSysCoreS::sqlImplode ( $ids ) ) );
	}
}