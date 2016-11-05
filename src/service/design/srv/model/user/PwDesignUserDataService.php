<?php
Wind::import('SRV:design.srv.model.PwDesignModelBase');
Wind::import('SRV:education.srv.helper.PwEducationHelper');
/**
 * 用户设置
 * <note>
 *  decorateAddProperty 为插入表单值修饰
 *  decorateEditProperty 为修改表单值修饰
 *  getData 获取数据
 * </note>
 *
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwDesignUserDataService.php 24726 2013-02-18 06:15:04Z gao.wanggao $
 * @package src.service.design.srv.model.user
 */
class PwDesignUserDataService extends PwDesignModelBase {

	/**
	 * (non-PHPdoc)
	 * @see src/service/design/srv/model/PwDesignModelBase::decorateAddProperty()
	 */
	public function decorateAddProperty($model) {
		$data = array();
		$data['gidOptions'] = $this->_buildGids(-1);
		return $data;
	}

	/**
	 * (non-PHPdoc)
	 * @see src/service/design/srv/model/PwDesignModelBase::decorateEditProperty()
	 */
	public function decorateEditProperty($moduleBo) {
		$property = $moduleBo->getProperty();
		$data = array();
		!isset($property['gid']) && $property['gid'] = -1;
		$data['gidOptions'] = $this->_buildGids($property['gid']);
		return $data;
	}

	/**
	 * (non-PHPdoc)
	 * @see src/service/design/srv/model/PwDesignModelBase::decorateSaveProperty()
	 */
	public function decorateSaveProperty($property, $moduleid) {
		//用户名
		if (trim($property['usernames'])) {
			$usernames = array_unique(explode(' ', trim($property['usernames'])));
			$userList = $this->_getDs()->fetchUserByName($usernames);
			$clear = $uids = array();
			foreach ($userList as $_k => $_i) {
				$clear[] = $_i['username'];
				$uids[] = $_i['uid'];
			}
			$property['usernames'] = implode(' ', $clear);
			$property['uids'] = $uids;
		}

		//居住地/家乡
		$property['hometown'] = $this->_buildArea($property['hometown']['id']);
		$property['location'] = $this->_buildArea($property['location']['id']);
		return $property;
	}

	/* (non-PHPdoc)
	 * @see PwDesignModelBase::getData()
	 */
	protected function getData($field, $order, $limit, $offset) {
		Wind::import('SRV:user.vo.PwUserSo');
		$so = new PwUserSo();
		$field['uids'] && $so->setUid($field['uids']);
		$field['gid'] != '-1' && $so->setGid($field['gid']);
		if ($field['gender']) {
			if (in_array(0, $field['gender']) && !in_array(1, $field['gender'])) {
				$so->setGender(0);
			} elseif (!in_array(0, $field['gender']) && in_array(1, $field['gender'])) {
				$so->setGender(1);
			}
		}
		$field['hometown']['id'] && $so->setHometown($field['hometown']['id']);
		$field['location']['id'] && $so->setLocation($field['location']['id']);
		
		$orderby = $field['orderby'];
		switch($orderby) {
			case '2': //按主题数倒序,
				$so->orderbyPostnum(false);
				break;
			case '3' : //按发帖时间倒序,
				$so->orderbyLastpost(false);
				break;
			case '4' : //按被喜欢数倒序,
				break;
			case '5': //按注册时间倒序,
				$so->orderbyRegdate(false);
				break;
			case '6' :// 按访问时间倒序,
				$so->orderbyLastvisit(false);
				break;
			case '1'://系统推荐排序
			default:
				break;
		}
		
		$list = Wekit::load('user.PwUserSearch')->searchUserAllData($so, $limit, $offset);
		return $this->_buildSignKey($list);
	}

	/**
	 * 用于推送时的指定数据获取
	 * @see src/service/design/srv/model/PwDesignModelBase::_fetchData()
	 */
	protected function fetchData($ids) {
		Wind::import('SRV:user.vo.PwUserSo');
		$so = new PwUserSo();
		$so->setUid($ids);
		$list = Wekit::load('user.PwUserSearch')->searchUserAllData($so);
		return $this->_buildSignKey($list);
	}

	/**
	 * 构建模板用的标签
	 *
	 * @param array $list
	 * @return string
	 */
	private function _buildSignKey($list) {
		$clear = array();
		/* @var $userGroupSrv PwUserGroupsService */
		$userGroupSrv = Wekit::load('usergroup.srv.PwUserGroupsService');
		/* @var $workDs PwWork */
		$workDs = Wekit::load('work.PwWork');
		/* @var $educateDs PwEducation */
		$educateDs = Wekit::load('education.PwEducation');
		$location = $hometown = $schoolids = $_areaid = array();
		foreach ($list as $_uid => $_item) {
			$_one = array();
			$_one['uid'] = $_item['uid'];
			$_one['username'] = $_item['username'];
			$_one['url'] = WindUrlHelper::createUrl('space/index/run', array('uid' => $_item['uid']), '', 'pw');
			$_one['smallavatar'] = Pw::getAvatar($_uid, 'small');
			$_one['middleavatar'] = Pw::getAvatar($_uid, 'middle');
			$_one['bigavatar'] = Pw::getAvatar($_uid, 'big');
			$_one['regdate'] = $this->_formatTime($_item['regdate']);
			$_one['lastvisit'] = $this->_formatTime($_item['lastvisit']);
			$_one['posts'] = $_item['postnum'];
			$_one['topics'] = $_item['postnum'];//主题数
			$_one['digests'] =$_item['digest'];
			$_one['compositePoint'] = $userGroupSrv->getCredit($_item);
			$_one['realname'] = $_item['realname'];
			$_one['sex'] = !in_array($_item['gender'], array(0, 1)) ? '未知' : ($_item['gender'] == 0 ? '男' : '女');
			$_one['birthYear'] = $_item['byear'];
			$_one['birthMonth'] = $_item['bmonth'];
			$_one['birthDay'] = $_item['bday'];
			$_one['homepage'] = $_item['homepage'];
			$_one['profile'] = $_item['profile'];
			$_one['alipay'] = $_item['alipay'];
			$_one['mobile'] = $_item['mobile'];
			$_one['telphone'] = $_item['telphone'];
			$_one['address'] = $_item['address'];
			$_one['zipcode'] = $_item['zipcode'];
			$_one['email'] = $_item['email'];
			$_one['aliww'] = $_item['aliww'];
			$_one['qq'] = $_item['qq'];
			$_one['msn'] = $_item['msn'];
			
			$_one['worklist'] = $this->_buildWork($workDs, $_uid);
			list($_schoolids, $_one['educationlist']) = $this->_buildEducation($educateDs, $_uid);
			$schoolids += $_schoolids;
			$_one['home_province'] = $_one['home_city'] = $_one['home_area'] = '';
			$_one['locate_province'] = $_one['locate_city'] = $_one['locate_area'] = '';
			$location[$_uid] = $_item['location'];
			$hometown[$_uid] = $_item['hometown'];
			$_areaid[] = $_item['location'];
			$_areaid[] = $_item['hometown'];
			$clear[$_uid] = $_one;
		}
		
		//家庭和居住地
		$areaSrv = WindidApi::api('area');
		$areaList = $areaSrv->fetchAreaInfo(array_unique($_areaid));
		
		//学校列表
		$schoolSrv = WindidApi::api('school');
		$schoolList = $schoolSrv->fetchSchool($schoolids);

		foreach ($clear as $_uid => $_item) {
			if ($hometown[$_uid] && isset($areaList[$hometown[$_uid]])) {
				$_temp = explode(' ', $areaList[$hometown[$_uid]], 3);
				$clear[$_uid]['home_province'] = $_temp[0];
				isset($_temp[1]) && $clear[$_uid]['home_city'] =  $_temp[1];
				isset($_temp[2]) && $clear[$_uid]['home_area'] =  $_temp[2];
			}
			if ($location[$_uid] && isset($areaList[$location[$_uid]])) {
				$_temp = explode(' ', $areaList[$location[$_uid]], 3);
				$clear[$_uid]['locate_province'] = $_temp[0];
				isset($_temp[1]) && $clear[$_uid]['locate_city'] = $_temp[1];
				isset($_temp[2]) && $clear[$_uid]['locate_area'] = $_temp[2];
			}
			if ($_item['educationlist']) {
				$_temp = array();
				foreach ($_item['educationlist'] as $_i) {
					if (isset($schoolList[$_i['schoolid']])) {
						$_i['school'] = $schoolList[$_i['schoolid']]['name'];
						unset($_i['schoolid']);
						$_temp[] = $_i;
					}
				}
				$clear[$_uid]['educationlist'] = $_temp;
			}
		}
		return $clear;
	}
	
	/**
	 * 用户工作经历
	 *
	 * @param PwWork $ds
	 * @param int $uid
	 * @return array
	 */
	private function _buildWork(PwWork $ds, $uid) {
		$list = $ds->getByUid($uid, 10, 0);
		$workList = array();
		foreach ($list as $id => $_item) {
			$_one = array();
			$_one['company'] = $_item['company'];
			$_one['start'] = $_item['starty'] . '-' . $_item['startm'];
			$_one['end'] = $_item['endy'] . '-' . $_item['endm'];
			$workList[] = $_one;
		}
		return $workList;
	}
	
	/**
	 * 用户教育经历
	 *
	 * @param PwEducation $ds
	 * @param int $uid
	 * @return array
	 */
	private function _buildEducation(PwEducation $ds, $uid) {
		$list = $ds->getByUid($uid, 10, 0);
		$educateList = $schoolids = array();
		$degree = PwEducationHelper::getDegrees();
		foreach ($list as $id => $_item) {
			$_one = array();
			$schoolids[] = $_item['schoolid'];
			$_one['school'] = '';
			$_one['schoolid'] = $_item['schoolid'];
			$_one['degree'] = $degree[$_item['degree']];
			$_one['start'] = $_item['start_time'];
			$educateList[] = $_one;
		}
		return array($schoolids, $educateList);
	}
	
	/**
	 * 构造用户组
	 *
	 * @param string $gid
	 * @return string
	 */
	private function _buildGids($gid) {
		$gidOptions = '<option value="-1" ' . Pw::isSelected($gid == -1) . '>全部用户组</option>';
		$gidOptions .= '<option value="0" ' . Pw::isSelected($gid == 0) . '>会员组</option>';
		/* @var $groupDs PwUserGroups */
		$groupDs = Wekit::load('usergroup.PwUserGroups');
		$groups = $groupDs->getClassifiedGroups();
		if (!$groups) return $gidOptions;
		$types = $groupDs->getTypeNames();
		unset($types['member']);
		foreach($types as $_k => $_v) {
			if (!isset($groups[$_k])) continue;
			$option = '<optgroup label="' . $_v . '">';
			foreach ($groups[$_k] as $_item) {
				$option .= '<option value="' . $_item['gid'] . '" ' . Pw::isSelected($gid == $_item['gid']) . '>' . $_item['name'] . '</option>';
			}
			$gidOptions .= $option;
		}
		return $gidOptions;
	}
	
	/**
	 * 构建推送的地区库---获取地区库的省/市/区
	 *  
	 * @param int $areaid
	 * @return array
	 */
	private function _buildArea($areaid) {
		$areaSrv = WindidApi::api('area');
		$_rout = $areaSrv->getAreaRout($areaid);
		$_return = array('id' => '', 'rout' => array(array('', ''), array('', ''), array('', '')));
		if (!$_rout) return $_return;
		foreach ($_rout as $_k => $_r) {
			$_return['rout'][$_k] = array($_r['areaid'], $_r['name']);
		}
		$_return['id'] = $areaid;
		return $_return;
	}

	/**
	 * 用户DS
	 *
	 * @return PwUser
	 */
	private function _getDs() {
		return Wekit::load('user.PwUser');
	}
}
?>
