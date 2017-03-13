<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: jieyin $>.
 *
 * @author $Author: jieyin $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: WindidAreaApi.php 24517 2013-01-31 07:04:40Z jieyin $
 */
class WindidAreaApi
{
    public function getArea($id)
    {
        $params = [
            'id' => $id,
        ];

        return WindidApi::open('area/get', $params);
    }

    public function fetchArea($ids)
    {
        $params = [
            'ids' => $ids,
        ];

        return WindidApi::open('area/fetch', $params);
    }

    public function getByParentid($parentid)
    {
        $params = [
            'parentid' => $parentid,
        ];

        return WindidApi::open('area/getByParentid', $params);
    }

    public function getAll()
    {
        $params = [];

        return WindidApi::open('area/getAll', $params);
        if (!is_array($result)) {
            return [];
        }

        return $result;
    }

    public function getAreaInfo($areaid)
    {
        $params = [
            'areaid' => $areaid,
        ];

        return WindidApi::open('area/getAreaInfo', $params);
    }

    public function fetchAreaInfo($areaids)
    {
        $params = [
            'areaids' => $areaids,
        ];

        return WindidApi::open('area/fetchAreaInfo', $params);
    }

    public function getAreaRout($areaid)
    {
        $params = [
            'areaid' => $areaid,
        ];

        return WindidApi::open('area/getAreaRout', $params);
    }

    public function fetchAreaRout($areaids)
    {
        $params = [
            'areaids' => $areaids,
        ];

        return WindidApi::open('area/fetchAreaRout', $params);
    }

    public function getAreaTree()
    {
        return WindidApi::open('area/getAreaTree', []);
    }

    public function updateArea(WindidAreaDm $dm)
    {
        $params = [
            'id' => $dm->areaid,
        ];

        return WindidApi::open('area/update', $params, $dm->getData());
    }

    public function batchAddArea($dms)
    {
        $data = [];
        foreach ($dms as $k => $dm) {
            $data['id'][] = $dm->areaid;
            $_data = $dm->getData();
            foreach ($_data as $_k => $_v) {
                $data[$_k][] = $_v;
            }
        }

        return WindidApi::open('area/batchadd', [], $data);
    }

    public function deleteArea($areaid)
    {
        $params = [
            'id' => $areaid,
        ];

        return WindidApi::open('area/delete', [], $params);
    }
}
