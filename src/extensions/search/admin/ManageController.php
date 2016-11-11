<?php
Wind::import('ADMIN:library.AdminBaseController');
Wind::import('SRV:config.srv.PwConfigSet');

class ManageController extends AdminBaseController {

	/* (non-PHPdoc)
	 * @see WindController::run()
	 */
	public function run()
	{
		$conf = Wekit::C('search');
		$this->setOutput($conf, 'conf');
		$stypes = $this->_getSearchService()->getTypes(true);
		$this->setOutput($stypes, 'stypes');
	}

	/**
	 * 保存搜索设置
	 *
	 */
	public function doRunAction()
	{
		$conf = $this->getInput('conf', 'post');
		$config = new PwConfigSet('search');
		if(!$conf['types'])
		{
			$this->showError('请至少选择一种搜索方法');	
		}
		$config->set('isopen', $conf['isopen'])->set('types', $conf['types'])->set('seo.title', $conf['seo_title'])->set('seo.keyword', $conf['seo_keyword'])->set('seo.desc', $conf['seo_desc'])
			->flush();
		$con = new PwConfigSet('site');
		$con->set('search.isopen', $conf['isopen'])->flush();
		$this->showMessage('success');
	}
	
	/**
	 * @return AppSearchService
	 */
	private function _getSearchService()
	{
		return Wekit::load('EXT:search.service.srv.AppSearchService');
	}
}

?>