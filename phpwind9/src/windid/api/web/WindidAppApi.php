<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>.
 *
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: WindidAppApi.php 24414 2013-01-30 05:35:27Z gao.wanggao $
 */
class WindidAppApi
{
    public function getList()
    {
        $params = [
        ];

        return WindidApi::open('app/list', $params);
    }

    public function getApp($id)
    {
        $params = [
            'id' => $id,
        ];

        return WindidApi::open('app/get', $params);
    }

    public function addApp(WindidAppDm $dm)
    {
        $params = $dm->getData();

        return WindidApi::open('app/add', [], $params);
    }

    public function delApp($id)
    {
        $params = [
            'id' => $id,
        ];

        return WindidApi::open('app/delete', [], $params);
    }

    public function editApp(WindidAppDm $dm)
    {
        $params = [
            'id' => $dm->id,
        ];
        $params += $dm->getData();

        return WindidApi::open('app/edit', [], $params);
    }
}
