<?php
Wind::import('APPS:windid.admin.WindidBaseController');
Wind::import('WSRV:school.dm.WindidSchoolDm');
Wind::import('WSRV:school.vo.WindidSchoolSo');


/**
 * 全局-资料库-学校库
 *
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: SchooldataController.php 24837 2013-02-22 06:59:57Z jieyin $
 * @package applications.config.admin
 */
class SchooldataController extends WindidBaseController {
	
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
		$areaRoute = Wekit::load('WSRV:area.srv.WindidAreaService')->getAreaRout($areaid);
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
				->setFirstChar($this->_getService()->getFirstChar($name));
			$r = $this->_getDs()->updateSchool($dm);
			if ($r instanceof WindidError) $this->showError('WINDID:code.' . $r->getCode());
		}
		$addDms = array();
		foreach ($add as $name) {
			$dm = new WindidSchoolDm();
			$dm->setName($name)
				->setTypeid($typeid)
				->setAreaid($areaid)
				->setFirstChar($this->_getService()->getFirstChar($name));
			$addDms[] = $dm;
		}
		$r = $this->_getDs()->batchAddSchool($addDms);
		if ($r instanceof WindidError) $this->showError('WINDID:code.' . $r->getCode());
		$this->showMessage('success', 'windid/schooldata/run?type=' . $typeid . '&areaid=' . $areaid);
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
		return Wekit::load('WSRV:school.WindidSchool');
	}
	
	/**
	 * 学校的service
	 *
	 * @return WindidSchoolService
	 */
	private function _getService() {
		return Wekit::load('WSRV:school.srv.WindidSchoolService');
	}
}