<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );
class ACloudSysCoreResponse {
	
	private $errorCode = '';
	private $responseData = '';
	
	public function setErrorCode($errorCode) {
		$this->errorCode = $errorCode;
		return true;
	}
	
	public function getErrorCode() {
		return $this->errorCode;
	}
	
	public function setResponseData($responseData) {
		$this->responseData = $responseData;
		return true;
	}
	
	public function getResponseData() {
		return $this->responseData;
	}
	
	public function getOutputData() {
		$format = ACloudSysCoreCommon::getGlobal ( 'acloud_api_output_format' );
		list ( $data, $charset ) = array (array ('code' => $this->getErrorCode (), 'info' => $this->getResponseData () ), ACloudSysCoreCommon::getGlobal ( 'g_charset' ) );
		return ACloudSysCoreCommon::loadSystemClass ( 'format' )->format ( $data, $format, $charset );
	}
}