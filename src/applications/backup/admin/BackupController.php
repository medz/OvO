<?php
Wind::import('ADMIN:library.AdminBaseController');

/**
 * 数据库
 *
 * @author jinlong.panjl <jinlong.panjl@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id$
 * @package wind
 */
class BackupController extends AdminBaseController {
	
	private $_bakupDir;
	
	public function beforeAction($handlerAdapter) {
		parent::beforeAction($handlerAdapter);
		$this->_bakupDir = $this->_getBackupService()->getSavePath();
	}
	
	/* (non-PHPdoc)
	 * @see WindController::run()
	 */
	public function run() {
		$system = $this->getInput('system', 'get');
		$tables = $this->_getBackupDs()->getTables();
		if ($system) {
			$tables = $this->_buildTables($tables);
		}
		$tables && $count = count($tables);
		$this->setOutput($count,'count');
		$this->setOutput($tables,'tables');
	}
	
	/**
	 * 数据库还原列表
	 * 
	 * @return void
	 */
	public function restoreAction(){
		$files = WindFolder::read($this->_bakupDir);
		$filedb = array();
        foreach ($files as $v) {
			if (preg_match('/^\w{8}_pw_([^_]+)_(\d{14})/i', $v, $match)) {
				$bk['name'] = $v;
				$bk['version'] = str_replace('-', '.', $match[1]);
				$time = explode(',',wordwrap($match[2],2,',',true));
				$bk['time'] = $time[0] . $time[1] . '-' . $time[2] . '-' . $time[3] . ' ' . $time[4] . ':' . $time[5];
				$bk['dir'] = $bk['name'];
				$bk['num'] = '-';
				$bk['type'] = '目录';
				$bk['isdir'] = 1;
				$filedb[] = $bk;
			}
		}
		$filedb = array_reverse($filedb);
		$this->setOutput($filedb,'filedb');
	}
	
	/**
	 * 数据库还原子目录列表
	 * 
	 * @return void
	 */
	public function subcatAction(){
		$name = $this->getInput('name');
		$name = WindSecurity::escapePath($name);
		!$name && $this->showError('BACKUP:name.empty');
		$files = WindFolder::read(WindSecurity::escapePath($this->_bakupDir.$name));
		$filedb = array();
		foreach ($files as $v) {
			if (preg_match('/^(pw_([^_]+)_(\d{14})_[a-zA-Z0-9]{5})_(\d+)\.(sql|zip)$/i', $v, $match)) {
				$bk['name'] = $v;
				$bk['version'] = str_replace('-', '.', $match[2]);
				$time = explode(',',wordwrap($match[3],2,',',true));
				$bk['time'] = $time[0] . $time[1] . '-' . $time[2] . '-' . $time[3] . ' ' . $time[4] . ':' . $time[5];
				$bk['dir'] = $match[1];
				$bk['num'] = $match[4];
				$bk['type'] = $match[5] == 'sql' ? '备份文件' : '压缩文件';
				$bk['nosub'] = 1;
				$parentFile = $match[1];
				$tmpType = $match[5];
				$filedb[] = $bk;
			}
		}
		$tableStructure = $filedb[0];
		$tableStructure['name'] = 'table.' . $tmpType;
		$tableStructure['file'] = $parentFile.'/'.$tableStructure['name'];
		if (file_exists($this->_bakupDir.$tableStructure['pre'])) {
			$tableStructure['num'] = '-';
			$tableStructure['type'] = '数据表结构备份';
			$filedb[] = $tableStructure;
		}
		$this->setOutput($filedb,'filedb');
		$this->setOutput(1,'subcat');
		$this->setTemplate('backup_restore');
	}
	
	/**
	 * 批量删除备份
	 * 
	 * @return void
	 */
	public function batchdeleteAction() {
		$files = $this->getInput('files');
		!$files && $this->showError('BACKUP:name.empty');
		foreach($files as $value){
			$value = WindSecurity::escapePath($value);
			if (!$value) continue;
			if(preg_match('/^(\w{8}_pw_[^_]+_\d{14})(.*)(sql|zip)$/i', $value)){
				$deletePath = $this->_bakupDir . $value;
				WindFile::del($deletePath);
            }elseif (preg_match('/^\w{8}_pw_([^_]+)_(\d{14})/i', $value)) {
				WindFolder::rm($this->_bakupDir . $value,true);
			}
		}
		$this->showMessage('success');
	}
	
	/**
	 * 备份
	 * 
	 * @return void
	 */
	public function dobackAction(){
		$siteState = Wekit::C('site', 'visit.state');
		if ($siteState != 2) {
			$this->showError('BACKUP:site.isopen');
		}
		@set_time_limit(500);
		list($sizelimit, $compress, $start, $tableid, $step, $dirname) = $this->getInput(array('sizelimit', 'compress', 'start', 'tableid', 'step', 'dirname'));
		list($tabledb, $insertmethod, $tabledbname) = $this->getInput(array('tabledb', 'insertmethod', 'tabledbname'));
		
		$backupService = $this->_getBackupService();
		$tabledbTmpSaveDir = $backupService->getDataDir() . 'tmp/';
		$backupService->createFolder($tabledbTmpSaveDir);
		
		$tableid = intval($tableid);
		$tableid = $tableid ? $tableid : 0;
		$insertmethod = $insertmethod == 'extend' ? 'extend' : 'common';
		$sizelimit = $sizelimit ? $sizelimit : 2048;
		((!is_array($tabledb) || !$tabledb) && !$step) && $this->showError('BACKUP:name.empty');
		// 读取保存的需要操作的表
		
		if (!$tabledb && $step) {
			$cachedTable = WindFile::read(WindSecurity::escapePath($tabledbTmpSaveDir . $tabledbname . '.tmp'));
			$tabledb = explode("|", $cachedTable);
		}
		
		!$dirname && $dirname = $backupService->getDirectoryName();
		// 第一次临时保存需要操作的表
		if (!$step) {
			$specialTables = array_intersect($backupService->getSpecialTables(), $tabledb);
			$tabledb = array_values(array_diff($tabledb, $backupService->getSpecialTables()));
			if ($tabledb) {
				$backupService->backupTable($tabledb, $dirname, $compress);
				$tabledbname = 'cached_table_buckup';
				WindFile::write(WindSecurity::escapePath($tabledbTmpSaveDir . $tabledbname . '.tmp'), implode("|", $tabledb), 'wb');
			}
			// 备份数据表结构
			// 备份特殊表结构和数据
			if ($specialTables) {
				$backupService->backupSpecialTable($specialTables, $dirname, $compress, $insertmethod);
				$referer = 'admin/backup/backup/doback?'."start=0&tableid=$tableid&sizelimit=$sizelimit&step=1&insertmethod=$insertmethod&compress=$compress&tabledbname=$tabledbname&dirname=$dirname";
				$this->showMessage('正在备份',$referer,true);
			}
		}
		if (!$tabledb) {
			$this->showMessage(array('BACKUP:bakup_success', array(
				'{path}'=>$backupService->getSavePath(). $dirname,
			)),'admin/backup/backup/run');
		}
		// 保存数据
		$step = (!$step ? 1 : $step) + 1;
		$filename = $dirname . '/' . $dirname . '_' . ($step - 1) . '.sql';
		list($backupData, $tableid, $start)  = $backupService->backupData($tabledb, $tableid, $start, $sizelimit, $insertmethod, $filename);
		
		$continue = $tableid < count($tabledb) ? true : false;
		$backupService->saveData($filename, $backupData, $compress);
		// 循环执行
		if ($continue) {
			$currentTableName = $tabledb[$tableid];
			$currentPos = $start + 1;
			$createdFileNum = $step - 1;
			$referer = 'admin/backup/backup/doback?'."start=$start&tableid=$tableid&sizelimit=$sizelimit&step=$step&insertmethod=$insertmethod&compress=$compress&tabledbname=$tabledbname&dirname=$dirname";
			$this->showMessage(array('BACKUP:bakup_step', array(
				'{currentTableName}'=>$currentTableName,
				'{currentPos}'=>$currentPos,
				'{createdFileNum}'=>$createdFileNum,
			)),$referer,true);
		} else {
		
			unlink(WindSecurity::escapePath($tabledbTmpSaveDir . $tabledbname . '.tmp'));
			$this->showMessage(array('BACKUP:bakup_success', array(
				'{path}'=>$backupService->getSavePath(). $dirname,
			)),'admin/backup/backup/run');
		}
	}
	
	/**
	 * 简单优化数据表
	 * 
	 * @return void
	 */
	public function optimizeAction() {
		$tabledb = $this->getInput('tabledb');
		!$tabledb && $this->showError('BACKUP:table.empty');
		//关闭站点
		$config = new PwConfigSet('site');
		$siteState = Wekit::C()->getValues('site');
		$config->set('visit.state', 2)->flush();
		$this->_getBackupDs()->optimizeTables($tabledb);
		//还原站点
		$config = new PwConfigSet('site');
		$config->set('visit.state', $siteState['visit.state'])->flush();
		$this->showMessage('success');
	}
	
	/**
	 * 简单修复数据表
	 * 
	 * @return void
	 */
	public function repairAction() {
		$tabledb = $this->getInput('tabledb');
		!$tabledb && $this->showError('BACKUP:table.empty');
		//关闭站点
		$config = new PwConfigSet('site');
		$siteState = Wekit::C()->getValues('site');
		$config->set('visit.state', 2)->flush();
		$this->_getBackupDs()->repairTables($tabledb);
		//还原站点
		$config = new PwConfigSet('site');
		$config->set('visit.state', $siteState['visit.state'])->flush();
		$this->showMessage('success');
	}
	
	/**
	 * 导入数据表
	 * 
	 * @return void
	 */
	public function importAction() {
		list($file,$dir) = $this->getInput(array('file','dir'));
		list($step,$count,$isdir) = $this->getInput(array('step','count','isdir'));
		!$dir && $this->showError('BACKUP:name.empty');
		$siteState = Wekit::C('site', 'visit.state');
		if ($siteState != 2) {
			$this->showError('BACKUP:site.isopen');
		}
		if (!$count) {
			$count = 1;
			$files = WindFolder::read(WindSecurity::escapePath($this->_bakupDir.$dir));
			foreach ($files as $v) {
				if(preg_match("/^$dir\_\d+\.(sql|zip)$/i", $v)) $count++;
			}
		}
		!$step && $step = 1;
		!$isdir ? $this->_getBackupService()->bakinSql($this->_bakupDir.$dir.'/'.$file) : $this->_getBackupService()->backinData($dir, $step);
		
		$i = $step;
		$step++;
		if($count > 1 && $step <= $count){
			$referer = 'admin/backup/backup/import?'."step=$step&file=$file&dir=$dir&isdir=$isdir&count=$count";
			$this->showMessage(array('BACKUP:bakup_import', array(
				'{i}'=>$i
			)),$referer,true);
		}

		$this->showMessage('success','admin/backup/backup/restore');
	}
	
	/**
	 * 组装系统表白名单wind_structure.sql获取
	 * 
	 * @return void
	 */
	protected function _buildTables($tables) {
		if (!$tables) return array();
		$structure = WindFile::read(Wind::getRealPath('APPS:install.lang.wind_structure.sql', true));
		if (!$structure) return array();
		$tablePrefix = $this->_getBackupDs()->getTablePrefix();
		preg_match_all('/DROP TABLE IF EXISTS `pw_(\w+)`/', $structure, $matches);
		$tableNames = array_keys($tables);
		$whitleTables = array();
		foreach ($matches[1] as $v) {
			if (in_array($tablePrefix.$v, $tableNames)) {
				$whitleTables[$tablePrefix.$v] = $tables[$tablePrefix.$v];
			}
		}
		
		return $whitleTables;
	}
	
	/**
	 * PwBackupDs
	 * 
	 * @return PwBackup
	 */
	private function _getBackupDs(){
		return Wekit::load('backup.PwBackup');
	}
	
	/**
	 * PwBackupService
	 * 
	 * @return PwBackupService
	 */
	private function _getBackupService(){
		return Wekit::load('backup.srv.PwBackupService');
	}
}
