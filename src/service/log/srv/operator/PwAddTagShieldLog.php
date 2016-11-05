<?php
//TODO 话题屏蔽-对应类型获取临时解决方案
/**
 * 添加话题屏蔽的管理日志
 *
 * @author xiaoxia.xu<xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwAddTagShieldLog.php 21242 2012-12-03 07:50:27Z xiaoxia.xuxx $
 * @package src.service.log.srv.operator
 */
class PwAddTagShieldLog extends PwBaseHookService {
	public $typeId;
	public $tagId;
	public $paramId;
	public $user;
	public $ifShield = true;

	/**
	 * 构造函数
	 *
	 * @param int $tagId
	 * @param int $typeId
	 * @param int $paramId
	 * @param PwUserBo $user
	 */
	public function __construct($tagId, $typeId, $paramId, PwUserBo $user) {
		parent::__construct();
		$this->typeId = intval($typeId);
		$this->tagId = intval($tagId);
		$this->paramId = intval($paramId);
		$this->user = $user;
	}

	/**
	 * 设置是否屏蔽还是取消屏蔽
	 *
	 * @param boolean $ifShield
	 * @return PwAddTagShieldLog
	 */
	public function setIfShield($ifShield = true) {
		$this->ifShield = $ifShield == 1 ? false : true;
		return $this;
	}

	/**
	 * 执行
	 */
	public function execute() {
		$tagInfo = $this->getData();
		if (!$tagInfo) return false;
		$this->_init();
		$this->runDo('gleanData', $tagInfo);
		$this->runDo('run', $this->paramId);
		return true;
	}
	
	protected function getData() {
		return Wekit::load('tag.PwTag')->getTag($this->tagId);
	}

	/**
	 * 添加类型支持
	 */
	protected function _init() {
		switch ($this->typeId) {
			case PwTag::TYPE_THREAD_TOPIC:
				Wind::import('SRV:log.srv.datasource.PwShieldTagDoTopic');
				$this->appendDo(new PwShieldTagDoTopic($this));
				break;
			case PwTag::TYPE_THREAD_REPLY:
				Wind::import('SRV:log.srv.datasource.PwShieldTagDoReply');
				$this->appendDo(new PwShieldTagDoReply($this));
				break;
			case PwTag::TYPE_WEIBO:
				Wind::import('SRV:log.srv.datasource.PwShieldTagDoWeibo');
				$this->appendDo(new PwShieldTagDoWeibo($this));
				break;
			default :
				break;
		}
	}
	
	/* (non-PHPdoc)
	 * @see PwBaseHookService::_getInterfaceName()
	 */
	protected function _getInterfaceName() {
		return 'iPwGleanDoHookProcess';
	}
}