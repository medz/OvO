<?php

! defined('ACLOUD_PATH') && exit('Forbidden');
require_once Wind::getRealPath('ACLOUD:system.core.ACloudSysCoreDao');
class ACloudSysConfigDaoApis extends ACloudSysCoreDao
{
    private $tablename = '{{acloud_apis}}';

    public function insert($fields)
    {
        $sql = sprintf('REPLACE INTO %s %s', $this->tablename, $this->buildClause($fields));
        $this->query($sql);

        return $this->insert_id();
    }

    public function delete($name)
    {
        return $this->query(sprintf('DELETE FROM %s WHERE name = %s', $this->tablename, ACloudSysCoreS::sqlEscape($name)));
    }

    public function update($fields, $name)
    {
        $sql = sprintf('UPDATE %s %s WHERE name = %s', $this->tablename, $this->buildClause($fields), ACloudSysCoreS::sqlEscape($name));

        return $this->query($sql);
    }

    public function get($name)
    {
        return $this->fetchOne(sprintf('SELECT * FROM %s WHERE name = %s', $this->tablename, ACloudSysCoreS::sqlEscape($name)));
    }

    public function gets()
    {
        return $this->fetchAll(sprintf('SELECT * FROM %s ', $this->tablename));
    }
}
