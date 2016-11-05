<?php
Wind::import('APPCENTER:service.srv.helper.PwApplicationHelper');
/**
 * 系统升级帮助类
 *
 * @author Shi Long <long.shi@alibaba-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwSystemHelper.php 24585 2013-02-01 04:02:37Z jieyin $
 * @package wind
 */
class PwSystemHelper {

	/**
	 * 解析sql语句，并返回解析后的结果
	 *
	 * @param string $strSQL        	
	 * @param string $charset        	
	 * @param string $dbprefix        	
	 * @return array($sqlStatement,$sqlOptions)
	 */
	static public function sqlParser($strSQL, $charset, $dbprefix, $engine) {
		if (empty($strSQL)) return array();
		
		$dataSQL = array();
		$strSQL = str_replace(array("\r", "\n", "\r\n"), "\n", $strSQL);
		$arrSQL = explode("\n", $strSQL);
		$query = '';
		$i = $alter = 0;
		foreach ($arrSQL as $value) {
			$value = trim($value, " \t");
			if (!$value || substr($value, 0, 2) === '--') continue;
			$query .= $value;
			if (substr($query, -1) != ';') continue;
			$sql_key = strtoupper(substr($query, 0, strpos($query, ' ')));
			$query = preg_replace('/([ `]+)pw_/', '$1' . $dbprefix, $query);
			if ($sql_key == 'CREATE') {
				$query = preg_replace(
					array('/CREATE\s+TABLE(\s+IF\s+NOT\s+EXISTS)?/i', '/\)([\w\s=\x7f-\xff\']*);/i'), 
					array(
						'CREATE TABLE IF NOT EXISTS', 
						')ENGINE=' . $engine . ' DEFAULT CHARSET=' . $charset), $query);
				$dataSQL[$i][] = trim($query, ';');
				$alter = 0;
			} else if ($sql_key == 'DROP') {
				$dataSQL[$i][] = trim($query, ';');
				$alter = 0;
			} else if ($sql_key == 'ALTER') {
				$alter || ++$i;
				$dataSQL[$i][] = trim($query, ';');
				++$i;
				$alter = 1;
			} elseif (in_array($sql_key, array('INSERT', 'REPLACE', 'UPDATE', 'DELETE'))) {
				$dataSQL[$i][] = trim($query, ';');
				$alter = 0;
			}
			$query = '';
		}
		return $dataSQL;
	}

	public static function alterIndex($value, $pdo) {
		$unique = 0;
		if ($value[3] == 'PRIMARY') {
			$add = $drop = 'PRIMARY KEY';
		} elseif ($value[3] == 'UNIQUE') {
			$add = "UNIQUE $value[1]";
			$drop = "INDEX $value[1]";
		} else {
			$add = $drop = "INDEX $value[1]";
			$unique = 1;
		}
		$indexkey = array();
		foreach ($pdo->query("SHOW KEYS FROM $value[0]")->fetchAll() as $rt) {
			$indexkey[$rt['Key_name']][$rt['Column_name']] = $unique;
		}
		if ($indexkey[$value[1]]) {
			if ($value[2]) {
				$ifdo = false;
				$column = explode(',', $value[2]);
				if (count($indexkey[$value[1]]) != count($column)) {
					$ifdo = true;
				} else {
					foreach ($column as $v) {
						if (!$indexkey[$value[1]][$v]) {
							$ifdo = true;
							break;
						}
					}
				}
				$ifdo && $pdo->execute("ALTER TABLE $value[0] DROP $drop,ADD $add ($value[2])");
			} elseif (empty($value[4]) || isset($indexkey[$value[1]][$value[4]])) {
				$pdo->execute("ALTER TABLE $value[0] DROP $drop");
			}
		} elseif ($value[2]) {
			$pdo->execute("ALTER TABLE $value[0] ADD $add ($value[2])");
		}
	}

	/**
	 * 解析md5sum文件
	 *
	 * @param unknown_type $md5sum        	
	 * @return multitype:multitype:
	 */
	public static function resolveMd5($md5sum) {
		$md5List = array();
		foreach (explode("\n", $md5sum) as $v) {
			list($_k, $_v) = explode("\t", $v);
			if ($_k && $_v) {
				$md5List[$_v] = $_k;
			}
		}
		return $md5List;
	}

	public static function md5content($md5, $file) {
		return $md5 . "\t" . trim(str_replace(DIRECTORY_SEPARATOR, '/', $file), '/') . "\n";
	}

	/**
	 * 计算sourcepath相对于targetpath的相对路径值
	 *
	 * @param unknown_type $sourcePath        	
	 * @param unknown_type $targetPath        	
	 * @return string
	 */
	public static function resolveRelativePath($sourcePath, $targetPath) {
		list($sourcePath, $targetPath) = array(realpath($sourcePath), realpath($targetPath));
		$src_paths = explode(DIRECTORY_SEPARATOR, $sourcePath);
		$tgt_paths = explode(DIRECTORY_SEPARATOR, $targetPath);
		$src_count = count($src_paths);
		$tgt_count = count($tgt_paths);
		
		$relative_path = '';
		// 默认把不同点设在最后一个
		$break_point = $src_count;
		$i = 0;
		// 计算两个路径不相同的点，然后开始往上数..
		for ($i = 0; $i < $src_count; $i++) {
			if ($src_paths[$i] == $tgt_paths[$i]) continue;
			$relative_path .= '../';
			$break_point == $src_count && $break_point = $i;
		}
		$relative_path || $relative_path = './';
		
		// 往上..后，继续算目标路径的接下来的path
		for ($i = $break_point; $i < $tgt_count; $i++) {
			$relative_path .= $tgt_paths[$i] . '/';
		}
		return rtrim($relative_path, '/');
	}

	public static function alterField($value, $pdo) {
		// 检查表是否存在，以兼容论坛独立表某些表不存在的情况
		$ckTableIfExists = $pdo->query("SHOW TABLES LIKE '$value[0]'")->fetch();
		if (empty($ckTableIfExists)) continue;
		$rt = $pdo->query("SHOW COLUMNS FROM $value[0] LIKE '$value[1]'")->fetch();
		$lowersql = strtolower($value[2]);
		if ((strpos($lowersql, ' add ') !== false && $rt['Field'] != $value[1]) || (str_replace(
			array(' drop ', ' change '), '', $lowersql) != $lowersql && $rt['Field'] == $value[1])) {
			$pdo->execute($value[2]);
		}
	}

	/**
	 * 使用socket下载
	 *
	 * @param unknown_type $url
	 * @param unknown_type $file
	 * @return multitype:boolean unknown 
	 */
	public static function downloadUseSocket($url, $file) {
		Wind::import('WIND:http.transfer.WindHttpSocket');
		$http = new WindHttpSocket($url);
		WindFolder::mkRecur(dirname($file));
		$data = $http->send();
		WindFile::write($file, $data);
		$http->close();
		return array(true, $file);
	}

	/**
	 * 下载
	 *
	 * @param unknown_type $url
	 * @param unknown_type $file
	 * @param unknown_type $useSocket
	 * @return Ambigous <multitype:boolean, multitype:boolean unknown_type >|multitype:boolean string |multitype:boolean unknown 
	 */
	public static function download($url, $file, $useSocket = false) {
		if ($useSocket) return self::downloadUseSocket($url, $file);
		Wind::import('WIND:http.transfer.WindHttpCurl');
		$http = new WindHttpCurl($url);
		WindFolder::mkRecur(dirname($file));
		$fp = fopen($file, "w");
		$opt = array(
			CURLOPT_FILE => $fp,
			CURLOPT_HEADER => 0,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_SSL_VERIFYHOST => false);
		$http->send('GET', $opt);
		if ($e = $http->getError()) return array(false, $e);
		$http->close();
		fclose($fp);
		return array(true, $file);
	}

	/**
	 * 根据升级列表校对md5
	 *
	 * 返回有更改的/无更改的/新增的
	 */
	public static function validateMd5($fileList) {
		$change = $unchange = $new = array();
		foreach ($fileList as $f => $hash) {
			$file = ROOT_PATH . $f;
			if (!file_exists($file) || !$hash) {
				$new[] = $f;
				continue;
			}
			if (md5_file($file) != $hash)
				$change[] = $f;
			else
				$unchange[] = $f;
		}
		return array($change, $unchange, $new);
	}

	/**
	 * 解压压缩包,将源文件解压至目标文件
	 * 目前只支持zip文件的解压，返回解后包文件绝对路径地址
	 *
	 * @param string $source        	
	 * @param string $target        	
	 * @return string
	 */
	static public function extract($source, $target) {
		Wind::import('APPCENTER:service.srv.helper.PwExtractZip');
		$zip = new PwExtractZip();
		if (!$data = $zip->extract($source)) return false;
		foreach ($data as $value) {
			$filename = $target . '/' . $value['filename'];
			WindFolder::mkRecur(dirname($filename));
			WindFile::write($filename, $value['data']);
		}
		return true;
	}

	/**
	 * 检查升级文件目录可写
	 *
	 * @param unknown_type $fileList        	
	 * @return multitype:boolean unknown |boolean
	 */
	public static function checkFolder($fileList) {
		foreach ($fileList as $v => $hash) {
			$file = ROOT_PATH . $v;
			if (!self::checkWriteAble(file_exists($file) ? $file : dirname($file) . '/')) return array(
				false, 
				$v);
		}
		return true;
	}

	public static function log($msg, $version, $start = false) {
		static $log;
		if (!$log) {
			$log = Wind::getRealDir('DATA:upgrade.log', true) . '/' . $version . '.log';
			WindFolder::mkRecur(dirname($log));
		}
		$status = $start ? WindFile::READWRITE : WindFile::APPEND_WRITEREAD;
		WindFile::write($log, "\r\n" . date('Y-m-d H:i') . '   ' . $msg, $status);
	}

	/**
	 * 检查目录可写
	 *
	 * @param string $pathfile        	
	 * @return boolean
	 */
	public static function checkWriteAble($pathfile) {
		if (!$pathfile) return false;
		$isDir = substr($pathfile, -1) == '/' ? true : false;
		if ($isDir) {
			if (is_dir($pathfile)) {
				mt_srand((double) microtime() * 1000000);
				$pathfile = $pathfile . 'pw_' . uniqid(mt_rand()) . '.tmp';
			} else {
				return self::checkWriteAble(dirname($pathfile) . '/');
			}
		}
		$exist = file_exists($pathfile);
		@chmod($pathfile, 0777);
		$fp = @fopen($pathfile, 'ab');
		if ($fp === false) return false;
		fclose($fp);
		$exist || @unlink($pathfile);
		return true;
	}

	public static function relative($relativePath) {
		$pattern = '/\w+\/\.\.\/?/';
		$pattern = '/\w+' . preg_quote(DIRECTORY_SEPARATOR, '/') . '\.\.' . preg_quote(
			DIRECTORY_SEPARATOR, '/') . '?/';
		while (preg_match($pattern, $relativePath)) {
			$relativePath = preg_replace($pattern, '', $relativePath);
		}
		return $relativePath;
	}

	public static function replaceStr($str, $search, $replace, $count, $nums) {
		$strarr = explode($search, $str);
		$replacestr = '';
		foreach ($strarr as $key => $value) {
			if ($key == $count) {
				$replacestr .= $value;
			} else {
				if (in_array(($key + 1), $nums)) {
					$replacestr .= $value . $replace;
				} else {
					$replacestr .= $value . $search;
				}
			}
		}
		return $replacestr;
	}
}

?>