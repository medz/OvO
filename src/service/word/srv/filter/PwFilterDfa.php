<?php
Wind::import('SRV:word.srv.filter.PwFilterAction');

/**
 * 敏感词过滤算法
 *
 * @author jinlong.panjl <jinlong.panjl@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id$
 * @package wind
 */
class PwFilterDfa extends PwFilterAction {

	public $nodes;
	
	public function __construct($nodes = '') {
		$nodes && $this->nodes = $nodes;
	}
	
	/**
	 * 生成敏感词字典
	 *
	 * @param array $words
	 * @return array
	 */
	public function createData($words) {
		$this->nodes = array( array(false, array()) ); //初始化，添加根节点
		$p = 1; //下一个要插入的节点号
		foreach ($words as $word) {
			$cur = 0; //当前节点号
			list($word, $type, $replace) = $this->split($word);
			for ($len = strlen($word), $i = 0; $i < $len; $i++) {
				$c = ord($word[$i]);

				if (isset($this->nodes[$cur][1][$c])) { //已存在就下移
					$cur = $this->nodes[$cur][1][$c];
					continue;
				}
				$this->nodes[$p]= array(false, array()); //创建新节点
				$this->nodes[$cur][1][$c] = $p; //在父节点记录子节点号
				$cur = $p; //把当前节点设为新插入的
				$p++; //
			}
			$this->nodes[$cur][0] = true; //一个词结束，标记叶子节点
			$this->nodes[$cur][2] = $type; //敏感词类型
			$this->nodes[$cur][3] = trim($replace);  //替换敏感词
		}
		return $this->nodes;
	}

	function split($str) {
		if (($pos = strrpos($str, '|')) === false) {
			return array($str, 0);
		}
		return explode('|',$str);
	}
	
	/**
	 * 保存敏感词字典
	 *
	 * @param array $nodes
	 */
	public function saveData($nodes) {
		WindFolder::mkRecur($this->file);
		WindFile::write($this->file.'/word.txt', serialize($nodes));
	}
	
	/**
	 * 检测敏感词 | 如果有敏感词直接返回true
	 *
	 * @param string $s
	 * @return bool
	 */
   public function check($s) {  //直接提示
   		$charset = Wekit::V('charset');
		$charset = str_replace('-', '', strtolower($charset));
        $isUTF8 = ($charset == 'utf8') ? true : false;
        $ret = array();
        $cur = 0; //当前节点，初始为根节点
        $i = 0; //字符串当前偏移
        $p = 0; //字符串回溯位置
        $len = strlen($s);
        while($i < $len) {
            $c = ord($s[$i]);
            if (isset($this->nodes[$cur][1][$c])) { //如果存在
                $cur = $this->nodes[$cur][1][$c]; //下移当前节点
                if ($this->nodes[$cur][0]) { //是叶子节点，单词匹配！
                    return true;
                }
				$i++; //下一个字符
            } else { //不匹配
				$cur = 0; //重置当前节点为根节点
                if (!$isUTF8 && ord($s[$p]) > 127 && ord($s[$p+1]) > 127) {
					$p += 2; //设置下一个回溯位置
				} else {
					$p += 1; //设置下一个回溯位置
				}
				$i = $p; //把当前偏移设为回溯位置
            }
        }
        return false;    
    }
	
	/**
	 * 检测敏感词 | 检测所有敏感词并返回
	 *
	 * @param string $s
	 * @return array
	 */
    public function match($s) {
   		$charset = Wekit::V('charset');
		$charset = str_replace('-', '', strtolower($charset));
        $isUTF8 = ($charset == 'utf8') ? true : false;
        $ret = array();
        $cur = 0; //当前节点，初始为根节点
        $i = 0; //字符串当前偏移
        $p = 0; //字符串回溯位置
        $len = strlen($s);
        $type = array();
        while($i < $len) {
            $c = ord($s[$i]);
            if (isset($this->nodes[$cur][1][$c])) { //如果存在
                $cur = $this->nodes[$cur][1][$c]; //下移当前节点
                if ($this->nodes[$cur][0]) { //是叶子节点，单词匹配！
                	$type[] = $this->nodes[$cur][2];
                    $ret[$p] = substr($s, $p, $i - $p + 1); //取出匹配位置和匹配的词以及词信息
                    $p = $i + 1; //设置下一个回溯位置
                    $cur = 0; //重置当前节点为根节点
                }
				$i++; //下一个字符
            } else { //不匹配
				$cur = 0; //重置当前节点为根节点
                if (!$isUTF8 && ord($s[$p]) > 127 && ord($s[$p+1]) > 127) {
					$p += 2; //设置下一个回溯位置
				} else {
					$p += 1; //设置下一个回溯位置
				}
				$i = $p; //把当前偏移设为回溯位置
            }
        }
        $type && $minType = min($type);
        return array($minType,$ret);    
    }

    /**
     * 替换敏感词
     * 
     * @param string $s  需要查找的文本
     * @return string $s 替换后的文本
     */
 	public function replace($s) {
   		$charset = Wekit::V('charset');
		$charset = str_replace('-', '', strtolower($charset));
        $isUTF8 = ($charset == 'utf8') ? true : false;
        $ret = array();
        $cur = 0; //当前节点，初始为根节点
        $i = 0; //字符串当前偏移
        $p = 0; //字符串回溯位置
        $len = strlen($s);
        while($i < $len) {
            $c = ord($s[$i]);
            if (isset($this->nodes[$cur][1][$c])) { //如果存在
                $cur = $this->nodes[$cur][1][$c]; //下移当前节点
                if ($this->nodes[$cur][0]) { //是叶子节点，单词匹配！
                    $s = substr_replace($s, $this->nodes[$cur][3], $p, $i - $p + 1); //取出匹配位置和匹配的词以及词的权重
                    $p = $i + 1; //设置下一个回溯位置
                    $cur = 0; //重置当前节点为根节点
                }
				$i++; //下一个字符
            } else { //不匹配
				$cur = 0; //重置当前节点为根节点
                if (!$isUTF8 && ord($s[$p]) > 127 && ord($s[$p+1]) > 127) {
					$p += 2; //设置下一个回溯位置
				} else {
					$p += 1; //设置下一个回溯位置
				}
				$i = $p; //把当前偏移设为回溯位置
            }
        }
        
        return $s;    
    }
}