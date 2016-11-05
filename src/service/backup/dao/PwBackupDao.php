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
class PwBackupDao extends PwBaseDao {
	
	/**
	 * 获取一个表的总行数
	 * 
	 * @param $table
	 * @return table status string
	 */
	public function getTableStatus($table) {
		$sql = $this->_bindTable("SHOW TABLE STATUS LIKE '%s'", $table);
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->getOne(array());
	}
	
	/**
	 * 获取数据
	 * 
	 * @param $table
	 * @param int $start
	 * @param int $limit 
	 * @return table status string
	 */
	public function getData($table,$limit,$start) {
		$sql = $this->_bindSql('SELECT * FROM `%s` %s ' , $table, $this->sqlLimit($limit,$start));
		$smt = $this->getConnection()->createStatement($sql);
		$result = $smt->queryAll(array(),'',PDO::FETCH_NUM);
		$temp = $array = array();
		foreach ($result as $k => $v) {
			foreach ($v as $kt => $vt) {
				$temp[$kt] = $this->getConnection()->quote($vt);
			}
			$array[$k] = $temp;
		}
		return $array;
	}
	
	/**
	 * 获取表的字段数
	 * 
	 * @param $table
	 * @return int
	 */
	public function getColumnCount($table) {
		$sql = $this->_bindSql('SELECT * FROM `%s` %s ', $table, $this->sqlLimit(1));
		$smt = $this->getConnection()->query($sql);
		return $smt->columnCount();
	}
	
	/**
	 * 获取create table 信息
	 * 
	 * @param $table
	 * @return create table string
	 */
	public function getCreateTable($table) {
		$sql = $this->_bindSql('SHOW CREATE TABLE `%s`', $table);
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->getOne(array());
	}
	
	/**
	 * 获取所有表
	 * 
	 * @return tables
	 */
	public function getTables() {
		$sql = $this->_bindTable('SHOW TABLES');
		$smt = $this->getConnection()->query($sql);
		return $smt->fetchAll();
	}
	
	/**
	 * 优化表
	 * 
	 * @param string $tables table1,table2,table3....
	 * @return tables
	 */
	public function optimizeTables($table) {
		$sql = $this->_bindSql('OPTIMIZE TABLE %s', $table);
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->execute(array());
	}
	
	/**
	 * 修复表
	 * 
	 * @param string $tables table1,table2,table3....
	 * @return tables
	 */
	public function repairTables($table) {
		$sql = $this->_bindSql('REPAIR TABLE %s EXTENDED', $table);
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->execute(array());
	}
	
	/**
	 * 执行Sql
	 * 
	 * @return tables
	 */
	public function executeQuery($query) {
		$this->getConnection()->query($query);
		return true;
	}
	
	/**
	 * 获取表前缀
	 * 
	 * @param $table
	 * @return create table string
	 */
	public function getTablePrefix() {
		return $this->getConnection()->getTablePrefix();
	}
}
?>