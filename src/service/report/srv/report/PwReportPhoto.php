<?php
Wind::import('SRV:report.srv.report.PwReportAction');
/**
 * 照片举报
 *
 * @author Shi Long <long.shi@alibaba-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $id$
 * @package album.service.srv
 */
class PwReportPhoto extends PwReportAction {
	/* (non-PHPdoc)
	 * @see PwReportAction::buildDm()
	 */
	public function buildDm($type_id) {
		$photo = $this->_service()->getPhotoInfo($type_id);
		if (!$photo) return false;
		$content = "照片({$photo['name']})";
		$url = WindUrlHelper::createUrl('album/space/view', array('photoid' => $type_id, 'uid' => $photo['created_uid']));
		$dm = new PwReportDm();
		$dm->setAuthorUserid($photo['created_uid'])
		   ->setContent($content)
		   ->setContentUrl($url);
		return $dm;
	}

	/* (non-PHPdoc)
	 * @see PwReportAction::getExtendReceiver()
	 */
	public function getExtendReceiver() {
		return array();
	}
	
	/**
	 * @return PwPhotoJoin
	 */
	private function _service() {
		return Wekit::load('SRC:extensions.album.service.PwPhotoJoin');
	}

}

?>