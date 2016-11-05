<?php
/**
 * xml 文件操作类
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwXML.php 13576 2012-07-10 04:22:29Z gao.wanggao $ 
 * @package 
 */
class PwXML {
	
	private $dom;
	
	public function __construct() {
		if (!class_exists('DOMDocument')) throw new WindException('DOMDocument is not exist.');
		$this->dom = new DOMDocument('1.0', 'utf-8');
	}

	/**
	 * 创建根节点
	 * Enter description here ...
	 * @param unknown_type $root
	 */
	public function createRoot($node) {
		$this->dom->appendChild($this->dom->createElement($node));
	}
	
	/**
	 * 添加子节点
	 * Enter description here ...
	 * @param unknown_type $node
	 */
	public function createChild($node, $parents) {
		$parents = $this->dom->appendChild($parents);
		$parents->createElement($node);
	}
	
	/**
	 * 添加值
	 * Enter description here ...
	 * @param unknown_type $node
	 * @param unknown_type $value
	 */
	public function createValue($node, $value) {
		$node = $this->dom->appendChild($node);
		$value = $this->dom->createTextNode($value);
		$node->appendChild($value);
	}
	
	public function arrayToXML($array) {
		
	}
	
}
?>