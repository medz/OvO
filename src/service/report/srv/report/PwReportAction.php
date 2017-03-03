<?php

 
abstract class PwReportAction
{
    protected $fid = 0;

    abstract public function buildDm($type_id);

    abstract public function getExtendReceiver();
}
