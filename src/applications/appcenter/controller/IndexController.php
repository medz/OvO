<?php
/**
 *
 * @author jinling.su<emily100813@gmail.com> 2012-5-28
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2010 phpwind.com
 * @version $Id: IndexController.php 24585 2013-02-01 04:02:37Z jieyin $
 */
class IndexController extends PwBaseController {
	private $perpage = 10;
	private $orderBy = array('time' => 'created_time');

	public function run() {
		$page = intval($this->getInput('page'));
		$page < 1 && $page = 1;
		list($start, $num) = Pw::page2limit($page, $this->perpage);
		$orderBy = $this->getInput('orderby', 'get');
		if (!$orderBy || !isset($this->orderBy[$orderBy])) {
			$orderBy = key($this->orderBy);
		}
		$count = $this->_appDs()->countByStatus(9);
		$apps = $this->_appDs()->fetchListByStatus($num, $start, 9, $this->orderBy[$orderBy]);
		$return = array();
		foreach ($apps as $k => $v) {
			$return[] = array(
				'app_id' => $k, 
				'name' => $v['name'], 
				'logo' => $v['logo'], 
				'alias' => $v['alias'], 
				'desc' => $v['description'] ? $v['description'] : '这家伙很懒', 
				'url' => $v['status'] & 8 ? WindUrlHelper::createUrl('appcenter/apps/run', array('appid' => $v['app_id'])) : WindUrlHelper::createUrl('app/index/run', array('app' => $v['alias'])));
		}
		$this->setOutput(
			array(
				'apps' => $return, 
				'count' => $count, 
				'perpage' => $this->perpage, 
				'page' => $page,
				'orderby' => $orderBy
				));
		$this->setTemplate('app_index_run');
		// seo设置
		Wind::import('SRV:seo.bo.PwSeoBo');
		$seoBo = PwSeoBo::getInstance();
		$lang = Wind::getComponent('i18n');
		$seoBo->setCustomSeo($lang->getMessage('SEO:appcenter.appindex.run.title'), '', '');
		Wekit::setV('seo', $seoBo);
	}

	/**
	 *
	 * @return PwApplication
	 */
	private function _appDs() {
		return Wekit::load('APPCENTER:service.PwApplication');
	}
}