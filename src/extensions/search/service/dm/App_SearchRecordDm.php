<?php

/**
 * 搜索记录DM
 */
class App_SearchRecordDm extends PwBaseDm {
	
	/** 
	 * 设置关键词
	 *
	 * @param string $keywords
	 * @return PwSearchRecordDm
	 */
	public function setKeywords($keywords) 
	{
		$this->_data['keywords'] = trim($keywords);
		return $this;
	}
	
	/** 
	 * 设置类型
	 *
	 * @param int $search_type
	 * @return PwSearchRecordDm
	 */
	public function setSearchType($search_type) 
	{
		$this->_data['search_type'] = intval($search_type);
		return $this; 
	}
	
	/** 
	 * 设置创建人
	 *
	 * @param int $created_userid
	 * @return PwSearchRecordDm
	 */
	public function setCreatedUserid($created_userid) 
	{
		$this->_data['created_userid'] = intval($created_userid);
		return $this; 
	}
	
	/** 
	 * 设置创建时间
	 *
	 * @param int $created_time
	 * @return PwSearchRecordDm
	 */
	public function setCreatedTime($created_time) 
	{
		$this->_data['created_time'] = intval($created_time);
		return $this; 
	}
	
	public function addNum($v)
	{
		$this->_increaseData['num'] = (int)$v;
		return $this;
	}
	
	/* (non-PHPdoc)
	 * @see PwBaseDm::_beforeAdd()
	 */
	protected function _beforeAdd()
	{
		return $this->check();
	}

	/* (non-PHPdoc)
	 * @see PwBaseDm::_beforeUpdate()
	 */
	protected function _beforeUpdate() 
	{
		return $this->check();
	}
	
	/**
	 * 检查数据
	 *
	 * @return PwError
	 */
	protected function check()
	{
		if (!isset($this->_data['created_userid'])) return new PwError('user.not.login');
		return true;
	}
}