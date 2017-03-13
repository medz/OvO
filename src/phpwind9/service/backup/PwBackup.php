<?php

/**
 * 数据库备份Ds.
 *
 * @author jinlong.panjl <jinlong.panjl@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id$
 */
class PwBackup
{
    /**
     * 获取一个表的总行数.
     *
     * @param $table
     *
     * @return table status string
     */
    public function getTableStatus($table)
    {
        if (!$table) {
            return 0;
        }

        return $this->_getBackupDao()->getTableStatus($table);
    }

    /**
     * 获取create table 信息.
     *
     * @param $table
     *
     * @return create table string
     */
    public function getCreateTable($table)
    {
        if (!$table) {
            return [];
        }

        return $this->_getBackupDao()->getCreateTable($table);
    }

    /**
     * 获取数据.
     *
     * @param $table
     * @param int $start
     * @param int $limit
     *
     * @return table status string
     */
    public function getData($table, $limit, $start)
    {
        if (!$table) {
            return [];
        }

        return $this->_getBackupDao()->getData($table, $limit, $start);
    }

    /**
     * 获取表的字段数.
     *
     * @param $table
     *
     * @return int
     */
    public function getColumnCount($table)
    {
        if (!$table) {
            return 0;
        }

        return $this->_getBackupDao()->getColumnCount($table);
    }

    /**
     * 获取表前缀
     *
     * @return string
     */
    public function getTablePrefix()
    {
        return $this->_getBackupDao()->getTablePrefix();
    }

    /**
     * 获取所有表.
     *
     * @return tables
     */
    public function getTables()
    {
        $tables = $this->_getBackupDao()->getTables();
        $prefix = $this->getTablePrefix();
        $prefixLen = strlen($prefix);
        $tableArray = [];
        foreach ($tables as $v) {
            $name = array_values($v);
            if (!$name[0]) {
                continue;
            }
            if (substr($name[0], 0, $prefixLen) != $prefix) {
                continue;
            }
            $tableStatus = $this->getTableStatus($name[0]);
            $tmp['name'] = $name[0];
            $tmp['Comment'] = $tableStatus['Comment'];
            $tableArray[$name[0]] = $tmp;
        }

        return $tableArray;
    }

    /**
     * 优化表.
     *
     * @param array $tables
     *
     * @return bool
     */
    public function optimizeTables($tables)
    {
        $table = $this->_buildTables($tables);
        if (!$table) {
            return false;
        }
        $this->_getBackupDao()->optimizeTables($table);

        return true;
    }

    /**
     * 修复表.
     *
     * @param array $tables
     *
     * @return bool
     */
    public function repairTables($tables)
    {
        $table = $this->_buildTables($tables);
        if (!$table) {
            return false;
        }
        $this->_getBackupDao()->repairTables($table);

        return true;
    }

    /**
     * 执行Sql.
     *
     * @return tables
     */
    public function executeQuery($query)
    {
        return $this->_getBackupDao()->executeQuery($query);
    }

    /**
     * 组装可执行的table.
     *
     * @param array $tables
     *
     * @return tables table1,table2,table3
     */
    private function _buildTables($tables)
    {
        if (!$tables) {
            return false;
        }
        !is_array($tables) && $tables = [$tables];
        $tables = array_unique($tables);
        $table = '';
        foreach ($tables as $v) {
            $v = WindSecurity::escapeHTML($v);
            $v && $table .= `$v`;
        }

        return $table ? $table : false;
    }

    /**
     * PwBackupDao.
     *
     * @return PwBackupDao
     */
    private function _getBackupDao()
    {
        return Wekit::loadDao('backup.dao.PwBackupDao');
    }
}
