<?php
defined('WEKIT_VERSION') || exit('Forbidden');
/**
 * 帖子搜索条件
 */
class PwThreadSo 
{
	
	protected $_data = array();
	protected $_orderby = array();

	public function getData()
	{
		return $this->_data;
	}

	public function getOrderby()
	{
		return $this->_orderby;
	}
	
	/**
	 * 搜索多个tid
	 */
	public function setTid($tid)
	{
		$this->_data['tid'] = $tid;
		return $this;
	}
	
	/**
	 * 搜索帖子标题
	 */
	public function setKeywordOfTitle($keyword)
	{
		$this->_data['title_keyword'] = $keyword;
		return $this;
	}
	
	/**
	 * 搜索帖子内容
	 */
	public function setKeywordOfContent($keyword)
	{
		$this->_data['content_keyword'] = $keyword;
		return $this;
	}
	
	/**
	 * 搜索帖子标题或内容
	 */
	public function setKeywordOfTitleOrContent($keyword)
	{
		$this->_data['title_and_content_keyword'] = $keyword;
		return $this;
	}
	
	/**
	 * 帖子是否可用
	 */
	public function setDisabled($disabled)
	{
		$this->_data['disabled'] = $disabled;
		return $this;
	}

	/**
	 * 搜索版块
	 */
	public function setFid($fid)
	{
		$this->_data['fid'] = $fid;
		return $this;
	}

	public function setNoInFid($fid)
	{
		$this->_data['nofid'] = $fid;
		return $this;
	}		
	
	/**
	 * 搜索主题分类
	 */
	public function setTopicType($type)
	{
		$this->_data['topic_type'] = $type;
		return $this;
	}
	
	/**
	 * 搜索作者
	 */
	public function setAuthor($authors)
	{
		if (!is_array($authors)) {
			$authors = array($authors);
		} 
		$users = Wekit::load('user.PwUser')->fetchUserByName($authors);
		$this->setAuthorId(array_keys($users));
		return $this;
	}

	/**
	 * 搜索多个作者id
	 */
	public function setAuthorId($authorid)
	{
		if (!$authorid) $authorid = array(0);
		$this->_data['created_userid'] = $authorid;
		return $this;
	}
	
	/**
	 * 搜索多个主题类型
	 */
	public function setSpecial($special)
	{
		$this->_data['special'] = $special;
		return $this;
	}
	
	/**
	 * 多个置顶级
	 */
	public function setTopped($topped)
	{
		$this->_data['topped'] = $topped;
		return $this;
	}
	
	/**
	 * 是否精华
	 */
	public function setDigest($digest)
	{
		$this->_data['digest'] = intval($digest);
		return $this;
	}
	
	/**
	 * 是否包含图片
	 */
	public function setHasImage($bool)
	{
		$this->_data['hasimage'] = $bool ? 1 : 0;
		return $this;
	}
	
	/**
	 * 点击率区间，起始
	 */
	public function setHitsStart($hits)
	{
		$this->_data['hits_start'] = $hits;
		return $this;
	}
	
	/**
	 * 点击率区间，结束
	 */
	public function setHitsEnd($hits)
	{
		$this->_data['hits_end'] = $hits;
		return $this;
	}
	
	/**
	 * 回复数区间，起始
	 */
	public function setRepliesStart($replies)
	{
		$this->_data['replies_start'] = $replies;
		return $this;
	}
	
	/**
	 * 回复数区间，结束
	 */
	public function setRepliesEnd($replies)
	{
		$this->_data['replies_end'] = $replies;
		return $this;
	}

	public function setCreatedIp($ip)
	{
		$this->_data['created_ip'] = $ip;
		return $this;
	}
	
	/**
	 * 发帖时间区间，起始
	 */
	public function setCreateTimeStart($time)
	{
		$this->_data['created_time_start'] = $time;
		return $this;
	}
	
	/**
	 * 发帖时间区间，结束
	 */
	public function setCreateTimeEnd($time)
	{
		$this->_data['created_time_end'] = $time + 86400;
		return $this;
	}
	
	/**
	 * 回复时间区间，起始
	 */
	public function setLastpostTimeStart($start)
	{
		$this->_data['lastpost_time_start'] = $start;
		return $this;
	}
	
	/**
	 * 回复时间区间，结束
	 */
	public function setLastpostTimeEnd($end)
	{
		$this->_data['lastpost_time_end'] = $end;
		return $this;
	}

	/**
	 * 发帖时间排序
	 */
	public function orderbyCreatedTime($asc)
	{
		$this->_orderby['created_time'] = (bool)$asc;
		return $this;
	}
	
	/**
	 * 回复时间排序
	 */
	public function orderbyLastPostTime($asc)
	{
		$this->_orderby['lastpost_time'] = (bool)$asc;
		return $this;
	}
	
	/**
	 * 回复数排序
	 */
	public function orderbyReplies($asc)
	{
		$this->_orderby['replies'] = (bool)$asc;
		return $this;
	}
	
	/**
	 * 点击数排序
	 */
	public function orderbyHits($asc)
	{
		$this->_orderby['hits'] = (bool)$asc;
		return $this;
	}
	
	/**
	 * 喜欢数排序
	 */
	public function orderbyLike($asc)
	{
		$this->_orderby['like'] = (bool)$asc;
		return $this;
	}
}