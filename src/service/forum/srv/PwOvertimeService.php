<?php
defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('SRV:forum.dm.PwTopicDm');

class PwOvertimeService {

	public function updateOvertime($tid) {
		$overtimes = $this->_getOvertimeDs()->getOvertimeByTid($tid);
		$deltop = 0;
		$dm = new PwTopicDm($tid);
		if ($overtimes) {
			$timestamp = Pw::getTime();
			$newOvertime = 0;
			$ids = array();
			foreach ($overtimes as $v) {
				if ($v['overtime'] > $timestamp) {
					(!$newOvertime || $newOvertime > $v['overtime']) && $newOvertime = $v['overtime'];
				} else {
					switch ($v['m_type']) {
						case 'topped':
							$dm->setTopped(0);
							$deltop = 1;
							break;
						case 'highlight':
							$dm->setHighlight('');
							break;
					}
					$ids[] = $v['id'];
				}
			}
			$ids && $this->_getOvertimeDs()->batchDelete($ids);
			$dm->setOvertime($newOvertime);
		} else {
			$dm->setOvertime(0);
		}
		$this->_getThreadDs()->updateThread($dm);

		if ($deltop) {
			Wekit::load('forum.PwSpecialSort')->deleteSpecialSortByTid($tid);
		}
	}
	
	/**
	 * Enter description here ...
	 * 
	 * @return PwOvertime
	 */
	private function _getOvertimeDs() {
		return Wekit::load('forum.PwOvertime');
	}
	
	/**
	 * Enter description here ...
	 * 
	 * @return PwThread
	 */
	protected function _getThreadDs() {
		return Wekit::load('forum.PwThread');
	}
}