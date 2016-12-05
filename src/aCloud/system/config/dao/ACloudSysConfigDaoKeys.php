<?php

!defined('ACLOUD_PATH') && exit('Forbidden');
require_once Wind::getRealPath('ACLOUD:system.core.ACloudSysCoreDao');
class ACloudSysConfigDaoKeys extends ACloudSysCoreDao
{
    private $tablename = '{{acloud_keys}}';

    public function insert($fields)
    {
        $sql = sprintf('INSERT INTO %s %s', $this->tablename, $this->buildClause($fields));

        return $this->query($sql);
    }

    public function update($fields, $id)
    {
        $sql = sprintf('UPDATE %s %s WHERE id = %s', $this->tablename, $this->buildClause($fields), ACloudSysCoreS::sqlEscape($id));

        return $this->query($sql);
    }

    public function get($id)
    {
        return $this->fetchOne(sprintf('SELECT * FROM %s WHERE id = %s', $this->tablename, ACloudSysCoreS::sqlEscape($id)));
    }

    public function delete($id)
    {
        return $this->query(sprintf('DELETE FROM %s WHERE id = %s', $this->tablename, ACloudSysCoreS::sqlEscape($id)));
    }
}
