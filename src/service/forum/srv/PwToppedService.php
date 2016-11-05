<?php
defined('WEKIT_VERSION') || exit('Forbidden');

class PwToppedService {

	public function getForumListForHeadTopic($groupid,$currentFid=0,$currentCategory = false){
		$sub1 = $sub2 = $forumdb = array();
		$forums = $this->_getForumDs()->getForumList();
		if ($currentCategory){
			$category = $this->_getCategoryByFid($currentFid,$forums);
		}
		foreach ($forums as $v) {
			if ($v['isshow'] || ( !$v['isshow'] && strpos($v['allowvisit'],','.$groupid.',') !== false )) {
				$v['fid'] == $currentFid && $currentForum = $v;
				if ($v['type'] == 'category') {
					if ($currentCategory && $category != $v['fid']) continue;
					$catedb[] = $v;
				} elseif ($v['type'] == 'forum') {
					$forumdb[$v['parentid']] || $forumdb[$v['parentid']] = array();
					$forumdb[$v['parentid']][] = $v;
				} elseif ($v['type'] == 'sub') {
					$sub1[$v['parentid']] || $sub1[$v['parentid']] = array();
					$sub1[$v['parentid']][] = $v;
				} else {
					$sub2[$v['parentid']] || $sub2[$v['parentid']] = array();
					$sub2[$v['parentid']][] = $v;
				}
			}
		}
		$top_3 = $top_2 = $top_1 = $catedbs = array();
		foreach ((array)$catedb as $k1 => $v1) {
			$catedbs[$v1['fid']] = array();
			foreach ((array)$forumdb[$v1['fid']] as $k2 => $v2) {
				$catedbs[$v1['fid']][] = $v2['fid'];
				foreach ((array)$sub1[$v2['fid']] as $k3 => $v3) {
					$catedbs[$v1['fid']][] = $v3['fid'];
					foreach ((array)$sub2[$v3['fid']] as $k4 => $v4) {
						$catedbs[$v1['fid']][] = $v4['fid'];
					}
				}
			}
		}
		foreach ((array)$catedb as $k1 => $v1) {
			$v1['name'] = WindSecurity::escapeHTML(strip_tags($v1['name']));
			$top_3[$v1['fid']] = "&gt;&gt;".$v1['name'];
			if (in_array($currentForum['fid'],$catedbs[$v1['fid']])) {
				$top_2[$v1['fid']] = "&gt;&gt;".$v1['name'];
			}
			foreach ((array)$forumdb[$v1['fid']] as $k2 => $v2) {
				$v2['name'] = WindSecurity::escapeHTML(strip_tags($v2['name']));
				if ($v2['fid'] == $currentForum['fid']) {
					$top_1[$v2['fid']] = "&nbsp;|-".$v2['name'];
				}
				if (in_array($currentForum['fid'],$catedbs[$v1['fid']])) {
					$top_2[$v2['fid']] = "&nbsp;|-".$v2['name'];
				}
				$top_3[$v2['fid']] = "&nbsp;|-".$v2['name'];
				if (!is_array($sub1[$v2['fid']])) {
					continue;
				}
				foreach ((array)$sub1[$v2['fid']] as $k3 => $v3) {
					$_subs = array();
					$v3['name'] = WindSecurity::escapeHTML(strip_tags($v3['name']));
					if ($v3['fid'] == $currentForum['fid']) {
						$top_1[$v3['fid']] = "&nbsp;|-".$v3['name'];
					}
					if ($v3['parentid'] == $currentForum['fid']) {
						$_subs[] = $v3['fid'];
						$top_1[$v3['fid']] = "&nbsp;&nbsp;&nbsp;|-".$v3['name'];
					}
					$v1['fid'] == $currentForum['parentid'] && $top_2[$v3['fid']] = "&nbsp;&nbsp;&nbsp;|-".$v3['name'];
					if (in_array($currentForum['fid'],$catedbs[$v1['fid']])) {
						$top_2[$v3['fid']] = "&nbsp;&nbsp;&nbsp;|-".$v3['name'];
					}
					$top_3[$v3['fid']] = "&nbsp;&nbsp;&nbsp;|-".$v3['name'];
					if (!is_array($sub2[$v3['fid']])) {
						continue;
					}
					foreach ((array)$sub2[$v3['fid']] as $k4 => $v4) {
						$v4['name'] = WindSecurity::escapeHTML(strip_tags($v4['name']));
						if ($v4['fid'] == $currentForum['fid']) {
							$top_1[$v4['fid']] = "&nbsp;|-".$v4['name'];
						}
						if (in_array($v4['parentid'],$_subs)) {
							$top_1[$v4['fid']] =  "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|-".$v4['name'];
						}
						if (in_array($currentForum['fid'],$catedbs[$v1['fid']])) {
							$top_2[$v4['fid']] = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|-".$v4['name'];
						}
						$top_3[$v4['fid']] = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|-".$v4['name'];
					}
				}
			}
		}
		return array($catedbs,$top_1,$top_2,$top_3);
	}
	
	private function _getCategoryByFid($fid,$forums){
		if (!$forums) return 0;
		$current = $forums[$fid];
		switch ($current['type']) {
			case 'forum':
				return $forums[$current['parentid']]['fid'];
			break;
			case 'sub':
				$current = $forums[$current['parentid']];
				return $forums[$current['parentid']]['fid'];
			break;
			case 'sub2':
				$current = $forums[$current['parentid']];
				$current = $forums[$current['parentid']];
				return $forums[$current['parentid']]['fid'];
			break;
		}
		return 0;
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