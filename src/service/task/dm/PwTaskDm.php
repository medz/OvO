<?php
Wind::import('LIB:base.PwBaseDm');
/**
 * 任务体系的数据模型
 *
 * @author Shi Long <long.shi@alibaba-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwTaskDm.php 20911 2012-11-19 06:56:00Z xiaoxia.xuxx $
 * @package service.task.dm
 */
class PwTaskDm extends PwBaseDm {
	
	const MAXENDTIME = 4197024000;
	/**
	 * @var PwTaskRewardDoBase 
	 */
	protected $decoration = null;
	
	/**
	 * 任务id
	 *
	 * @var int
	 */
	protected $id;
	
	protected $groups = array();

	/**
	 * 组装任务用户组数据
	 *
	 * @return array 
	 */
	public function getTaskGroupData() {
		if ($this->id <= 0) return array();
		$tmp = array();
		$endTime = isset($this->_data['end_time']) ? $this->_data['end_time'] : 0;
		$endTime = $endTime ? $endTime : self::MAXENDTIME;
		$groups = $this->_data['is_display_all'] ? array(-1) : $this->groups;
		foreach ($groups as $v) {
			$data = array();
			$data['taskid'] = $this->id;
			$data['gid'] = $v;
			$data['is_auto'] = isset($this->_data['is_auto']) ? $this->_data['is_auto'] : 0;
			$data['end_time'] = $endTime;
			$tmp[] = $data;
		}
		return $tmp;
	}

	/**
	 * 获取任务id
	 *
	 * @return int
	 */
	public function getTaskId() {
		return $this->id;
	}

	/**
	 * 设置任务id
	 *
	 * @param int $id
	 */
	public function setTaskId($id) {
		$this->id = (int) $id;
		return $this;
	}

	/**
	 * 设置任务的是否自动申请
	 *
	 * @param int $isAuto
	 * @return PwTaskDm
	 */
	public function setIsAuto($isAuto) {
		$this->_data['is_auto'] = (int) $isAuto;
		return $this;
	}

	/**
	 * 设置任务的是否显示所有
	 *
	 * @param int $isDisplayAll
	 * @return PwTaskDm
	 */
	public function setIsDisplayAll($isDisplayAll) {
		$this->_data['is_display_all'] = (int) $isDisplayAll;
		return $this;
	}

	/**
	 * 设置任务的顺序
	 *
	 * @param int $view_order
	 * @return PwTaskDm
	 */
	public function setViewOrder($view_order) {
		$this->_data['view_order'] = (int) $view_order;
		return $this;
	}

	/**
	 * 设置任务是否启用
	 *
	 * @param int $is_open
	 * @return PwTaskDm
	 */
	public function setIsOpen($is_open) {
		$this->_data['is_open'] = (int) $is_open;
		return $this;
	}

	/**
	 * 设置任务的开始时间
	 *
	 * @param int $startTime
	 * @return PwTaskDm
	 */
	public function setStartTime($startTime) {
		$this->_data['start_time'] = $startTime;
		return $this;
	}

	/**
	 * 设置任务的结束时间
	 *
	 * @param int $endTime
	 * @return PwTaskDm
	 */
	public function setEndTime($endTime) {
		$this->_data['end_time'] = $endTime;
		return $this;
	}

	/**
	 * 设置任务的周期
	 *
	 * @param int $period
	 * @return PwTaskDm
	 */
	public function setPeriod($period) {
		$this->_data['period'] = (int) $period;
		return $this;
	}

	/**
	 * 设置任务的前置任务
	 *
	 * @param int $id
	 * @return PwTaskDm
	 */
	public function setPreTask($id) {
		$this->_data['pre_task'] = (int) $id;
		return $this;
	}

	/**
	 * 设置任务名称
	 *
	 * @param string $title
	 * @return PwTaskDm
	 */
	public function setTitle($title) {
		$this->_data['title'] = trim($title);
		return $this;
	}

	/**
	 * 设置任务的描述
	 *
	 * @param string $description
	 * @return PwTaskDm
	 */
	public function setDescription($description) {
		$this->_data['description'] = trim($description);
		return $this;
	}

	/**
	 * 设置任务的图标
	 *
	 * @param string $icon
	 * @return PwTaskDm
	 */
	public function setIcon($icon) {
		$this->_data['icon'] = trim($icon);
		return $this;
	}

	/**
	 * 设置任务的用户组
	 *
	 * @param array $userGroups
	 * @return PwTaskDm
	 */
	public function setUserGroups($userGroups) {
		$this->groups = (array) $userGroups;
		return $this;
	}

	/**
	 * 设置任务的奖励
	 *
	 * @param string $reward
	 * @return PwTaskDm
	 */
	public function setReward($reward) {
		$this->_data['reward'] = $reward;
		return $this;
	}

	/**
	 * 设置任务的完成条件
	 *
	 * @param array $conditions
	 * @return PwTaskDm
	 */
	public function setConditions($conditions) {
		$this->_data['conditions'] = $conditions;
		return $this;
	}

	/* (non-PHPdoc)
	 * @see PwBaseDm::_beforeAdd()
	 */
	protected function _beforeAdd() {
		if (empty($this->_data['title'])) return new PwError('TASK:title.empty');
		if (empty($this->_data['description'])) return new PwError('TASK:description.empty');
		if (!isset($this->_data['is_open'])) $this->_data['is_open'] = 0;
		$this->_data['user_groups'] = implode(',', $this->groups);
		if (isset($this->_data['end_time']))
			$this->_data['end_time'] = $this->_data['end_time'] ? $this->_data['end_time'] : self::MAXENDTIME;
		else
			$this->_data['end_time'] = self::MAXENDTIME;
		if (($r = $this->filterConditionData()) instanceof PwError) return $r;
		if (($r = $this->filterRewardData()) instanceof PwError) return $r;
		return true;
	}

	/* (non-PHPdoc)
	 * @see PwBaseDm::_beforeUpdate()
	 */
	protected function _beforeUpdate() {
		if (!$this->id) return new PwError('TASK:id.empty');
		if (empty($this->_data['title'])) return new PwError('TASK:title.empty');
		$this->_data['user_groups'] = implode(',', $this->groups);
		if (isset($this->_data['end_time']))
			$this->_data['end_time'] = $this->_data['end_time'] ? $this->_data['end_time'] : self::MAXENDTIME;
		else
			$this->_data['end_time'] = self::MAXENDTIME;
		if (($r = $this->filterConditionData()) instanceof PwError) return $r;
		if (($r = $this->filterRewardData()) instanceof PwError) return $r;
		return true;
	}

	/**
	 * 处理过滤数据
	 * 
	 * 关于完成条件：
	 * 传递过来url: 去做任务的链接，支持{tid}的占位符格式，将会利用$conditions中的变量去替换url中的占位变量
	 * 比如 url=bbs/thread/run?fid={fid},  conditions中有一个元素为condition['fid'] = 10;
	 * 则将会把url替换为: bbs/thread/run?fid=10;
	 * 可以设置一个必填项require:该组成为必填key=>msg(为空的时候输出的信息）
	 * 关于奖励：
	 * 传递过来"key": 该key表明“value”中对应的值的组成，传递过来一个descript占位字串，也由key中对应的组成
	 * 比如key= typeid-name-unity  对应的value= 1-威望-个
	 * descript={num}{unity}{name}  对应将组成： 10个威望  ---num在传递过来的数组中有值
	 */
	protected function filterConditionData() {
		if (!isset($this->_data['conditions'])) return true;
		$condition = $this->_data['conditions'];
		if (!$condition || !is_array($condition)) return new PwError('TASK:condition.require');
		//完成条件
		if (!$condition['child']) return new PwError('TASK:condition.require');
		$url = $condition['url'];
		unset($condition['url']);
		$this->_data['conditions']['url'] = $this->getReplace($condition, $url);
		$this->_data['conditions'] = serialize($this->_data['conditions']);
		return true;
	}
	
	/**
	 * 处理过滤数据
	 * 
	 * 关于奖励：
	 * 传递过来"key": 该key表明“value”中对应的值的组成，传递过来一个descript占位字串，也由key中对应的组成
	 * 比如key= typeid-name-unity  对应的value= 1-威望-个
	 * descript={num}{unity}{name}  对应将组成： 10个威望  ---num在传递过来的数组中有值
	 */
	protected function filterRewardData() {
		if (!isset($this->_data['reward']) || !$this->decoration) {
			$this->_data['reward'] = serialize(array());
			return true;
		}
		if (!is_array($this->_data['reward'])) return new PwError('TASK:condition.reward.format.error');
		
		if ($this->decoration instanceof PwTaskRewardDoBase) {
			$reward = $this->decoration->checkReward($this->_data['reward']);
			if ($reward instanceof PwError) return $reward;
		}		
		$this->_data['reward'] = serialize($reward);
	}

	/**
	 * 返回替换后的地址
	 *
	 * @param array $vars  替换参数
	 * @param string $string 带替换的字符串
	 * @return string 返回替换后的字符串
	 */
	protected function getReplace($vars, $string) {
		$search = array();
		foreach ($vars as $key => $val) {
			$search[] = '{' . $key . '}';
		}
		return str_replace($search, array_values($vars), $string);
	}
	
	/**
	 * 设置奖励
	 *
	 * @param PwTaskRewardDoBase $reward
	 * @return PwTaskDm
	 */
	public function setRewardDecoration(PwTaskRewardDoBase $reward) {
		$this->decoration = $reward;
		return $this;
	}
}
?>