<?php
defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 词语过滤搜索条件
 *
 * @author Mingqu Luo <luo.mingqu@gmail.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwWordSo.php 8142 2012-04-16 09:24:55Z hejin $
 * @package wind
 */

class PwWordSo {
	
	protected $_data = array();
	
	/**
	 * 设置类型查询条件
	 *
	 * @param int $type
	 * @return PwWordSo
	 */
	public function setWordType($type) {
		$this->_data['word_type'] = $type;
		return $this;
	}
	
	/**
	 * 设置词语查询条件
	 *
	 * @param string $word
	 */
	public function setWord($word) {
		$this->_data['word'] = $word;
		return $this;
	}
	
	public function getData() {
		return $this->_data;
	}
}