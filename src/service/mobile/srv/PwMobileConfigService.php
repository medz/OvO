<?php

/**
 * 手机短信服务
 *
 * @author jinlong.panjl <jinlong.panjl@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id$
 * @package wind
 */
class PwMobileConfigService {
	protected $sHook = null;
	
	public function __construct($hookKey) {
		$this->sHook = PwSimpleHook::getInstance($hookKey);
	}
	
	public function getPlats() {
		$conf = Wind::getRealPath('SRV:mobile.config.plat.php', true);
		$tmp = array('name' => '', 'alias' => '', 'managelink' => '', 'description' => '', 'components' => array());
		$plats = @include $conf;
		$plats = $this->sHook->runWithFilters($plats);
		foreach ($plats as $key => $value) {
			$plats[$key] = array_merge($tmp, $value);
		}
		return $plats;
	}

	/**
	 * 设置方案到系统
	 * 
	 * @param string $storageType
	 * @return true|pwError
	 */
	public function setPlatComponents($platType) {
		$plats = $this->getPlats();
		if (!array_key_exists($platType, $plats)) return new PwError('USER:mobile.plat.type.not.exit');
		$plat = $plats[$platType];
		if (!isset($plat['components']['path'])) return new PwError('USER:mobile.plat.config.fail');
		/* @var $componentService PwComponentsService */
		$componentService = Wekit::load('hook.srv.PwComponentsService');
		$componentService->setComponent('mobileplat', $plat['components'], $plat['description']);
		
		$config = new PwConfigSet('mobile');
		$config->set('plat.type', $platType)->flush();
		return true;
	}
}