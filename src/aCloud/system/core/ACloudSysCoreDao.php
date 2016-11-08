<?php

! defined('ACLOUD_PATH') && exit('Forbidden');
class ACloudSysCoreDao
{
    public function fetchOne($sql)
    {
        return ACloudSysCoreCommon::getGlobal(ACloudSysCoreDefine::ACLOUD_OBJECT_DAO)->fetchOne($sql);
    }

    public function fetchAll($sql, $resultIndexKey = null, $type = PDO::FETCH_ASSOC)
    {
        return ACloudSysCoreCommon::getGlobal(ACloudSysCoreDefine::ACLOUD_OBJECT_DAO)->fetchAll($sql, $resultIndexKey, $type);
    }

    public function getField($sql)
    {
        return ACloudSysCoreCommon::getGlobal(ACloudSysCoreDefine::ACLOUD_OBJECT_DAO)->getField($sql);
    }

    public function query($sql)
    {
        return ACloudSysCoreCommon::getGlobal(ACloudSysCoreDefine::ACLOUD_OBJECT_DAO)->query($sql);
    }

    public function insert_id()
    {
        return ACloudSysCoreCommon::getGlobal(ACloudSysCoreDefine::ACLOUD_OBJECT_DAO)->insert_id();
    }

    public function affected_rows()
    {
        return ACloudSysCoreCommon::getGlobal(ACloudSysCoreDefine::ACLOUD_OBJECT_DAO)->affected_rows();
    }

    public function getDB()
    {
        return ACloudSysCoreCommon::getGlobal(ACloudSysCoreDefine::ACLOUD_OBJECT_DAO)->getDB();
    }

    public function buildClause($arrays, $expr = null)
    {
        if (! is_array($arrays) && ! $expr) {
            return '';
        }
        $sets = ' SET ';
        if ($expr) {
            foreach ($expr as $v) {
                $sets .= ' '.$v.',';
            }
        }
        if ($arrays) {
            foreach ($arrays as $k => $v) {
                $sets .= ' '.ACloudSysCoreS::sqlMetadata($k).' = '.ACloudSysCoreS::sqlEscape($v).',';
            }
        }
        $sets = trim($sets, ',');

        return ($sets) ? $sets : '';
    }
}
