<?php

/**
 * 完成条件扩展实现--论坛类之喜欢帖子
 *
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwTaskBbsLikeDm.php 15745 2012-08-13 02:45:07Z xiaoxia.xuxx $
 * @package src.service.task.dm.condition
 */
class PwTaskBbsLikeDm extends PwTaskDm {
	
	/* (non-PHPdoc)
	 * @see PwTaskDm::filterConditionData()
	 */
	protected function filterConditionData() {
		if (!isset($this->_data['conditions'])) return true;
		$condition = $this->_data['conditions'];
		if (!$condition || !is_array($condition)) return new PwError('TASK:condition.require');
		
		if (!$condition['fid']) return new PwError('TASK:condition.like.fid.require');
		if (!$condition['num']) return new PwError('TASK:condition.like.num.require');
		if (!WindValidator::isNonNegative($condition['num'])) return new PwError('TASK:condition.like.num.isNonNegative');
		$condition['num'] = ceil($condition['num']);
		
		$url = $condition['url'];
		unset($condition['url']);
		$this->_data['conditions']['num'] = $condition['num'];
		$this->_data['conditions']['url'] = $this->getReplace($condition, $url);
		$this->_data['conditions'] = serialize($this->_data['conditions']);
		return true;
	}
}