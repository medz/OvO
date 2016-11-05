<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );
require_once Wind::getRealPath ( 'ACLOUD:system.core.ACloudSysCoreDao' );
class ACloudSysConfigDaoExtras extends ACloudSysCoreDao {
	
	private $tablename = "{{acloud_extras}}";
	
	public function insert($fields) {
		$sql = sprintf ( "REPLACE INTO %s %s", $this->tablename, $this->buildClause ( $fields ) );
		$this->query ( $sql );
		return $this->get ( $fields ['ekey'] );
	}
	
	public function update($fields, $ekey) {
		$sql = sprintf ( "UPDATE %s %s WHERE ekey = %s", $this->tablename, $this->buildClause ( $fields ), ACloudSysCoreS::sqlEscape ( $ekey ) );
		return $this->query ( $sql );
	}
	
	public function get($ekey) {
		if (! $this->checkTable ())
			return array ();
		return $this->fetchOne ( sprintf ( "SELECT * FROM %s WHERE ekey = %s", $this->tablename, ACloudSysCoreS::sqlEscape ( $ekey ) ) );
	}
	
	public function getsByKeys($keys) {
		if (! $this->checkTable ())
			return array ();
		return $this->fetchAll ( sprintf ( "SELECT * FROM %s WHERE ekey in (%s) ", $this->tablename, ACloudSysCoreS::sqlImplode ( $keys ) ) );
	}
	
	public function delete($ekey) {
		return $this->query ( sprintf ( "DELETE FROM %s WHERE ekey = %s", $this->tablename, ACloudSysCoreS::sqlEscape ( $ekey ) ) );
	}
	
	public function deleteAll() {
		return $this->query ( sprintf ( "DELETE FROM %s ", $this->tablename ) );
	}
	
	public function gets() {
		if (! $this->checkTable ())
			return array ();
		return $this->fetchAll ( sprintf ( "SELECT * FROM %s ", $this->tablename ) );
	}
	
	public function checkTable() {
		return true;
	}
}