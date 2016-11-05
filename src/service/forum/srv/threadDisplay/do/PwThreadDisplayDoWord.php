<?php
defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('SRV:forum.srv.threadDisplay.do.PwThreadDisplayDoBase');

class PwThreadDisplayDoWord extends PwThreadDisplayDoBase {
	
	public function bulidRead($read) {
		$wordFilter = Wekit::load('SRV:word.srv.PwWordFilter');
		$content = $read['subject']. '<wind>' .$read['content'];
		$content = $wordFilter->replaceWord($content, $read['word_version']);
		if ($content === false) return $read;
		list($read['subject'], $read['content']) = explode('<wind>', $content);
		return $read;
	}
}