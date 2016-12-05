<?php

!defined('ACLOUD_PATH') && exit('Forbidden');

class ACloudVerCommonColony extends ACloudVerCommonBase
{
    public function getPrimaryKeyAndTable()
    {
        return array('', '');
    }

    public function getColonysByRange($startId, $endId)
    {
        return array();
    }
}
