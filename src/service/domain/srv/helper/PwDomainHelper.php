<?php
/**
 * 帮助类
 *
 * @author Shi Long <long.shi@alibaba-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwDomainHelper.php 20723 2012-11-05 11:25:00Z long.shi $
 * @package domain.srv.helper
 */
class PwDomainHelper {

	/**
	 * 解析url，分成各块，分别为主机、是否https，脚本文件，路径
	 *
	 * @param string $url        	
	 * @return PwError array
	 */
	public static function parse_url($url) {
		$components = parse_url($url);
		$host = $script = $path = $scriptUrl = '';
		$isSecure = false;
		if ($components['scheme']) {
			$host = $components['scheme'] . '://' . $components['host'];
			$isSecure = $components['scheme'] === 'https';
			$components['port'] && $host .= ':' . $components['port'];
		} 
		if (strpos($components['path'], '.php') !== false) {
			if (($pos = strrpos($components['path'], '/')) === false) $pos = -1;
			$script = substr($components['path'], $pos + 1);
			$scriptUrl = substr($components['path'], 0, $pos);
			$path = $components['path'] . '?' . $components['query'];
		} else {
			$script = '';
			$path = str_replace($host, '', $url);
		}
		return array($host, $isSecure, $script, $path, $scriptUrl);
	}

	/**
	 * 判断是否是子域名
	 *
	 * 此方法不够严谨，如果遇到主域名正好就是域名后缀的时候，类似www.info.com,www.net.cn时有bug。
	 *
	 * @param string $domain1        	
	 * @param string $domain2        	
	 */
	public static function isMyBrother($domain1, $domain2) {
		if ($domain1 == $domain2) return true;
		if (WindFile::getSuffix($domain1) != WindFile::getSuffix($domain2)) return false;
		$domain1 = str_replace(array('http://'), 'https://', $domain1);
		$domain2 = str_replace(array('http://'), 'https://', $domain2);
		$suffix = array(
			'com', 
			'cn', 
			'name', 
			'org', 
			'net', 
			'edu', 
			'gov', 
			'info', 
			'pro', 
			'museum', 
			'coop', 
			'aero', 
			'xxx', 
			'idv', 
			'hk', 
			'tw', 
			'mo',
			'me',
			'biz');
		$preg = implode('|', $suffix);
		$domain1 = preg_replace("/(\.($preg))*\.($preg)$/iU", '', $domain1);
		$domain2 = preg_replace("/(\.($preg))*\.($preg)$/iU", '', $domain2);
		
		$r = explode('.', $domain1);
		$main1 = end($r);
		$r = explode('.', $domain2);
		$main2 = end($r);
		return $main1 == $main2;
	}
}

?>