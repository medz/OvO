<?php
/**
 * 导航Ds服务
 *
 * @author $Author: gao.wanggao $ 
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwNav.php 19309 2012-10-12 09:03:36Z long.shi $ 
 * @package nav
 */

class PwNav {

	/**
	 * 根据ID获得一条导航信息
	 *
	 * @param int $navid 导航ID
	 * @return array
	 */
	public function getNav($navId) {
		return  $this->_getNavDao()->getNav($navId);
	}
	
	/**
	 * 获取多条导航信息
	 *
	 * @param array $navids
	 * @return Ambigous <multitype:, multitype:multitype: Ambigous <multitype:, multitype:unknown , mixed> >
	 */
	public function fetchNav($navids) {
		return $this->_getNavDao()->fetchNav($navids);
	}

	/**
	 * 根据导航类型获得列表
	 *
	 * @param string $type 导航类型
	 * @param int  	 $isshow 0不显示,1显示,2全部
	 * @return array
	 */
	public function getNavByType($type = 'main', $isShow = 1) {
		$data= $this->_getNavDao()->getNavByType($type, $isShow);
		return $this->_arrayValueSort($data);
	}
	
	public function getNavBySign($type = 'main', $sign = '') {
		if (!$sign) return array();
		return $this->_getNavDao()->getNavBySign($type, $sign);
	}
	
	/**
	 * 根据某导航类型顶级列表
	 *
	 * @param string $type 导航类型
	 * @param int  	$isshow 0不显示,1显示,2全部
	 * @return array
	 */
	public function getRootNav($type = 'main', $isShow = 2) {
		return $this->_getNavDao()->getRootNav($type, $isShow);
	}
	
	/**
	 * 根据某导航的子导航列表
	 *
	 * @param int $navId 导航类型
	 * @param int  $isshow 0不显示,1显示,2全部
	 * @return array
	 */
	public function getChildNav($navId, $isShow=2) {
		return $this->_getNavDao()->getChildNav($navId, $isShow);
	}
	
	/**
	 * 增加一条导航信息
	 *
	 * @param object $dm 导航模型数据
	 * @return array
	 */
	public function addNav(PwNavDm $dm) {
		$resource=$dm->beforeAdd();
		if ($resource instanceof PwError) return $resource;
		$data = $dm->getData();
		$navId = $this->_getNavDao()->addNav($data);
		$this->_updateNav($navId,$data);
		return $navId;
	}
	
	/**
	 * 修改一条导航信息
	 *
	 * @param object $dm导航数据模型
	 * @return array
	 */
	public function updateNav(PwNavDm $dm) {
		$resource = $dm->beforeUpdate();
		if ($resource instanceof PwError) return $resource;
		$data = $dm->getData();
		$data['rootid'] = $data['parentid'] ? $data['parentid'] : $dm->navid;
		return $this->_getNavDao()->updateNav($dm->navid, $data);
	}
	
	/**
	 * 修改多条导航信息
	 *
	 * @param array $dms 导航数据
	 * @return array
	 */
	public function updateNavs($dms) {
		$number = 0;
		foreach ($dms AS $dm) {
			if (!$dm instanceof PwNavDm) continue;
			$msg =  $this->_getNavDao()->updateNav($dm->navid, $dm->getData());
			if($msg === false) $number++;
		}
		return $number;
	}
	
	/**
	 * 添加多条导航信息
	 *
	 * TODO 必需跟据数组入栈顺序循环
	 * @param array $dms 导航数据
	 * @return array
	 */
	public function addNavs($dms) {
		$parenid = 0;
		foreach ($dms AS $k=>$dm) {
			if (!$dm instanceof PwNavDm) continue;
			$data = $dm->getData();
			if (strpos($data['parentid'],'temp') !== false) {
				$data['parentid'] = $parenid;
			}
			$navId = $this->_getNavDao()->addNav($data);
			if ($data['tempid']) {
				$parenid = $navId;
			} 
			$this->_updateNav($navId, $data);
			
		}
		return true;
	}
	
	/**
	 * 删除一条导航信息
	 *
	 * @param int $navId 导航ID
	 * @return array
	 */
	public function delNav($navId) {
		$child = $this->_getNavDao()->getChildNav($navId, 2);
		if (!empty($child)) {
			return new PwError('ADMIN:nav.del.fail.have.child');
		}
		return $this->_getNavDao()->delNav($navId);
	}
	
	/**
	 * 对导航进行分组和排序更新
	 *
	 * @param int $navId 导航ID
	 * @param array $data 导航数据
	 * @return array
	 */
	private function _updateNav($navId,$data){
		if ($data['parentid']) {
			$rootid = $data['parentid'];
			$orderid = $this->_getNavDao()->getNavMaxOrder($data['type'], $rootid);
		} else {
			$rootid = $navId;
			$orderid = $this->_getNavDao()->getNavMaxOrder($data['type']);
		}
		
		$this->_getNavDao()->updateNav($navId, array('rootid'=>$rootid));
		
		if ($data['orderid'] < 1) {
			$orderid = intval($orderid) + 1;
			$this->_getNavDao()->updateNav($navId, array('orderid'=>$orderid));
		}
	}
	/**
	 * @return PwNavDao
	 */
	private function _getNavDao() {
		return Wekit::loadDao('nav.dao.PwNavDao');
	}
	
	/**
	 * 对导航信息进行分组和排序
	 *
	 * @param array $array 导航数据
	 * @return array
	 */
	private function _arrayValueSort($array) {
		if (!is_array($array)) return array();
		$_array = array();
		$_key = 0;
		foreach ($array as $key=> $value) {
			if ($value['parentid'] == '0'){
				$_key = $key;
				$_array[$_key] = $value['orderid'];
				$array[$_key]['child'] = array();
			}else{
				$array[$_key]['child'][] = $array[$key];
			}
		}
		asort($_array,SORT_NUMERIC);
		foreach ($_array as $_key=> $_value) {
				$_array[$_key] = $array[$_key];
		}
		return $_array;
	}
	
}