<?php

!defined('ACLOUD_PATH') && exit('Forbidden');
class ACloudVerConfigFilter
{
    public function filterFields($data)
    {
        if (!ACloudSysCoreS::isArray($data)) {
            return $data;
        }
        $result = array();
        foreach ($data as $key => $fields) {
            if (ACloudSysCoreS::isArray($fields)) {
                unset($fields['password'], $fields['safecv']);
            }
            $result[$key] = $fields;
        }

        return $result;
    }
}
