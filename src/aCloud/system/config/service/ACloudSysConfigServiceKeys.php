<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );

class ACloudSysConfigServiceKeys {
	
	public function updateKey6($id) {
		$id = intval ( $id );
		$this->getKeysDao ()->update ( array ('key6' => ACloudSysCoreCommon::randCode ( 128 ) ), $id );
		return $this->getKey6 ( $id );
	}
	
	public function getKey6($id) {
		$id = intval ( $id );
		$key = $this->getKeysDao ()->get ( $id );
		return ($key && $key ['key6'] && strlen ( $key ['key6'] ) == 128) ? $key ['key6'] : '';
	}
	
	public function getKey1($id) {
		$id = intval ( $id );
		$key = $this->getKeysDao ()->get ( $id );
		return ($key && $key ['key1'] && strlen ( $key ['key1'] ) == 128) ? $key ['key1'] : '';
	}
	
	public function updateKey123($id, $key1, $key2, $key3) {
		if (strlen ( $key1 ) != 128 || strlen ( $key2 ) != 128 || strlen ( $key3 ) != 128)
			return false;
		return $this->getKeysDao ()->update ( array ('key1' => $key1, 'key2' => $key2, 'key3' => $key3, 'modified_time' => time () ), $id );
	}
	
	public function updateKey456($id, $key4, $key5, $key6) {
		if (strlen ( $key4 ) != 128 || strlen ( $key5 ) != 128 || strlen ( $key6 ) != 128)
			return false;
		return $this->getKeysDao ()->update ( array ('key4' => $key4, 'key5' => $key5, 'key6' => $key6, 'modified_time' => time () ), $id );
	}
	
	public function getKey123($id) {
		return $this->getKeysDao ()->get ( $id );
	}
	
	private function getKeysDao() {
		return ACloudSysCoreCommon::loadSystemClass ( 'keys', 'config.dao' );
	}
}