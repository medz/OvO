<?php

/**
 * 数据库备份还原
 *
 * @author jinlong.panjl <jinlong.panjl@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id$
 * @package wind
 */
class PwBackupService {
	
	private $_backupTipLength;
	private $_backupTip;
	private $_version = NEXT_VERSION;
	
	public function __construct() {
		$this->_backupTip = $this->getBackupTip();
		$this->_backupTipLength = strlen($this->_backupTip);	
	}
	
	public function getSpecialTables() {
		// 特殊数据表
		$specialTables = array(
			'common_config',
		);
		$tablePrefix = $this->_getBackupDs()->getTablePrefix();
		foreach ($specialTables as $v) {
			$result[] = $tablePrefix.$v;
		}
		return $result;
	}
	
	/**
	 * 备份特殊表数据  - 小数据量，依赖程序，一个进程搞定的。
	 * 
	 * @param $tabledb
	 * @param $tableid
	 * @param $start
	 * @param $sizelimit
	 * @param $insertmethod
	 * @param $filename
	 * @return array
	 */
	public function backupSpecialTable($tabledb, $dirname, $isCompress, $insertmethod = 'common') {
		$tabledb = array_intersect($this->getSpecialTables(),$tabledb);
		list($dirname, $isCompress) = array($dirname, intval($isCompress));
		if (!$dirname || !is_array($tabledb) || !$tabledb) return false;
		$createSql = '';
		$method = (strtolower($insertmethod) == 'common') ? '_backupDataCommonMethod' : '_backupDataExtendMethod';
		foreach($tabledb as $k => $table){
			$createSql .= "DROP TABLE IF EXISTS `$table`;\n"; 
			$CreatTable = $this->_getBackupDs()->getCreateTable($table);
			$CreatTable['Create Table'] = str_replace($CreatTable['Table'], $table, $CreatTable['Create Table']);
			$createSql .= $CreatTable['Create Table'] . ";\n\n";
			list($backupData) = $this->$method($tabledb, $k);
			$createSql .= $backupData . "\n\n";
		}
		$this->saveData($dirname . '/specialTables.sql', $createSql, $isCompress);
		return true;
	}
	
	/**
	 * 备份表数据
	 * 
	 * @param $tabledb
	 * @param $tableid
	 * @param $start
	 * @param $sizelimit
	 * @param $insertmethod
	 * @param $filename
	 * @return array
	 */
	public function backupData($tabledb, $tableid, $start, $sizelimit, $insertmethod = 'common', $filename = '') {
		if (!is_array($tabledb) || !count($tabledb)) return array();
		$tableid = intval($tableid) ? intval($tableid) : 0;
		list($backupData, $totalRows, $tableSaveInfo) = array('', 0, array());
		$method = (strtolower($insertmethod) == 'common') ? '_backupDataCommonMethod' : '_backupDataExtendMethod';
		list($backupData, $tableid, $start, $tableSaveInfo) = $this->$method($tabledb, $tableid, $start, $sizelimit);
		$this->_recordTableSaveInfo($tableSaveInfo, $filename);
		
		return array($backupData, $tableid, $start);
	}
	
	/**
	 * 普通方式备份表数据
	 * 
	 * @param $tabledb
	 * @param $tableid
	 * @param $start
	 * @param $sizelimit
	 * @return array
	 */
	protected function _backupDataCommonMethod($tabledb, $tableid = 0, $start = 0, $sizelimit = 0) {
		list($writedRows, $backupData, $tableSaveInfo, $totalTableNum) = array(0, '', array(), count($tabledb));
		// 循环每个表
		while ($tableid < $totalTableNum) {
			if (!isset($tabledb[$tableid])) continue;
			$tmpWritedRows = $writedRows;
			$selectNum = 5000;
			$result = $this->_getBackupDs()->getData($tabledb[$tableid],$selectNum,$start);
			$fieldNum = $this->_getBackupDs()->getColumnCount($tabledb[$tableid]);
			$count = 0;
			// 循环组装$selectNum条数据
			foreach ($result as $v) {
				$tmpData = "INSERT INTO " . $tabledb[$tableid] . " VALUES(" . $v[0];
				$tmpData .= $this->_buildFieldsData($fieldNum, $v) . ");\n";
				if ($sizelimit && (($this->_backupTipLength + strlen($backupData) + strlen($tmpData) + 2) > $sizelimit * 1000)) {
					$tableSaveInfo[$tabledb[$tableid]] = array('start' => $tmpWritedRows, 'end' => -1);
					break 2;
				}
				$backupData .= $tmpData;
				$writedRows++;
				$start++;	
				$count++;
			}
			if ($count < $selectNum) {
				$start = 0;
				$tableid++;
			}
			$backupData .= "\n";
			$tableSaveInfo[$tabledb[$tableid]] = array('start' => $tmpWritedRows, 'end' => $writedRows++);
		}
		return array($backupData, $tableid, $start, $tableSaveInfo);
	}
	
	/**
	 * 扩展方式备份表数据
	 * 
	 * @param $tabledb
	 * @param $tableid
	 * @param $start
	 * @param $sizelimit
	 * @return array
	 */
	protected function _backupDataExtendMethod($tabledb, $tableid = 0, $start = 0, $sizelimit = 0) {
		list($writedRows, $backupData, $tableSaveInfo, $totalTableNum) = array(0, '', array(), count($tabledb));
		while ($tableid < $totalTableNum) {
			if (!isset($tabledb[$tableid])) continue;
			$tmpWritedRows = $writedRows;
			$selectNum = 5000;
			$result = $this->_getBackupDs()->getData($tabledb[$tableid], $selectNum, $start);
			$fieldNum = $this->_getBackupDs()->getColumnCount($tabledb[$tableid]);
			$count = 0;
			$outTmpData = '';
			$outFrontData = 'INSERT INTO ' . $tabledb[$tableid] . ' VALUES ';
			foreach ($result as $v) {
				$v = array_values($v);
				$tmpData = "(" . $v[0];
				$tmpData .= $this->_buildFieldsData($fieldNum, $v) . "),\n";
				if ($sizelimit && (($this->_backupTipLength + strlen($backupData) + strlen($tmpData) + strlen($outTmpData) + strlen($outFrontData) + 2) > $sizelimit * 1000)) {
					$outTmpData && $backupData .= $outFrontData . rtrim($outTmpData, ",\n") . ";\n";
					$tableSaveInfo[$tabledb[$tableid]] = array('start' => $tmpWritedRows, 'end' => -1);
					break 2;
				}
				$outTmpData .= $tmpData;
				$start++;
				$count++;
			}
			if ($outTmpData) {
				$backupData .= $outFrontData . rtrim($outTmpData, ",\n") . ";\n";
				$writedRows++;
			}
			if ($count < $selectNum) {
				$start = 0;
				$tableid++;
			}
			$backupData .= "\n";
			$tableSaveInfo[$tabledb[$tableid]] = array('start' => $tmpWritedRows, 'end' => $writedRows++);
		}
		return array($backupData, $tableid, $start, $tableSaveInfo);
	}
	
	/**
	 * 获取数据
	 * 
	 * @param $table
	 * @param $start
	 * @param $num
	 */
	public function _selectData($table, $start, $num) {
		list($start, $num) = array(intval($start), intval($num));
		return $this->_getBackupDs()->getData($table, $start, $num);
	}
	
	/**
	 * 组装每个字段的数据
	 * 
	 * @param $total
	 * @param $result
	 */
	public function _buildFieldsData($total, $result) {
		list($total, $data) = array(intval($total), '');
		if ($total < 2) return $data;
		for ($i = 1; $i < $total; $i++) {
			$data .= "," . $result[$i];
		}
		return $data;
	}
	
	/**
	 * 备份表结构
	 * 
	 * @param $tabledb
	 * @param $dirname
	 * @param $isCompress
	 * @return bool
	 */
	public function backupTable($tabledb, $dirname, $isCompress){
		list($dirname, $isCompress) = array($dirname, intval($isCompress));
		if (!$dirname || !is_array($tabledb) || !count($tabledb)) return false;
		$createSql = '';
		foreach($tabledb as $table){
			$createSql .= "DROP TABLE IF EXISTS `$table`;\n"; 
			$CreatTable = $this->_getBackupDs()->getCreateTable($table);
			$CreatTable['Create Table'] = str_replace($CreatTable['Table'], $table, $CreatTable['Create Table']);
			$createSql .= $CreatTable['Create Table'] . ";\n\n";
		}
		$this->saveData($dirname . '/table.sql', $createSql, $isCompress);
		return true;
	}
	
	/**
	 * 备份文件提示
	 * 
	 * @return string
	 */
	public function getBackupTip() {
		$tablePrefix = $this->_getBackupDs()->getTablePrefix();
		return "--\n-- phpwind SQL Dump\n-- version:" . $this->_version . "\n-- time: " . Pw::time2str(Pw::getTime(),'Y-m-d H:i') . "\n-- tablepre: $tablePrefix\n-- phpwind: http://www.phpwind.net\n-- --------------------------------------------------------\n\n\n";
	}
	
	/**
	 * 获取备份文件提示的行数
	 * 
	 * @return int
	 */
	public function getLinesOfBackupTip() {
		return substr_count($this->_backupTip, "\n");
	}
	
	/**
	 * 保存数据到文件
	 * 
	 * @param $filePath
	 * @param $data
	 * @param $isCompress
	 * @return bool
	 */
	public function saveData($filePath, $data, $isCompress = false) {
		if (!trim($data) || !$filePath) return false;
		$filePath = $this->getSavePath() . $filePath;
		$this->createFolder(dirname($filePath));
		$data = $this->_backupTip . $data;
		if ($isCompress && $this->_checkZlib()) {
			$zipService = $this->_getZipService();
			$filename = basename($filePath);
			$zipName = substr($filename,0,strrpos($filename, '.')) . '.zip';
			$filePath = dirname($filePath) . '/' . $zipName;
			$zipService->init();
			$zipService->addFile($data, $filename);
			$data = $zipService->getCompressedFile();
		}
		Wind::import('WIND:utility.WindFile');
		WindFile::write($filePath, $data);
		return true;
	}
	
	/**
	 * 记录表数据的保存文件跟位置
	 * 
	 * @param $tableSaveInfo
	 * @param $filename
	 * @return bool
	 */
	public function _recordTableSaveInfo($tableSaveInfo, $filename) {
		if (!$filename || !is_array($tableSaveInfo) || !count($tableSaveInfo)) return false;
		$filePath = $this->getSavePath() . dirname($filename);
		$filename = basename($filename);
		$this->createFolder($filePath);
		$linesOfBackupTip = $this->getLinesOfBackupTip();
		foreach ($tableSaveInfo as $key => $value) {
			$value['start'] += $linesOfBackupTip;
			$value['end'] != -1 && $value['end'] += $linesOfBackupTip;
			$record .= $key . ':' . $filename . ',' . $value['start'] . ',' . $value['end'] . "\n";
		}
		Wind::import('WIND:utility.WindFile');
		WindFile::write($filePath . '/table.index', $record, 'ab+');
		return true;
	}
	
	/**
	 * 备份文件保存目录
	 * 
	 * @return string
	 */
	public function getSavePath() {
		return  $this->getDataDir() . 'sqlbackup/';
	}
	
	/**
	 * 获取data目录
	 * 
	 * @return string
	 */
	public function getDataDir() {
		return Wind::getRealDir('DATA:');
	}
	
	/**
	 * 生成文件前缀
	 * 
	 * @return string
	 */
	public function getDirectoryName() {
		$version = str_replace('.', '-', $this->_version);
		//return 'pw_' . $version . '_' . Pw::time2str(Pw::getTime(), 'YmdHis') . '_' . WindUtility::generateRandStr(5);
        return WindUtility::generateRandStr(8).'_pw_' . $version . '_' . Pw::time2str(Pw::getTime(), 'YmdHis'); 
	}
	
	/**
	 * 创建文件夹
	 * 
	 * @return bool
	 */
 	public static function createFolder($path ='') {
		if ($path && !is_dir($path)) {
           self::createFolder(dirname($path));
           if (!@mkdir($path,0777)) {
				return false;
           }
        }
		return true;
	}	
	
	public function bakinSql($filename) {
		$data = WindFile::read($filename);
		$sql = explode("\n", $data);
		return $this->_doBackIn($sql);
	}

	/**
	 * 根据文件和步骤组装sql数组
	 * 
	 * @param string $dir
	 * @param int $step
	 * @return array
	 */
	public function backinData($dir, $step = 1) {
		if (!$dir || !$step) return false;
		$step = intval($step) - 1;
		$tmpname = $this->getSavePath() . $dir . '/';
		$extend = file_exists($tmpname . 'table.zip') ? 'zip' : 'sql';
		if (!$step) {
			$specialFile = $tmpname . 'specialTables.' . $extend ;
			$this->_backinFileData($specialFile,$extend);
			$filename = $tmpname . 'table.' . $extend ;
		} else {
			$filename = $tmpname . $dir . '_' . $step . '.' . $extend;
		}
		$this->_backinFileData($filename,$extend);
		return true;
	}
	
	private function _backinFileData($filename,$extend) {
		if ($extend == 'zip') {
			$zipService = Wekit::load('LIB:utility.PwZip');
			list($data) = $zipService->extract($filename);
			$sql = explode("\n", $data['data']);
		} else {
			$data = WindFile::read($filename);
			$sql = explode("\n", $data);
		}
		$this->_doBackIn($sql);	
		return true;
	}
	
	/**
	 * 执行导入sql
	 * 
	 * @param array $sql
	 * @return bool
	 */
	private function _doBackIn($sql) {
		
		if (!is_array($sql) || !count($sql)) return false;
		$tablepre = substr($sql[4], 0, 11) == '-- tablepre' ? trim(substr($sql[4], 12)) : '';
		$query = '';
		$num = 0;
		$charset = Wind::getApp()->getResponse()->getCharset();
		$charset = strtolower($charset) == 'utf-8' ? 'utf8' : $charset;
		$tablePrefix = $this->_getBackupDs()->getTablePrefix();
		foreach ($sql as $value) {
			$value = trim($value);
			if (!$value || Pw::substrs($value, 2, '', false) === '--') continue;
			if(preg_match("/;$/i", $value)){
				$query .= $value;
				if(preg_match("/^CREATE/i", $query)){
					$extra = substr(strrchr($query, ')'), 1);
					$tabtype = substr(strchr($extra, '='), 1);
					$tabtype = substr($tabtype, 0, strpos($tabtype, strpos($tabtype,' ') ? ' ' : ';'));
					$comment = strchr($extra, 'COMMENT=');
					if ($comment) {
						$comment = substr(strchr($comment, '='), 1);
						$comment = substr($comment, 0, strpos($comment, strpos($comment,';') ? ';' : ''));
					}
					$query = str_replace($extra, '', $query);
					$extra = $charset ? "ENGINE=$tabtype DEFAULT CHARSET=$charset" : "ENGINE=$tabtype";
					$extra = $comment ? "$extra COMMENT=$comment;" : "$extra;";
					$query .= $extra;
				} elseif (preg_match("/^INSERT/i", $query)){
					$query = 'REPLACE ' . substr($query, 6);
				}
				
				if ($tablepre && $tablepre != $tablePrefix) {
					$query = str_replace(array(" $tablepre", "`$tablepre", " '$tablepre"), array(" $tablePrefix", "`$tablePrefix", " '$tablePrefix"), $query);
				}
				
				$this->_getBackupDs()->executeQuery($query);
				$query = '';
			} else{
				$query .= $value;
			}
		}
		return true;
	}
	
	/**
	 * zlib扩展是否开启
	 * 
	 * @return bool
	 */
	private function _checkZlib() {
		return (extension_loaded('zlib') && function_exists('gzcompress')) ? true : false;
	}
	
	/**
	 * PwBackupDao
	 * 
	 * @return PwBackupDao
	 */
	private function _getZipService(){
		return Wekit::load('LIB:utility.PwZip');
	}
	
	/**
	 * PwBackup
	 * 
	 * @return PwBackup
	 */
	private function _getBackupDs(){
		return Wekit::load('backup.PwBackup');
	}
}
?>
