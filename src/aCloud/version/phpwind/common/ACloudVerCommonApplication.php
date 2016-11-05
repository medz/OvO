<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );

class ACloudVerCommonApplication extends ACloudVerCommonBase {
	
	public function onlineInstall($appId) {
		$result = $this->getPwInstallApplication ()->onlineInstall ( $appId );
		if ($result instanceof PwError)
			return $this->buildResponse ( - 1, $result->getError () );
		return $this->buildResponse ( 0, $result );
	}
	
	public function localInstall($appId) {
		$result = $this->getPwInstallApplication ()->install ( $appId);
		if ($result instanceof PwError)
			return $this->buildResponse ( - 1, $result->getError () );
		return $this->buildResponse ( 0, $result );
	}
	
	public function uninstallApp($appId) {
		$result = $this->getPwUninstallApplication ()->uninstall ( $appId );
		if ($result instanceof PwError)
			return $this->buildResponse ( - 1, $result->getError () );
		return $this->buildResponse ( 0, $result );
	}
	
	public function updateApp($appId) {
		$result = $this->getPwUpdateApplication ()->upgrade ( $appId );
		if ($result instanceof PwError)
			return $this->buildResponse ( - 1, $result->getError () );
		return $this->buildResponse ( 0, $result );
	}
 	
	private function getPwInstallApplication() {
		return wekit::load ( 'SRC:applications.appcenter.service.srv.PwInstallApplication' );
	}
	
	private function getPwUninstallApplication() {
		return wekit::load ( 'SRC:applications.appcenter.service.srv.PwUninstallApplication' );
	}
	
	private function getPwUpdateApplication() {
		return wekit::load ( 'SRC:applications.appcenter.service.srv.PwUpgradeApplication' );
	}

}