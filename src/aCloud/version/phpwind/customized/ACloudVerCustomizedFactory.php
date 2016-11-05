<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );
require_once Wind::getRealPath ( 'ACLOUD_VER:customized.ACloudVerCustomizedBase' );
class ACloudVerCustomizedFactory {
	
	private $service = array ();
	
	public function getInstance() {
		static $instance = null;
		if (! is_null ( $instance ))
			return $instance;
		$instance = new ACloudVerCustomizedFactory ();
		return $instance;
	}
	
	public function getVersionCustomizedThread() {
		if (! isset ( $this->service ['VersionCustomizedThread'] ) || ! $this->service ['VersionCustomizedThread']) {
			require_once Wind::getRealPath ( 'ACLOUD_VER:customized.ACloudVerCustomizedThread' );
			$this->service ['VersionCustomizedThread'] = new ACloudVerCustomizedThread ();
		}
		return $this->service ['VersionCustomizedThread'];
	}
	
	public function getVersionCustomizedPost() {
		if (! isset ( $this->service ['VersionCustomizedPost'] ) || ! $this->service ['VersionCustomizedPost']) {
			require_once Wind::getRealPath ( 'ACLOUD_VER:customized.ACloudVerCustomizedPost' );
			$this->service ['VersionCustomizedPost'] = new ACloudVerCustomizedPost ();
		}
		return $this->service ['VersionCustomizedPost'];
	}
	
	public function getVersionCustomizedUser() {
		if (! isset ( $this->service ['VersionCustomizedUser'] ) || ! $this->service ['VersionCustomizedUser']) {
			require_once Wind::getRealPath ( 'ACLOUD_VER:customized.ACloudVerCustomizedUser' );
			$this->service ['VersionCustomizedUser'] = new ACloudVerCustomizedUser ();
		}
		return $this->service ['VersionCustomizedUser'];
	}
	
	public function getVersionCustomizedForum() {
		if (! isset ( $this->service ['VersionCustomizedForum'] ) || ! $this->service ['VersionCustomizedForum']) {
			require_once Wind::getRealPath ( 'ACLOUD_VER:customized.ACloudVerCustomizedForum' );
			$this->service ['VersionCustomizedForum'] = new ACloudVerCustomizedForum ();
		}
		return $this->service ['VersionCustomizedForum'];
	}
	
	public function getVersionCustomizedMessage() {
		if (! isset ( $this->service ['VersionCustomizedMessage'] ) || ! $this->service ['VersionCustomizedMessage']) {
			require_once Wind::getRealPath ( 'ACLOUD_VER:customized.ACloudVerCustomizedMessage' );
			$this->service ['VersionCustomizedMessage'] = new ACloudVerCustomizedMessage ();
		}
		return $this->service ['VersionCustomizedMessage'];
	}
	
	public function getVersionCustomizedFriend() {
		if (! isset ( $this->service ['VersionCustomizedFriend'] ) || ! $this->service ['VersionCustomizedFriend']) {
			require_once Wind::getRealPath ( 'ACLOUD_VER:customized.ACloudVerCustomizedFriend' );
			$this->service ['VersionCustomizedFriend'] = new ACloudVerCustomizedFriend ();
		}
		return $this->service ['VersionCustomizedFriend'];
	}

	public function getVersionCustomizedCredit(){
		if (! isset ( $this->service ['VersionCustomizedCredit'] ) || ! $this->service ['VersionCustomizedCredit']) {
			require_once Wind::getRealPath ( 'ACLOUD_VER:customized.ACloudVerCustomizedCredit' );
			$this->service ['VersionCustomizedCredit'] = new ACloudVerCustomizedCredit ();
		}
		return $this->service ['VersionCustomizedCredit'];
	}
}