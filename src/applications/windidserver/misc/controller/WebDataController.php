<?php

/**
 * 地区访问
 *
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: WebDataController.php 24710 2013-02-16 07:45:42Z jieyin $ 
 * @package src.applications.bbs.controller
 */
class WebDataController extends PwBaseController {
	
	/**
	 * 地区库获取
	 */
	public function areaAction() {
		/* @var $areaService WindidAreaService */
		$areaService = Wekit::load('WSRV:area.srv.WindidAreaService');
		$list = $areaService->getAreaTree();
		exit($list ? Pw::jsonEncode($list) : '');
	}
	
	/**
	 * 学校获取（typeid = 1:小学，2：中学，3：大学）
	 */
	public function schoolAction() {
		list($type, $areaid, $name, $first) = $this->getInput(array('typeid', 'areaid', 'name', 'first'));
		!$type && $type = 3;
		Wind::import('WSRV:school.vo.WindidSchoolSo');
		$schoolSo = new WindidSchoolSo();
		$schoolSo->setName($name)
			->setTypeid($type)
			->setFirstChar($first)
			->setAreaid($areaid);
		/* @var $schoolService WindidSchoolService */
		$schoolService = Wekit::load('WSRV:school.srv.WindidSchoolService');
		$list = $schoolService->searchSchool($schoolSo, 1000);
		exit($list ? Pw::jsonEncode($list) : '');
	}
}