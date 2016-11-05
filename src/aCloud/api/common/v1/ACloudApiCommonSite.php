<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );
require_once Wind::getRealPath ( "ACLOUD_VER:common.ACloudVerCommonFactory" );
class ACloudApiCommonSite {
	
	public function getTablePartitions($type = '') {
		return $this->getVersionCommonSite ()->getTablePartitions ( $type );
	}
	
	public function getSiteVersion() {
		return $this->getVersionCommonSite ()->getSiteVersion ();
	}
	
	public function get(){
		return $this->getVersionCommonSite ()->get ();
	}
	
	private function getVersionCommonSite() {
		return ACloudVerCommonFactory::getInstance ()->getVersionCommonSite ();
	}
}
