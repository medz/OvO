<?php
Wind::import('SRV:report.srv.report.PwReportAction');

class PwReportMessage extends PwReportAction{
	  
	public function buildDm($type_id) {
		$result = $this->_getWindid()->getMessageById($type_id);
		if (!$result) {
			return false;
		}
		$dm = new PwReportDm();
		$dm->setContent($result['content'])
			->setAuthorUserid($result['from_uid']);
		return $dm;
	}
	
	public function getExtendReceiver() {
		return array();
	}

		
	private function _getWindid() {
		return WindidApi::api('message');
	}

}