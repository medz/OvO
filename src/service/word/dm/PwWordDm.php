<?php
defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('LIB:base.PwBaseDm');

/**
 * 词语过滤DM
 *
 * @author Mingqu Luo <luo.mingqu@gmail.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwWordDm.php 8403 2012-04-18 09:29:00Z hejin $
 * @package wind
 */

class PwWordDm extends PwBaseDm {

	public $id;

	public function __construct($id = 0) {
		$this->id = $id;
	}
	
	/**
	 * 设置类型
	 *
	 * @param int $type
	 * @return PwWordFilterDm
	 */
	public function setWordType($type) {
		$this->_data['word_type'] = intval($type);
		return $this;
	}

	/**
	 * 设置词语(敏感词)
	 *
	 * @param string $word
	 * @return PwWordFilterDm
	 */
	public function setWord($word) {
		$this->_data['word'] = trim($word);
		return $this;
	}
	
	/**
	 * 设置替换词
	 *
	 * @param string $wordReplace
	 * @return PwWordFilterDm
	 */
	public function setWordReplace($wordReplace) {
		$this->_data['word_replace'] = $wordReplace;
		return $this;
	}
	
	/**
	 * 设置词语来源
	 *
	 * @param string $isCustom
	 * @return PwWordFilterDm
	 */
	public function setWordFrom($from) {
		$this->_data['word_from'] = $from;
		return $this;
	}
	
	protected function _beforeAdd() {
		$this->_data['created_time'] = Pw::getTime();
		return $this->_check();
	}

	protected function _beforeUpdate() {
		return true;
	}
	
	/**
	 * 验证数据
	 * 
	 * @return TRUE OR PwError
	 */
	private function _check() {
		if (empty($this->_data['word'])) {
			return new PwError('WORD:word.empty');
		}
	
		return true;
	}
}