<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: jieyin $>.
 *
 * @author $Author: jieyin $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: WindidConfigApi.php 24718 2013-02-17 06:42:06Z jieyin $
 */
class WindidConfigApi
{
    public function get($name)
    {
        $params = [
            'name' => $name,
        ];

        return WindidApi::open('config/get', $params);
    }

    public function getConfig($namespace)
    {
        $params = [
            'namespace' => $namespace,
        ];

        return WindidApi::open('config/getConfig', $params);
    }

    public function fetchConfig($namespace)
    {
        $params = [
            'namespace' => $namespace,
        ];

        return WindidApi::open('config/fetchConfig', $params);
    }

    public function getConfigByName($namespace, $name)
    {
        $params = [
            'namespace' => $namespace,
            'name'      => $name,
        ];

        return WindidApi::open('config/getConfigByName', $params);
    }

    public function getValues($namespace)
    {
        $params = [
            'namespace' => $namespace,
        ];

        return WindidApi::open('config/getValues', $params);
    }

    /**
     * 设置配置.
     *
     * @param string $namespace 命名空间
     * @param array  $keys
     */
    public function setConfig($namespace, $key, $value)
    {
        $params = [
            'namespace' => $namespace,
            'key'       => $key,
            'value'     => $value,
        ];

        return WindidApi::open('config/setConfig', [], $params);
    }

    public function setConfigs($namespace, $data)
    {
        $params = [
            'namespace' => $namespace,
            'data'      => $data,
        ];

        return WindidApi::open('config/setConfigs', [], $params);
    }

    public function deleteConfig($namespace)
    {
        $params = [
            'namespace' => $namespace,
        ];

        return WindidApi::open('config/deleteConfig', [], $params);
    }

    public function deleteConfigByName($namespace, $name)
    {
        $params = [
            'namespace' => $namespace,
            'name'      => $name,
        ];

        return WindidApi::open('config/deleteConfigByName', [], $params);
    }

    public function setCredits($credits)
    {
        $params = [
            'credits' => $credits,
        ];

        return WindidApi::open('config/setCredits', [], $params);
    }
}
