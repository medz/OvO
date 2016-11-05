<?php
Wind::import('ADMIN:library.AdminBaseController');
/**
 * 后台菜单管理操作类
 *
 * @author Qiong Wu <papa0924@gmail.com> 2011-10-21
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: SetbbsController.php 28790 2013-05-23 10:15:16Z jieyin $
 * @package admin
 * @subpackage controller
 */
class SetbbsController extends AdminBaseController {

	public function run() {
		$this->forwardAction('bbs/setbbs/thread');
		$config = Wekit::C()->getValues('bbs');
		$this->setOutput($config, 'config');
	}

	public function dorunAction() {
		$this->getRequest()->isPost() || $this->showError('operate.fail');

		list($showBirthdayMembers, $showLinks, $showOnlineUsers, $listOnlineUsers) = $this->getInput(
			array('show_birthday_members', 'show_links', 'show_online_users', 'list_online_users'));
		$config = new PwConfigSet('bbs');
		$config->set('index.show_birthday_members', $showBirthdayMembers)
				->set('index.show_links', $showLinks)
				->set('index.show_online_users', $showOnlineUsers)
				->set('index.list_online_users', $listOnlineUsers)
				->flush();
		$this->showMessage('success');
	}

	public function threadAction() {
		$config = Wekit::C()->getValues('bbs');
		$this->setOutput($config, 'config');
	}

	public function dothreadAction() {
		$this->getRequest()->isPost() || $this->showError('operate.fail');

		list($newThreadMinutes, $perpage, $maxPages, $leftsideWidth, $hotthreadReplies) = $this->getInput(
			array('new_thread_minutes', 'perpage', 'max_pages', 'leftside_width', 'hotthread_replies'));
		$config = new PwConfigSet('bbs');
		$config->set('thread.new_thread_minutes', $newThreadMinutes)
				->set('thread.perpage', $perpage)
				->set('thread.max_pages', $maxPages)
				->set('thread.leftside_width', $leftsideWidth)
				->set('thread.hotthread_replies', $hotthreadReplies)
				->flush();
		$this->showMessage('success');
	}

	public function readAction() {
		$config = Wekit::C()->getValues('bbs');
		$order = $config['read.display_info_vieworder'];
		is_array($order) || $order = array();
		
		$allInfo = array(
			'uid' => 'UID', 
			'regdate' => '注册日期', 
			'lastvisit' => '最后登录', 
			'fans' => '粉丝', 
			'follows' => '关注', 
			'posts' => '发帖数', 
			'homepage' => '个人主页', 
			'location' => '来自', 
			'qq' => 'QQ', 
			'aliww' => '阿里旺旺', 
			'birthday' => '生日', 
			'hometown' => '家乡');
		Wind::import('SRV:credit.bo.PwCreditBo');
		foreach (PwCreditBo::getInstance()->cType as $key => $value) {
			$allInfo[$key] = $value;
		}
		$i = 10000;
		foreach ($allInfo as $key => $value) {
			(!isset($order[$key]) || $order[$key] === '') && $order[$key] = $i++;
		}
		asort($order);
		reset($order);
		
		$this->setOutput($order, 'order');
		$this->setOutput($allInfo, 'allInfo');
		$this->setOutput($config, 'config');
	}

	public function doreadAction() {
		$this->getRequest()->isPost() || $this->showError('operate.fail');

		$arrInput = $this->getInput(
			array(
				'perpage', 
				'anoymous_displayname', 
				'shield_banthreads', 
				'floor_name', 
				'defined_floor_name',
				'hit_update',
				'image_lazy', 
				'display_member_info', 
				'display_info_vieworder', 
				'display_info'), 'POST', true);

		$i = 10000;
		foreach ($arrInput['display_info_vieworder'] as $key => $value) {
			$value === '' && $arrInput['display_info_vieworder'][$key] = $i++;
		}
		asort($arrInput['display_info_vieworder']);
		reset($arrInput['display_info_vieworder']);
		$i = 0;
		$display_info = array();
		foreach ($arrInput['display_info_vieworder'] as $key => $value) {
			$arrInput['display_info_vieworder'][$key] = $i++;
			isset($arrInput['display_info'][$key]) && $display_info[$key] = 1;
		}
		$config = new PwConfigSet('bbs');
		$config->set('read.perpage', $arrInput['perpage'])
				->set('read.anoymous_displayname', $arrInput['anoymous_displayname'])
				->set('read.shield_banthreads', $arrInput['shield_banthreads'])
				->set('read.floor_name', $arrInput['floor_name'])
				->set('read.defined_floor_name', $arrInput['defined_floor_name'])
				->set('read.display_member_info', $arrInput['display_member_info'])
				->set('read.hit_update', $arrInput['hit_update'])
				->set('read.image_lazy', $arrInput['image_lazy'])
				->set('read.display_info_vieworder', $arrInput['display_info_vieworder'])
				->set('read.display_info', $display_info)
				->flush();
		$this->showMessage('success', 'bbs/setbbs/read');
	}
}
?>