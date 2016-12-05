<?php

!defined('ACLOUD_PATH') && exit('Forbidden');
require_once Wind::getRealPath('ACLOUD:system.core.ACloudSysCoreDao');
class ACloudSysConfigDaoTableSettings extends ACloudSysCoreDao
{
    private $tablename = '{{acloud_table_settings}}';

    public function insert($fields)
    {
        $sql = sprintf('INSERT INTO %s %s', $this->tablename, $this->buildClause($fields));

        return $this->query($sql);
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

    public function delete($name)
    {
        return $this->query(sprintf('DELETE FROM %s WHERE name = %s', $this->tablename, ACloudSysCoreS::sqlEscape($name)));
    }

    public function gets()
    {
        return $this->fetchAll(sprintf('SELECT * FROM %s ', $this->tablename));
    }
}
