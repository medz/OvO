<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );
require_once Wind::getRealPath ( 'ACLOUD:system.core.ACloudSysCoreDao' );
class ACloudSysConfigDaoApps extends ACloudSysCoreDao {
	
	private $tablename = "{{acloud_apps}}";
	
	public function insert($fields) {
		$sql = sprintf ( "INSERT INTO %s %s", $this->tablename, $this->buildClause ( $fields ) );
		$this->query ( $sql );
		return $this->get ( $fields ['app_id'] );
	}
	
	public function update($fields, $id) {
		$sql = sprintf ( "UPDATE %s %s WHERE app_id = %s", $this->tablename, $this->buildClause ( $fields ), ACloudSysCoreS::sqlEscape ( $id ) );
		return $this->query ( $sql );
	}
	
	public function get($id) {
		return $this->fetchOne ( sprintf ( "SELECT * FROM %s WHERE app_id = %s", $this->tablename, ACloudSysCoreS::sqlEscape ( $id ) ) );
	}
	
	public function delete($id) {
		return $this->query ( sprintf ( "DELETE FROM %s WHERE app_id = %s", $this->tablename, ACloudSysCoreS::sqlEscape ( $id ) ) );
	}
	
	public function deleteAll() {
		return $this->query ( sprintf ( "DELETE FROM %s ", $this->tablename ) );
	}
	
	public function gets() {
		return $this->fetchAll ( sprintf ( "SELECT * FROM %s ", $this->tablename ) );
	}

}