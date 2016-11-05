<?php
defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('SRV:forum.srv.post.do.PwPostDoBase');
/**
 * 帖子回复 - 敏感词
 *
 * @author jinlong.panjl <jinlong.panjl@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id$
 * @package wind
 */
class PwReplyDoWord extends PwPostDoBase {
	protected $_isVerified = 0;
	protected $_confirm = 0;
	protected $_word = 0;
	
	public function __construct(PwPost $pwpost, $verifiedWord = 0) {
		$this->_confirm = $verifiedWord;
	}

	public function check($postDm) {
		$data = $postDm->getData();
		$content = Pw::substrs(Pw::stripWindCode($data['content']), 30) == $data['subject'] ? $data['content'] : $data['subject'].$data['content'];
		$banedStrLen = strlen($data['subject']);
		$wordFilter = Wekit::load('SRV:word.srv.PwWordFilter');

		list($type, $words, $isTip) = $wordFilter->filterWord($content);
		if (!$type) return true; 
		$words = array_unique($words);
		foreach ($words as $k => $v) {
			if ($k < $banedStrLen) {
				return new PwError('WORD:content.error.tip',array('{wordstr}' => implode(',', $words)));
			}
		}
		switch ($type) {
			case 1:
				return new PwError('WORD:content.error.tip',array('{wordstr}' => implode(',', $words)));
			case 2:
				$this->_isVerified = 1;
				if ($this->_confirm) {
					return true;
				}
			case 3:	
				$this->_word = 1;
			default:
				return true;
		}
		return true;
	}
	
	public function dataProcessing($postDm) {
		$word_version = $this->_word ? 0 : (int)Wekit::C('bbs', 'word_version');
		$this->_isVerified && $postDm->setDisabled(1);
		$postDm->setWordVersion($word_version);
		return $postDm;
	}
}