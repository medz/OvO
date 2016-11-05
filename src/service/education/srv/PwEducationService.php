<?php
Wind::import('SRV:education.srv.helper.PwEducationHelper');
/**
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com> 2010-11-2
 * @link http://www.phpwind.com
 * @copyright Copyright ©2003-2010 phpwind.com
 * @license
 */

class PwEducationService {
	
	/**
	 * 根据用户ID获得该用户的教育经历
	 *
	 * @param int $uid
	 * @param int $num
	 * @param boolean $buildArea 是否需要查询获取学校地区的层级
	 * @return array
	 */
	public function getEducationByUid($uid, $num = 10, $buildArea = false) {
		$educations = $this->_getDs()->getByUid($uid, $num);
		if (!$educations) return array();
		$schoolids = array();
		foreach ($educations as $key => $education) {
			$educations[$key]['degreeid'] = $education['degree'];
			$educations[$key]['degree'] = PwEducationHelper::getDegrees($education['degree']);
			$schoolids[] = $education['schoolid'];
		}
		$schools = $this->_getSchoolDs()->fetchSchool($schoolids);
		$areaids = array();
		foreach ($educations as $key => $education) {
			$educations[$key]['school'] = isset($schools[$education['schoolid']]) ? $schools[$education['schoolid']]['name'] : '';
			$buildArea && $educations[$key]['areaid'] = $schools[$education['schoolid']]['areaid'];
			$areaids[] = $schools[$education['schoolid']]['areaid'];
		}
		if ($buildArea) {
			$areaSrv = WindidApi::api('area');
			$areas = $areaSrv->fetchAreaRout($areaids);
			foreach ($educations as $key => $education) {
				$educations[$key]['areaid'] = $areas[$educations[$key]['areaid']];
			}
		}
		return $educations;
	}
	
	/**
	 * 获得学校Ds
	 *
	 * @return WindidSchool
	 */
	private function _getSchoolDs() {
		return WindidApi::api('school');
	}
	
	/**
	 * 教育经历DS
	 * 
	 * @return PwEducation
	 */
	private function _getDs() {
		return Wekit::load('education.PwEducation');
	}
}