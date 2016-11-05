<?php

Wind::import('ADMIN:library.AdminBaseController');

/**
 * @author Qiong Wu <papa0924@gmail.com> 2011-12-15
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: AttachmentController.php 3284 2011-12-15 08:38:49Z yishuo $
 * @package admin
 * @subpackage controller.config
 */
class AttachmentController extends AdminBaseController {
	
	/* (non-PHPdoc)
	 * @see WindController::run()
	 */
	public function run() {
		$config = Wekit::C()->getValues('attachment');
		!($post_max_size = ini_get('post_max_size')) && $post_max_size = '2M';
		!($upload_max_filesize = ini_get('upload_max_filesize')) && $upload_max_filesize = '2M';
		$maxSize = min($post_max_size, $upload_max_filesize);

		$this->setOutput($maxSize, 'maxSize');
		$this->setOutput($config, 'config');
	}

	/**
	 * 后台设置-附件设置
	 */
	public function dorunAction() {
		$this->getRequest()->isPost() || $this->showError('operate.fail');

		list($pathsize, $attachnum, $extsize) = $this->getInput(array('pathsize', 'attachnum', 'extsize'), 'post');
		$_extsize = array();
		foreach ($extsize as $key => $value) {
			if (!empty($value['ext'])) $_extsize[$value['ext']] = abs(intval($value['size']));
		}
		$config = new PwConfigSet('attachment');
		$config->set('pathsize', abs(intval($pathsize)))->set('attachnum', abs(intval($attachnum)))->set('extsize', 
			$_extsize)->flush();
		$this->showMessage('ADMIN:success');
	}

	/**
	 * 附件存储方式设置列表页
	 */
	public function storageAction() {
		/* @var $attService PwAttacmentService */
		$attService = Wekit::load('LIB:storage.PwStorage');
		$storages = $attService->getStorages();
		$config = Wekit::C()->getValues('attachment');
		$storageType = 'local';
		if (isset($config['storage.type']) && isset($storages[$config['storage.type']])) {
			$storageType = $config['storage.type'];
		}

		$windidStorages = WindidApi::api('avatar')->getStorages();
		$windidStorageType = Wekit::app('windid')->config->attachment->get('storage.type');
		foreach ($windidStorages as $key => $value) {
			if ($value['managelink']) {
				$windidStorages[$key]['managelink'] = str_replace(Wekit::url()->base, Wekit::app('windid')->url->base, WindUrlHelper::createUrl($value['managelink']));
			}
		}

		$this->setOutput($storages, 'storages');
		$this->setOutput($storageType, 'storageType');
		$this->setOutput($windidStorages, 'windidStorages');
		$this->setOutput($windidStorageType, 'windidStorageType');
	}

	/**
	 * 附件存储方式设置列表页
	 */
	public function dostroageAction() {
		$this->getRequest()->isPost() || $this->showError('operate.fail');

		$att_storage = $this->getInput('att_storage', 'post');
		$avatar_storage = $this->getInput('avatar_storage', 'post');

		/* @var $attService PwAttacmentService */
		$attService = Wekit::load('LIB:storage.PwStorage');
		$_r = $attService->setStoragesComponents($att_storage);
		if ($_r !== true) {
			$this->showError($_r->getError());
		}
		$config = new PwConfigSet('attachment');
		$config->set('storage.type', $att_storage)->flush();
		
		$result = WindidApi::api('avatar')->setStorages($avatar_storage);
		if ($result == '1') {
			Wekit::C()->setConfig('site', 'avatarUrl', WindidApi::api('avatar')->getAvatarUrl());
		}

		$this->showMessage('ADMIN:success');
	}

	/**
	 * 后台设置-附件缩略设置
	 */
	public function thumbAction() {
		$config = Wekit::C()->getValues('attachment');
		$this->setOutput($config, 'config');
// 		$this->setOutput(Wekit::C('attachment'), 'config');
	}

	/**
	 * 后台设置-附件缩略设置
	 */
	public function dothumbAction() {
		$this->getRequest()->isPost() || $this->showError('operate.fail');

		list($thumb, $thumbsize_width, $thumbsize_height, $quality) = $this->getInput(
			array('thumb', 'thumbsize_width', 'thumbsize_height', 'quality'), 'post');

		$config = new PwConfigSet('attachment');
		$config->set('thumb', intval($thumb))
			->set('thumb.size.width', $thumbsize_width)
			->set('thumb.size.height', $thumbsize_height)
			->set('thumb.quality', $quality)
			->flush();
		$this->showMessage('ADMIN:success');
	}

	/**
	 * 后台设置-附件缩略预览
	 */
	public function viewAction() {
		$this->getRequest()->isPost() || $this->showError('operate.fail');

		list($thumb, $thumbsize_width, $thumbsize_height, $quality) = $this->getInput(
			array('thumb', 'thumbsize_width', 'thumbsize_height', 'quality'), 'post');
		
		Wind::import('LIB:image.PwImage');
		$image = new PwImage(Wind::getRealDir('REP:demo', false) . '/demo.jpg');
		$thumburl = Wind::getRealDir('PUBLIC:attachment', false) . '/demo_thumb.jpg';
		$image->makeThumb($thumburl, $thumbsize_width, $thumbsize_height, $quality, $thumb);
		
		$data = array('img' => Wekit::url()->attach . '/demo_thumb.jpg?' . time());
		$this->setOutput($data, 'data');
		$this->showMessage('ADMIN:success');
	}
}

?>