<?php
Wind::import('APPS:windid.admin.WindidBaseController');
Wind::import('WSRV:area.dm.WindidAreaDm');

/**
 * 全局-资料库-地区库
 *
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: AreadataController.php 24489 2013-01-31 02:59:09Z jieyin $
 * @package applications.config.admin
 */
class AreadataController extends WindidBaseController {

	/* (non-PHPdoc)
	 * @see WindController::run()
	 */
	public function run() {
		$parentid = intval($this->getInput('parentid'));
		$parentid || $parentid = 0;
		$list = $this->_loadAreaDs()->getAreaByParentid($parentid);
		$rout = array();
		if (0 < $parentid) {
			$rout = $this->_loadAreaService()->getAreaRout($parentid);
		}
		$this->setOutput($parentid, 'areaid');
		$this->setOutput($list, 'list');
		$_rout = array('all' =>array('disable' => 'li_disabled'), 'province' => array('areaid' =>'', 'name' => '', 'disable' => 'li_disabled', 'display' => 'display:none;'),
			'city' => array('areaid' =>'', 'name' => '', 'display' => 'display:none;'));
		switch (count($rout)) {
			case 1:
				$_rout['all']['disable'] = '';
				$_rout['province']['name'] = $rout[0]['name'];
				$_rout['province']['areaid'] = $rout[0]['areaid'];
				$_rout['province']['display'] = '';
				break;
			case 2:
				$_rout['all']['disable'] = '';
				$_rout['province']['name'] = $rout[0]['name'];
				$_rout['province']['areaid'] = $rout[0]['areaid'];
				$_rout['province']['disable'] = '';
				$_rout['province']['display'] = '';
				$_rout['city']['name'] = $rout[1]['name'];
				$_rout['city']['areaid'] = $rout[1]['areaid'];
				$_rout['city']['display'] = '';
				break;
			default:
				break;
		}
		$this->setOutput(count($rout), 'hasLevel');
		$this->setOutput($_rout, 'route');
	}

	/**
	 * 更新地区
	 */
	public function updateAction() {
		list($update, $add, $parentid) = $this->getInput(array('update', 'add', 'parentid'), 'post');

		is_array($update) || $update = array();
		is_array($add) || $add = array();

		$joinname = '';
		if ($parentid) {
			$rout = $this->_loadAreaService()->getAreaRout($parentid);
			switch (count($rout)) {
				case 0:
					$this->showError('ADMIN:area.parentid.error');
					break;
				case 3:
					$this->showError('ADMIN:area.level.limit');
					break;
			}
			$joinnames = array();
			foreach ($rout as $i) {
				$joinnames[] = $i['name'];
			}
			$joinname = implode('|', $joinnames);
		}
		foreach ($update as $id => $name) {
			$dm = new WindidAreaDm();
			$dm->setAreaid($id)->setName($name)
				->setJoinname($joinname ? $joinname . '|' . $name : $name);
			if (true !== ($r = $this->_loadAreaDs()->updateArea($dm))) {
				$this->showError(array('ADMIN:area.error.' . $r->getCode(), array('{flag}' => "“& \" ' < > \ / ”")));
			}
		}
		$addDms = array();
		foreach ($add as $name) {
			$dm = new WindidAreaDm();
			$dm->setName($name)
				->setParentid($parentid)
				->setJoinname($joinname ? $joinname . '|' . $name : $name);
			if (true !== ($r = $dm->beforeAdd())) {
				$this->showError(array('ADMIN:area.error.' . $r->getCode(), array('{flag}' => "“& \" ' < > \ / ”")));
			}
			$addDms[] = $dm;
		}
		$this->_loadAreaDs()->batchAddArea($addDms);
		//$this->_loadAreaService()->updateCache();
		$this->showMessage('success', 'admin/windid/areadata/run?parentid=' . $parentid);
	}
	
	/**
	 * 删除地区
	 */
	public function deleteAction() {
		$areaid = $this->getInput('areaid');
		if (!$areaid) $this->showError('ADMIN:area.areaid.error');
		$childs = $this->_loadAreaDs()->getAreaByParentid($areaid);
		if ($childs) $this->showError('ADMIN:area.delete.error.has.children');
		$this->_loadAreaDs()->deleteArea($areaid);
		$this->showMessage('ADMIN:area.delete.success');
	}
	
	/**
	 * 获得地区service
	 *
	 * @return WindidAreaService
	 */
	private function _loadAreaService() {
		return Wekit::load('WSRV:area.srv.WindidAreaService');
	}
	
	/**
	 * 获得area的DS
	 *
	 * @return WindidArea
	 */
	private function _loadAreaDs() {
		return Wekit::load('WSRV:area.WindidArea');
	}
}