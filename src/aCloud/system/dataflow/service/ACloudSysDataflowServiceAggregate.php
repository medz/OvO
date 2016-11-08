<?php

! defined('ACLOUD_PATH') && exit('Forbidden');
require_once Wind::getRealPath('ACLOUD_VER:dataflow.ACloudVerDataFlowAggregate');
class ACloudSysDataFlowServiceAggregate
{
    private $service = array();

    public function __construct()
    {
        if (! isset($this->service ['parse']) || ! $this->service ['parse']) {
            $this->service ['parse'] = new Aggregate_SQLParseExtension();
        }
        if (! isset($this->service ['operate']) || ! $this->service ['operate']) {
            $this->service ['operate'] = new Aggregate_SQLLogExtension();
        }
    }

    public function collectSQL($sql, $params)
    {
        list($bool, $operate, $tableName, $fields) = $this->service ['parse']->parseSQL($sql);
        if (! $bool) {
            return false;
        }

        return $this->service ['operate']->operate($operate, $tableName, $sql, $fields, $params);
    }
}

class Aggregate_SQLParseExtension
{
    private $prefix = '';

    public function __construct()
    {
        $this->prefix = ACloudSysCoreCommon::getGlobal(ACloudSysCoreDefine::ACLOUD_OBJECT_DAO)->getDB()->getTablePrefix();
    }

    public function parseSQL($sql)
    {
        list($sql, $info) = array(trim($sql), array());
        if (! $sql) {
            return array(false, '', '', $info);
        }
        list($bool, $operate, $tableName) = $this->matchOperateAndTableName($sql);
        if (! $bool) {
            return array(false, '', '', $info);
        }
        if (ACloudSysCoreS::inArray($operate, array('insert', 'replace'))) {
            $dao = ACloudSysCoreCommon::getGlobal(ACloudSysCoreDefine::ACLOUD_OBJECT_DAO);
            $insertId = $dao->insert_id();
            $info = array('insertid' => $insertId);
        }

        return array(true, $operate, $tableName, $info);
    }

    private function matchOperateAndTableName($sql)
    {
        preg_match('/^(DELETE|INSERT|REPLACE)\s+(.+?\s)?`?'.$this->prefix.'(\w+)`?\s+/i', $sql, $match);
        if (! $match) {
            return array(false, false, false);
        }
        list(, $operate, , $tableName) = $match;
        list($operate, $tableName) = array(strtolower($operate), strtolower($tableName));
        if (! ACloudSysCoreS::inArray($tableName, $this->getTables())) {
            return array(false, false, false);
        }

        return array(true, $operate, $tableName);
    }

    private function getTables()
    {
        return ACloudVerDataFlowAggregate::getMonitorTables();
    }
}

class Aggregate_SQLLogExtension
{
    private $params = array();

    public function operate($operate, $tableName, $sql, $fields, $params)
    {
        return ($operate == 'delete') ? $this->operateDeleteLog($sql, $params) : $this->operateAddLog($tableName, $fields);
    }

    private function operateDeleteLog($sql, $params)
    {
        $this->setParams($params);
        $sign = ACloudSysCoreCommon::getSiteSign();
        setcookie('_ac_'.$sign, intval(ACloudVerDataFlowAggregate::getDeleteSig()), time() + 60);
        $sqlLogService = ACloudSysCoreCommon::loadSystemClass('sql.log', 'config.service');
        $fields = array('log' => $this->buildSql($sql));

        return $sqlLogService->addSqlLog($fields);
    }

    private function operateAddLog($tableName, $fields)
    {
        list($type, $insertId) = array(ACloudVerDataFlowAggregate::getTypeByTableName($tableName), $fields ['insertid']);
        if (is_null($type) || ! $insertId) {
            return false;
        }
        $sign = ACloudSysCoreCommon::getSiteSign();

        return setcookie('_ac_'.$sign, intval($type), time() + 3600);
    }

    private function buildSql($sql)
    {
        if (! ACloudSysCoreS::isArray($this->getParams())) {
            return $sql;
        }

        return preg_replace_callback('/\?/', array($this, 'replaceSqlParams'), $sql);
    }

    private function replaceSqlParams($matches)
    {
        $params = $this->getParams();
        $replace = array_shift($params);
        $this->setParams($params);

        return $replace;
    }

    private function setParams($params)
    {
        $this->params = $params;
    }

    private function getParams()
    {
        return $this->params;
    }
}
