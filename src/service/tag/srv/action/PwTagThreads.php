<?php

Wind::import('SRV:tag.srv.action.PwTagAction');

class PwTagThreads extends PwTagAction{

	/**
	 * (non-PHPdoc)
	 * @see PwTagAction::getContents()
	 */
	public function getContents($ids){
		$threads = $this->_getThreadDs()->fetchThread($ids,PwThread::FETCH_ALL);
		$array = array();
		if ($threads) {
			$fids = array();
			foreach ($threads as $v) {
				$fids[] = $v['fid'];
			}
			$user = Wekit::getLoginUser();
			$forums = $this->_getForumDs()->fetchForum($fids);
			$forbidFids = $this->getForbidVisitForum($user, $forums);
			$lang = Wind::getComponent('i18n');
			foreach ($threads as $k => $v) {
				if ($v['disabled'] > 0) {
					$content = $lang->getMessage('BBS:forum.thread.disabled');
					$v['subject'] = $content;
					$v['content'] = $content;
				} elseif (in_array($v['fid'], $forbidFids)) {
					$content = $lang->getMessage('BBS:forum.thread.right.error');
					$v['subject'] = $content;
					$v['content'] = $content;
				}
				$v['forum_name'] = $forums[$v['fid']]['name'];
				$v['created_time_auto'] = pw::time2str($v['created_time'],'auto');
				$v['type_id'] = $forums[$v['fid']]['name'];
				$array[$k] = $v;
			}
		}
		
		return $array;
	}
	
	/**
	 * 获取用户所有禁止访问的版块列表
	 *
	 * @param PwUserBo $user
	 * @return array
	 */
	protected function getForbidVisitForum(PwUserBo $user, $forums) {
		$fids = array();
		foreach ($forums as $key => $value) {
			if (($value['allow_visit'] && !$user->inGroup(explode(',', $value['allow_visit']))) || ($value['allow_read'] && !$user->inGroup(explode(',', $value['allow_read'])))) {
				$fids[] = $value['fid'];
			}
		}
		return $fids;
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @return PwThread
	 */
	private function _getThreadDs(){
		return Wekit::load('forum.PwThread');
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @return PwForum
	 */
	private function _getForumDs(){
		return Wekit::load('forum.PwForum');
	}
}