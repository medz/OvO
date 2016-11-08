<?php

! defined('ACLOUD_PATH') && exit('Forbidden');

class ACloudVerCommonDiary extends ACloudVerCommonBase
{
    public function getPrimaryKeyAndTable()
    {
        return array('', '');
    }

    public function getDiarysByRange($startId, $endId)
    {
        return array();
    }
}
