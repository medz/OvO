<?php
Wind::import('SRV:report.dm.PwReportDm');
abstract class PwReportAction{
	
	protected $fid = 0;
	
	abstract function buildDm($type_id);
	
	abstract function getExtendReceiver();
}