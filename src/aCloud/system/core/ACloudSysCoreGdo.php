<?php

!defined('ACLOUD_PATH') && exit('Forbidden');
class ACloudSysCoreGdo
{
    private $gdo = array();

    public function get($key, $default = null)
    {
        return isset($this->gdo[$key]) ? $this->gdo[$key] : $default;
    }

    public function set($key, $value)
    {
        $this->gdo[$this->getKeyName($key)] = $value;
    }

    public function gets(array $keys)
    {
        $tmp = array();
        foreach ($keys as $key) {
            $tmp[] = $this->get($key);
        }

        return $tmp;
    }

    public function getAll()
    {
        return $this->gdo;
    }
}
