<?php

!defined('ACLOUD_PATH') && exit('Forbidden');
class ACloudSysCoreSqlbuilder
{
    public function buildSelectSql($config, $request)
    {
        if (!ACloudSysCoreS::isArray($config) || !ACloudSysCoreS::isArray($request)) {
            return '';
        }
        $arguments = $this->buildArguments($request, $config['argument'], $config['argument_type']);
        $fields = $this->buildFields($request, $config['fields']);

        return $this->replaceSql($config['template'], $arguments, $fields);
    }

    public function buildArguments($request, $arguments, $argumentTypes)
    {
        if (!$arguments) {
            return array();
        }
        list($arguments, $argumentTypes, $result) = array(explode(',', $arguments), $this->buildArgumentTypes($argumentTypes), array());
        foreach ($arguments as $argument) {
            $argument = strtolower($argument);
            $argumentType = isset($argumentTypes[$argument]) ? $argumentTypes[$argument] : 'int';
            $argumentValue = $this->getRequestArgumentValue($argument, $request);
            $result[$argument] = is_null($argumentValue) ? null : $this->filterArgument($argument, $argumentType, $argumentValue);
        }

        return $result;
    }

    public function buildArgumentTypes($argumentTypes)
    {
        list($result, $argumentTypes, $allowedTypes) = array(array(), trim($argumentTypes), $this->getAllowedTypes());
        if (!$argumentTypes) {
            return $result;
        }
        $argumentTypes = explode(',', $argumentTypes);
        foreach ($argumentTypes as $argumentType) {
            list($argument, $type) = explode('|', $argumentType);
            $type = strtolower($type);
            !in_array($type, $allowedTypes) && $type = 'int';
            $result[$argument] = $type;
        }

        return $result;
    }

    public function getAllowedTypes()
    {
        return array('int', 'array', 'string');
    }

    public function getRequestArgumentValue($param, $request)
    {
        $charset = ACloudSysCoreCommon::getGlobal('g_charset');

        return isset($request[$param]) ? ACloudSysCoreCommon::convert($request[$param], $charset, 'UTF-8') : null;
    }

    public function filterArgument($argument, $argumentType, $value)
    {
        if ($argumentType == 'array') {
            return explode(',', $value);
        }

        return $argumentType == 'int' ? intval($value) : ACloudSysCoreS::sqlEscape($value);
    }

    public function buildFields($request, $fields)
    {
        $requestFields = $this->getRequestArgumentValue('fields', $request);
        $fields = $fields ? explode(',', $fields) : array();
        if (!ACloudSysCoreS::isArray($fields)) {
            return '*';
        }
        if (is_null($requestFields)) {
            return $this->formatFields($fields);
        }
        $requestFields = explode(',', $requestFields);
        $intersect = (!ACloudSysCoreS::isArray($requestFields)) ? $fields : array_intersect($fields, $requestFields);
        !ACloudSysCoreS::isArray($intersect) && $intersect = $fields;

        return $this->formatFields($intersect);
    }

    public function formatFields($fields)
    {
        $result = array();
        if (!ACloudSysCoreS::isArray($fields)) {
            return '*';
        }
        foreach ($fields as $field) {
            list($tableAlias, $fieldName) = strpos($field, '.') === false ? array('', $field) : explode('.', $field);
            $result[] = ($tableAlias ? $tableAlias.'.' : '').ACloudSysCoreS::sqlMetadata($fieldName);
        }

        return implode(',', $result);
    }

    public function replaceSql($sqlTemplate, $argumentValues, $fields)
    {
        $sqlTemplate = preg_replace('/\{\{(\w+?)\}\}/', '[[$1]]', $sqlTemplate);
        preg_match_all('/\{(\w+)\}/', $sqlTemplate, $matches);
        if (!ACloudSysCoreS::isArray($matches)) {
            return '';
        }
        $seg = $this->getRandString(4);
        $sql = preg_replace('/\{(\w+)\}/', $seg.'{${1}}'.$seg, $sqlTemplate);
        foreach ($matches[0] as $k => $v) {
            $value = ($v != '{fields}') ? (is_array($argumentValues[$matches[1][$k]]) ? ACloudSysCoreS::sqlImplode($argumentValues[$matches[1][$k]]) : $argumentValues[$matches[1][$k]]) : $fields;
            $sql = str_replace($seg.$v.$seg, $value, $sql);
        }
        $sql = preg_replace('/\[\[(\w+?)\]\]/', '{{$1}}', $sql);

        return $sql;
    }

    public function getRandString($length)
    {
        return substr(md5($this->getRandNum($length)), mt_rand(0, 32 - $length), $length);
    }

    public function getRandNum($length)
    {
        mt_srand((float) microtime() * 1000000);
        $randVal = mt_rand(1, 9);
        for ($i = 1; $i < $length; $i++) {
            $randVal .= mt_rand(0, 9);
        }

        return $randVal;
    }
}
