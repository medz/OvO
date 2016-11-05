<?php

/**
 * 教育经历帮助类
 *
 * @author xiaoxia.xu <x_824@sina.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: codetemplates(windframework_docs_zend_8.0).xml 2781 2011-09-22 03:59:17Z yishuo $
 * @package src.service.user.hooks.experience_education.srv
 */
class PwEducationHelper {
	
	/**
	 * 返回教育时间
	 * 倒序返回-倒退100年
	 *
	 * @return array
	 */
	public static function getEducationYear() {
		$tyear = Pw::time2str(Pw::getTime(), 'Y');
		return range($tyear, $tyear - 100, -1);
	}
	
	/**
	 * 检查教育时间是否非法
	 *
	 * @param int $year
	 * @return int
	 */
	public static function checkEducationYear($year) {
		$endYear = Pw::time2str(Pw::getTime(), 'Y');
		if ($year > $endYear) {
			$year = $endYear;
		} elseif ($year < ($endYear - 100)) {
			$year = $endYear - 100;
		}
		return $year;
	}
	
	/**
	 * 获得学历
	 * 
	 * @param string $select 需要返回的数据key
	 * @return array
	 */
	public static function getDegrees($selected = '') {
		$degrees = array(
			'8' => '博士后',
			'7'	=> '博士',
			'6' => '硕士',
			'5' => '大学本科',
			'4' => '大学专科',
			'3'	=> '高中',
			'2'	=> '初中',
			'1'	=> '小学',
		);
		return $selected ? $degrees[$selected] : $degrees;
	}
	
	/**
	 * 检查是否符合
	 *
	 * @param string $degree
	 * @return boolean
	 */
	public static function checkDegree($degree) {
		return array_key_exists($degree, self::getDegrees());
	}
}