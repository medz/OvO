<?php
class AdminSearchLangParserService extends WindLangResource {
	
	/**
	 * 解析搜索的语言文件
	 * @return array 
	 */
	public function parseSearchLang() {
		$rs = array();
		$path = $this->_getPath();
		if (!$path || !file_exists($path)) return $rs;
		if (!$handle = fopen($path, 'rb')) return $rs;
		$currentMenu = $currentSubMenu = '';
		while (!feof($handle)) {
			$line = trim(fgets($handle, 8192));
			if (!$line) continue;
			if (preg_match('/^\[([a-z_]+)\]$/i', $line, $m)) {
				$currentMenu = $m[1];
				$currentSubMenu = '';
			} else {
				if (!$currentMenu) continue;
				if (preg_match('/^\[{2}(.+)\]{2}$/i', $line, $m)) {
					list($tmpName, $tmpUrl) = explode(':', $m[1]);
					$currentSubMenu = $tmpName;
					$rs[$currentMenu][$currentSubMenu] = array(
						'url' => WindUrlHelper::createUrl($tmpUrl),
					);
				} else {
					if ($currentSubMenu) {
						$rs[$currentMenu][$currentSubMenu]['items'][] = $line;
					} else {
						$rs[$currentMenu]['items'][] = $line;
					}
				}
			}
		}
		$handle && fclose($handle);
		return $rs;
	}
	
	/**
	 * 获得搜索文件包路径
	 * @return string
	 */
	private function _getPath() {
		$this->setConfig(Wind::getComponent('i18n')->getConfig());
		$path = $this->resolvedPath('ADMIN');
		if (is_file($path . '/search' . $this->suffix)) {
			$path = $path . '/search' . $this->suffix;
		} elseif (is_file($path . '/' . $this->default . $this->suffix)) {
			$path = $path . '/' . $this->default . $this->suffix;
		}
		return $path;
	}
}