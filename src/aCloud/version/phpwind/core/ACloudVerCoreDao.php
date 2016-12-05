<?php

!defined('ACLOUD_PATH') && exit('Forbidden');
class ACloudVerCoreDao
{
    private $_baseInstance = null;

    public function __construct()
    {
        $this->getDB();
    }

    public function ACloud_Ver_Core_Dao()
    {
        $this->__construct();
    }

    public function getDB()
    {
        if (!$this->_baseInstance) {
            $this->_baseInstance = Wind::getComponent('db');
        }

        return $this->_baseInstance;
    }

    public function fetchOne($sql)
    {
        $query = $this->getDB()->query($sql);

        return $query->fetch(PDO::FETCH_ASSOC);
    }

    public function fetchAll($sql, $resultIndexKey = null, $type = PDO::FETCH_ASSOC)
    {
        $query = $this->getDB()->query($sql);

        return $query->fetchAll($resultIndexKey, $type);
    }

    public function getField($sql)
    {
        $query = $this->getDB()->query($sql);

        return $query->fetchColumn();
    }

    public function query($sql)
    {
        $result = $this->getDB()->execute($sql);

        return ($result) ? true : false;
    }

    public function insert_id()
    {
        return $this->getDB()->lastInsertId();
    }
}
