<?php
defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('LIB:base.PwBaseDm');

/**
 * 
 * 投票选项Dm
 *
 * @author Mingqu Luo<luo.mingqu@gmail.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id$
 * @package wind
 */

class PwPollOptionDm extends PwBaseDm {
	public $id;

	public function __construct($id = 0) {
		$this->id = $id;
	}
	
	/**
	 * 设置投票ID
	 *
	 * @param int $pollid
	 * @return PwPollOptionDm
	 */
	public function setPollid($pollid) {
		$this->_data['poll_id'] = intval($pollid);
		return $this;
	}

	/**
	 * 增加投票数
	 *
	 * @param int $votedNum
	 * @return PwPollOptionDm
	 */
	public function addVotedNum($votedNum) {
		$this->_increaseData['voted_num'] = intval($votedNum);
		return $this;
	}

	/**
	 * 设置投票数
	 *
	 * @param int $votedNum
	 * @return PwPollOptionDm
	 */
	public function setVotedNum($votedNum) {
		$this->_data['voted_num'] = intval($votedNum);
		return $this;
	}
	
	/**
	 * 设置投票选项内容
	 *
	 * @param string $content
	 * @return PwPollOptionDm
	 */
	public function setContent($content) {
		$this->_data['content'] = trim($content);
		return $this;
	}
	
	/**
	 * 设置词语来源
	 *
	 * @param string $image
	 * @return PwPollOptionDm
	 */
	public function setImage($image) {
		$this->_data['image'] = $image;
		return $this;
	}
	
	protected function _beforeAdd() {
		return true;
	}

	protected function _beforeUpdate() {
		return true;
	}
}