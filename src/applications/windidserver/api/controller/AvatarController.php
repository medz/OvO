<?php
Wind::import('APPS:api.controller.OpenBaseController');

/**
 * 用户头像公共服务
 * 
 * @author Jianmin Chen <sky_hold@163.com>
 * @license http://www.phpwind.com
 * @version $Id: AvatarController.php 28965 2013-05-31 11:36:01Z jieyin $
 * @package windid.service.avatar
 */
class AvatarController  extends OpenBaseController{
	
	public $attachUrl = '';
	
	public function __construct() {
		$this->attachUrl = Wekit::url()->attach;
	}

	public function getAvatarUrlAction() {
		$result = substr(Pw::getPath('1.gpg'), 0, -6);
		$this->output($result);
	}

	public function getStoragesAction() {
		$attService = Wekit::load('LIB:storage.PwStorage');
		$storages = $attService->getStorages();
		$this->output($storages);
	}

	public function setStoragesAction() {
		$storage = $this->getInput('storage', 'post');
		$attService = Wekit::load('LIB:storage.PwStorage');
		$_r = $attService->setStoragesComponents($storage);
		if ($_r !== true) {
			$this->output(0);
		}
		$config = new PwConfigSet('attachment');
		$config->set('storage.type', $storage)->flush();
		
		$components = Wekit::C()->get('components')->toArray();
		Wind::getApp()->getFactory()->loadClassDefinitions($components);
		Wekit::C()->setConfig('site', 'avatarUrl', substr(Pw::getPath('1.gpg'), 0, -6));

		$this->_getNotifyService()->send('alterAvatarUrl', array(), $this->appid);
		$this->output(1);
	}
	
	/**
	 * 获取用户头像
	 *
	 * @param $uid
	 * @param $size big middle small
	 * @return string
	 */
	public function getAction() {
		$uid = $this->getInput('uid', 'get');
		$size = $this->getInput('size', 'get');
		!$size && $size = 'middle';
 		$file = $uid . (in_array($size, array('middle', 'small')) ? '_' . $size : '') . '.jpg';
		$result = $this->attachUrl . '/avatar/' . Pw::getUserDir($uid) . '/' . $file;
		$this->output($result);
	}
	
	
	/**
	 * 还原头像
	 *
	 * @param int $uid
	 * @param string $type 还原类型-一种默认头像face*,一种是禁止头像ban*
	 * @return boolean
	 */
	public function defaultAction() {
		$uid = $this->getInput('uid', 'post');
		$type = $this->getInput('type', 'post');
		!$type && $type = 'face';
		$srv = Wekit::load('WSRV:user.srv.WindidUserService');
		$result = $srv->defaultAvatar($uid, $type);
		$this->output($result);
	}
	
	/**
	 * 获取头像上传代码
	 *
	 * @param int $uid 用户uid
	 * @param int $getHtml 获取代码|配置
	 * @return string|array
	 */
	public function getFlashAction() {
		$this->output(0);
	}
	
	/**
	 * 上传头像
	 */
	public function doavatarAction() {
		$uid = (int)$this->getInput('uid', 'get');
		Wind::import('WSRV:upload.action.WindidAvatarUpload');
		Wind::import('LIB:upload.PwUpload');
		$bhv = new WindidAvatarUpload($uid);
		$upload = new PwUpload($bhv);
		if (($result = $upload->check()) === true) {
			$result = $upload->execute();
		} else {
			$this->output($this->errorCode('upload.content.error'));
		}
		if ($result instanceof PwError) {
			$this->output($this->errorCode($result->getError()));
		} else {
			$this->_getNotifyService()->send('uploadAvatar', array('uid'=>$uid), 0); //服务端发送通知
			$this->output(1);
		}
	}
	
	private function errorCode($msg) {
		is_array($msg) && $msg = current($msg);
		switch ($msg) {
			case 'upload.ext.error':
				return WindidError::UPLOAD_EXT_ERROR;
			case 'upload.size.less':
				return WindidError::UPLOAD_SIZE_LESS;
			case 'upload.size.over':
				return WindidError::UPLOAD_SIZE_OVER;
			case 'upload.content.error':
				return WindidError::UPLOAD_CONTENT_ERROR;
			default:
				return WindidError::UPLOAD_FAIL;
		}
	}
	
	private function _getNotifyService() {
		return Wekit::load('WSRV:notify.srv.WindidNotifyService');
	}
	
}