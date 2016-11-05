<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );

class ACloudVerCommonSite extends ACloudVerCommonBase {
	
	public function getTablePartitions($type) {
	
	}
	
	public function get(){
		$data = Wekit::C ( 'site' );
		$_extrasService = ACloudSysCoreCommon::loadSystemClass ( 'extras', 'config.service' );
		$result = array();
		$result ['ifopen']   = $data['visit.state'];
		$result ['sitename'] = $data ['info.name'];
		$result ['siteurl'] = $data ['info.url'];
		$result ['charset'] = ACloudSysCoreDefine::ACLOUD_APPLY_CHARSET ? ACloudSysCoreDefine::ACLOUD_APPLY_CHARSET : $_extrasService->getExtra ( 'ac_apply_charset' );
		$result ['cookie_prefix'] = $data['cookie.pre'];
		return $this->buildResponse(0,array('siteinfo' => $result));
	}
	
	public function getSiteVersion() {
		return $this->buildResponse ( 0, NEXT_VERSION );
	}

}