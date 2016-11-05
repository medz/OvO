<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: yanchixia $>
 * @author $Author: yanchixia $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwNavBo.php 24273 2013-01-24 03:24:52Z yanchixia $ 
 * @package 
 */
class PwNavBo {
	
	protected $default = array('m' => 'default', 'c' => 'index', 'a' => 'run');
	protected $forum = array();
	protected $router = array();
	
	public function setForum($cateid = 0, $fid = 0, $tid = 0) {
		$this->forum['fid'] = $fid;
		$this->forum['cateid'] = $cateid;
		$this->forum['tid'] = $tid;
	}
	

	public function isForum() {
		if ($this->router['m'] == 'bbs') return true;
		return false;
	}
	
	/**
	 * 需要定位时调用
	 * Enter description here ...
	 */
	public function setRouter() {
		$router = Wind::getComponent('router');
		$this->router['m'] = $router->getModule(); 
    	$this->router['c'] = $router->getController(); 
    	$this->router['a'] = $router->getAction();
	}
	
	/**
	 * 根据类型从数据库获取导航
	 * Enter description here ...
	 * @param string $type  @see navtype/system.php
	 * @param bool $current 是否需要当前定位
	 */
	public function getNavFromData($type, $current = false) {
		if (!$type) return array();
		$childRating = $rootRating = array();
		$list = $this->_getNavDs()->getNavByType($type);
		if (!is_array($list)) return array();
		if (!$current) return $list;
		$rating = $this->routRating();
		foreach ($list AS $key => $value) {
			if (!$value['name']) continue;
			$list[$key]['name'] = $this->bindHtml($value);
			if ($_k = array_search($value['sign'], $rating)) {
				//根导航定位
				$rootRating[$key] = $_k;
			}

			foreach ((array)$value['child'] AS $ckey => $cvalue) {
				if (!$cvalue['name']) continue;
				$list[$key]['child'][$ckey]['name'] = $this->bindHtml($cvalue);
				if ($_k = array_search($cvalue['sign'], $rating)) {
					//子导航定位
					$childRating[$key][$ckey] = $_k;
				}

			}
		}

		asort($rootRating);
		foreach ($childRating AS $_k=>$_c) {
			asort($_c);
			$tmp = array_keys($_c);
			$childCurrentId = array_shift($tmp);
			if ($childCurrentId) $list[$_k]['child'][$childCurrentId]['current'] = true;
		}
		$tmp = array_keys($rootRating);
		$rootCurrentId = array_shift($tmp);
		$list[$rootCurrentId]['current'] = true;
		return $list;
	}
	
	/**
	 * 根据类型从配置缓存中获取导航
	 * Enter description here ...
	 * @param string $type  @see navtype/system.php
	 * @param bool $current 是否需要当前定位
	 */
	public function getNavFromConfig($type, $current = false) {
		if (!$type) return array();
		$childRating = $rootRating = array();
		$list = Wekit::C('nav', $type);
		if (!is_array($list)) return array();
		if (!$current) return $list;
		$rating = $this->routRating();
		foreach ((array)$list AS $key => $value) {
			if (!$value['name']) continue;
			if ($_k = array_search($value['sign'], $rating)) {
				//根导航定位
				$rootRating[$key] = $_k;
			}
			foreach ((array)$value['child'] AS $ckey => $cvalue) {
				if (!$cvalue['name']) continue;
				if ($_k = array_search($cvalue['sign'], $rating)) {
					//子导航定位
					$childRating[$key][$ckey] = $_k;
				}
			}
		}
		if ($childRating) {
			foreach ($childRating AS $_k=>$_c) {
				asort($_c);
				$tmp = array_keys($_c);
				$childCurrentId = array_shift($tmp);
				$list[$_k]['child'][$childCurrentId]['current'] = true;
				$list[$_k]['current'] = true;
			}
			
		} else {
			asort($rootRating);
			$tmp = array_keys($rootRating);
			$rootCurrentId = array_shift($tmp);
			$list[$rootCurrentId]['current'] = true;
		}
		return $list;
	}

	
	/**
	 * 可能的路由等级
	 * key越小，说明当前定位优先级越高
	 * Enter description here ...
	 */
	protected function routRating() {
		$rating = array();
		$rating[] = '';
		$m = $this->router['m'];
		$c = $this->router['c'];
		$a = $this->router['a'];
		$rating[] = $m .'|'. $c .'|'. $a.'|';
    	if ($m == 'bbs') { //帖子兼容
    		$rating[] = $m .'|'. $c .'|'. $a .'|'.$this->forum['tid'].'|';
    		$rating[] = $m .'|thread|'. $this->default['a'] .'|'.$this->forum['fid'].'|';
    		if ($c == 'cate') {
    			$rating[] = $m .'|cate|'. $this->default['a'] .'|'.$this->forum['fid'].'|';
    		} else {
    			$rating[] = $m .'|cate|'. $this->default['a'] .'|'.$this->forum['cateid'].'|';
    		}
			$rating[] = $m .'|'. $this->default['c'] .'|'. $this->default['a'].'|';
    		$rating[] = $m .'|forumlist|'. $this->default['a'] .'|';
    		$rating[] = $m .'|forum|'. $this->default['a'] .'|';
    	}
    	if ($m == 'special') { //门户兼容
    		$id = Wind::getApp()->getRequest()->getGet('id');
    		$rating[] = $m .'|'.$c.'|'. $a .'|'.$id.'|';
    	}
    	
    	if ($m == 'app') { //应用中心兼容
    		$id = Wind::getApp()->getRequest()->getGet('app');
    		$rating[] = $m .'|'.$c.'|'. $a .'|'.$id.'|';
    		$rating[] = 'appcenter| '.$c.' |'. $a .'|';
    	}
    	
    	if ($m == 'like') {
    		$rating[] = 'like|like|run|';
    	}
    	
    	$rating[] = $m .'|'. $c .'|'. $this->default['a'].'|';
    	$rating[] = $m .'|'. $this->default['c'] .'|'. $this->default['a'].'|';
    	$home = Wekit::C('site', 'homeRouter');
    	$rating[] = $home['m'] .'|'. $home['c'] .'|'. $home['a'].'|';
    	$rating[] = $this->default['m'] .'|'. $this->default['c'] .'|'. $this->default['a'].'|';
    	return $rating;
	}

	public function bindHtml($data = '') {
		list($color, $bold, $italic, $underline) = explode('|', $data['style']);
		$html = '<a href="'.$this->_checkUrl($data['link']).'"';
		if ($data['alt']) {
			$html .= ' title="'.$data['alt'].'"';
		}
		if ($data['target']) {
			$html .= ' target="_blank"';
		}
		if ($color || $bold || $italic || $underline){
			$html .= ' style="';
			!empty($color) && $html .= 'color:'.$color.';';
			!empty($bold) && $html .= 'font-weight:bold;';
			!empty($italic) && $html .= 'font-style:italic;';
			!empty($underline) && $html .= 'text-decoration:underline;';
			$html .= '"';
		}
		$html .= '>';
		if ($data['type'] == 'my') {
			if (!$data['image']) {
				$icon = $data['sign'] ? 'icon_'.$data['sign'] : 'icon_default';
				$html .= '<em class="'.$icon.'"></em>';
			} else {
				//$icon = $data['sign'] ? $data['sign'] : 'default';
				//$icon = WindUrlHelper::checkUrl(PUBLIC_RES . '/images/nav/', PUBLIC_URL) . '/' .  $icon . '.png';
				$html .= '<em style="background:url('.$data['image'].') 0 bottom no-repeat;margin-top:7px;"></em>';
			}
		}
		$html .= $data['name'].'</a>';
		return $html;
	}
	
	protected function _checkUrl($url) {
		$router = Wind::getComponent('router');
		if ($route = $router->getRoute('pw')) {
			$url = $route->checkUrl($url);
		}
		return $url;
	}
	
	private function _getNavDs() {
		return Wekit::load('SRV:nav.PwNav');
	}
}