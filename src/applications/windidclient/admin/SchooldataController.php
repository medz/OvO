<?php
Wind::import('ADMIN:library.AdminBaseController');
Wind::import('WINDID:service.school.dm.WindidSchoolDm');
Wind::import('WINDID:service.school.vo.WindidSchoolSo');


/**
 * 全局-资料库-学校库
 *
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: SchooldataController.php 24834 2013-02-22 06:43:43Z jieyin $
 * @package applications.config.admin
 */
class SchooldataController extends AdminBaseController {
	
	private $type = array(1 => '小学', 2 => '中学', 3 => '大学');

	/* (non-PHPdoc)
	 * @see WindController::run()
	 */
	public function run() {
		list($type, $areaid, $name, $first) = $this->getInput(array('type', 'areaid', 'name', 'first'));
		!$type && $type = 3;
		$schoolSo = new WindidSchoolSo();
		$schoolSo->setName($name)
			->setTypeid($type)
			->setFirstChar($first)
			->setAreaid($areaid);
		
		$list = array();
		$areaRoute = WindidApi::api('area')->getAreaRout($areaid);
		$_rout = array('all' =>array('disable' => 'li_disabled'), 'province' => array('areaid' =>'', 'name' => '', 'display' => 'display:none;'),
			'city' => array('areaid' =>'', 'name' => '', 'display' => 'display:none;'), 'area' => array('areaid' =>'', 'name' => '', 'display' => 'display:none;'));
		switch (count($areaRoute)) {
			case 1:
				$_rout['all']['disable'] = '';
				$_rout['province']['name'] = $areaRoute[0]['name'];
				$_rout['province']['areaid'] = $areaRoute[0]['areaid'];
				$_rout['province']['display'] = '';
				break;
			case 2:
				$_rout['all']['disable'] = '';
				$_rout['province']['name'] = $areaRoute[0]['name'];
				$_rout['province']['areaid'] = $areaRoute[0]['areaid'];
				$_rout['province']['display'] = '';
				$_rout['city']['name'] = $areaRoute[1]['name'];
				$_rout['city']['areaid'] = $areaRoute[1]['areaid'];
				$_rout['city']['display'] = '';
				break;
			case 3:
				$_rout['all']['disable'] = '';
				$_rout['province']['name'] = $areaRoute[0]['name'];
				$_rout['province']['areaid'] = $areaRoute[0]['areaid'];
				$_rout['province']['display'] = '';
				$_rout['city']['name'] = $areaRoute[1]['name'];
				$_rout['city']['areaid'] = $areaRoute[1]['areaid'];
				$_rout['city']['display'] = '';
				$_rout['area']['name'] = $areaRoute[2]['name'];
				$_rout['area']['areaid'] = $areaRoute[2]['areaid'];
				$_rout['area']['display'] = '';
			default:
				break;
		}
		if ($areaid) {
			$list = $this->_getDs()->searchSchool($schoolSo, 100);
		}
		$this->setOutput($list, 'list');
		$this->setOutput($schoolSo->getData(), 'data');
		$this->setOutput($_rout, 'route');
		$this->setOutput($type, 'type');
		$this->setOutput($this->type, 'schools');
	}

	/**
	 * 更新学校
	 */
	public function updateAction() {
		list($update, $add, $areaid, $typeid) = $this->getInput(array('update', 'add', 'areaid', 'typeid'), 'post');

		is_array($update) || $update = array();
		is_array($add) || $add = array();

		foreach ($update as $id => $name) {
			$dm = new WindidSchoolDm();
			$dm->setSchoolid($id)
				->setName($name)
				->setFirstChar($this->_getDs()->getFirstChar($name));
			$r = $this->_getDs()->updateSchool($dm);
			if ($r < 1) $this->showError('WINDID:code.' . $r);
		}
		if ($add) {
			$addDms = array();
			foreach ($add as $name) {
				$dm = new WindidSchoolDm();
				$dm->setName($name)
					->setTypeid($typeid)
					->setAreaid($areaid)
					->setFirstChar($this->_getDs()->getFirstChar($name));
				$addDms[] = $dm;
			}
			$r = $this->_getDs()->batchAddSchool($addDms);
			if ($r < 1) $this->showError('WINDID:code.' . $r);
		}
		$this->showMessage('success', 'windidclient/schooldata/run?type=' . $typeid . '&areaid=' . $areaid);
	}
	
	/**
	 * 删除学校
	 */
	public function deleteAction() {
		$schoolid = $this->getInput('schoolid');
		if (!$schoolid) $this->showError('ADMIN:school.schoolid.error');
		$this->_getDs()->deleteSchool($schoolid);
		$this->showMessage('ADMIN:school.delete.success');
	}
	
	/**
	 * 学校Ds
	 *
	 * @return WindidSchool
	 */
	private function _getDs() {
		return WindidApi::api('school');
	}
}