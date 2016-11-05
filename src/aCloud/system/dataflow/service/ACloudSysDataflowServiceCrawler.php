<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );

class ACloudSysDataFlowServiceCrawler {
	
	private $perpage = 100;
	
	public function crawlTable($tableName, $page, $perpage) {
		list ( $tableName, $page, $perpage ) = array (trim ( $tableName ), intval ( $page ), intval ( $perpage ) );
		$page < 1 && $page = 1;
		$this->setPerpage ( $perpage );
		if (! $tableName)
			return '';
		$tableSettingService = ACloudSysCoreCommon::loadSystemClass ( 'table.settings', 'config.service' );
		$tableSetting = $tableSettingService->getSettingByTableNameWithReplace ( $tableName );
		if (! ACloudSysCoreS::isArray ( $tableSetting ))
			return '';
		if (! $tableSetting ['status'])
			return '';
		list ( $total, $data ) = $tableSetting ['primary_key'] ? $this->getTableDataByPrimaryKey ( $tableSetting, $tableName, $page ) : $this->getTableDataWithoutPrimaryKey ( $tableSetting, $tableName, $page );
		if ($total < 1)
			return '';
		$totalPages = ceil ( $total / $this->perpage );
		return $this->outputDataFlow ( $data, $totalPages );
	}
	
	public function crawlTableMaxId($tableName) {
		$tableName = trim ( $tableName );
		if (! $tableName)
			return '';
		$tableSettingService = ACloudSysCoreCommon::loadSystemClass ( 'table.settings', 'config.service' );
		$tableSetting = $tableSettingService->getSettingByTableNameWithReplace ( $tableName );
		if (! ACloudSysCoreS::isArray ( $tableSetting ))
			return '';
		if (! $tableSetting ['status'])
			return '';
		$maxId = $this->getMaxPrimaryKeyId ( $tableSetting );
		return $this->outputDataFlow ( array (array ('maxid' => $maxId ) ) );
	}
	
	public function crawlTableByIdRange($startId, $endId, $tableName) {
		list ( $tableName, $startId, $endId ) = array (trim ( $tableName ), intval ( $startId ), intval ( $endId ) );
		if (! $tableName)
			return '';
		if ($startId < 0 || $startId > $endId || $endId < 1)
			return '';
		$tableSettingService = ACloudSysCoreCommon::loadSystemClass ( 'table.settings', 'config.service' );
		$tableSetting = $tableSettingService->getSettingByTableNameWithReplace ( $tableName );
		if (! ACloudSysCoreS::isArray ( $tableSetting ))
			return '';
		if (! $tableSetting ['status'])
			return '';
		if (! $tableSetting ['primary_key'])
			return '';
		$data = $this->getDataByPrimaryKeyRange ( $tableSetting, $startId, $endId );
		return $this->outputDataFlow ( $data );
	}
	
	public function crawlDelta() {
	
	}
	
	public function crawlSqlLog($startTime, $endTime, $page, $perpage) {
		list ( $startTime, $endTime, $page, $perpage ) = array (intval ( $startTime ), intval ( $endTime ), intval ( $page ), intval ( $perpage ) );
		if ($startTime > $endTime)
			return '';
		$page < 1 && $page = 1;
		$perpage < 1 && $perpage = $this->perpage;
		$sqlLogService = ACloudSysCoreCommon::loadSystemClass ( 'sql.log', 'config.service' );
		$result = $sqlLogService->getSqlLogsByTimestamp ( $startTime, $endTime, $page, $perpage );
		if (! ACloudSysCoreS::isArray ( $result ))
			return '';
		return $this->outputDataFlow ( $result );
	}
	
	public function crawlSqlLogCount($startTime, $endTime) {
		list ( $startTime, $endTime ) = array (intval ( $startTime ), intval ( $endTime ) );
		if ($startTime > $endTime)
			return '';
		$sqlLogService = ACloudSysCoreCommon::loadSystemClass ( 'sql.log', 'config.service' );
		$result = $sqlLogService->countSqlLogsByTimestamp ( $startTime, $endTime );
		return $this->outputDataFlow ( array (array ('count' => intval ( $result ) ) ) );
	}
	
	public function deleteSqlLog($startTime, $endTime) {
		list ( $startTime, $endTime ) = array (intval ( $startTime ), intval ( $endTime ) );
		$sqlLogService = ACloudSysCoreCommon::loadSystemClass ( 'sql.log', 'config.service' );
		$result = $sqlLogService->deleteSqlLogByTimestamp ( $startTime, $endTime );
		return $this->outputDataFlow ( array (array ('delete' => intval ( $result ) ) ) );
	}
	
	public function crawlDeletedId($type, $startId, $endId) {
		list ( $type, $startId, $endId ) = array (trim ( strtolower ( $type ) ), intval ( $startId ), intval ( $endId ) );
		if (! $type)
			return '';
		if ($startId < 0 || $startId > $endId || $endId < 1)
			return '';
		list ( $commonFactory, $method ) = array ($this->getVerCommonFactory (), 'getVersionCommon' . ucfirst ( $type ) );
		if (! method_exists ( $commonFactory, $method ))
			return '';
		$service = $commonFactory->$method ();
		if (! $service || ! is_object ( $service ) || ! method_exists ( $service, 'getDeletedId' ))
			return '';
		$result = $service->getDeletedId ( $startId, $endId );
		return $this->outputDataFlow ( $result );
	}
	
	public function crawlThreadRange($startId, $endId) {
		list ( $startId, $endId ) = array (intval ( $startId ), intval ( $endId ) );
		$commonFactory = $this->getVerCommonFactory ();
		$commonThread = $commonFactory->getVersionCommonThread ();
		$result = $commonThread->getThreadsByRange ( $startId, $endId );
		return $this->outputDataFlow ( $result );
	}
	
	public function crawlMemberRange($startId, $endId) {
		list ( $startId, $endId ) = array (intval ( $startId ), intval ( $endId ) );
		$commonFactory = $this->getVerCommonFactory ();
		$commonUser = $commonFactory->getVersionCommonUser ();
		$result = $commonUser->getUsersByRange ( $startId, $endId );
		return $this->outputDataFlow ( $result );
	}
	
	public function crawlPostRange($startId, $endId) {
		list ( $startId, $endId ) = array (intval ( $startId ), intval ( $endId ) );
		$commonFactory = $this->getVerCommonFactory ();
		$commonPost = $commonFactory->getVersionCommonPost ();
		$result = $commonPost->getPostsByRange ( $startId, $endId );
		return $this->outputDataFlow ( $result );
	}
	
	public function crawlPostMaxId() {
		$commonFactory = $this->getVerCommonFactory ();
		$commonPost = $commonFactory->getVersionCommonPost ();
		$maxId = $commonPost->getPostMaxId ();
		return $this->outputDataFlow ( array (array ('maxid' => $maxId ) ) );
	}
	
	public function crawlAttachRange($startId, $endId) {
		list ( $startId, $endId ) = array (intval ( $startId ), intval ( $endId ) );
		$commonFactory = $this->getVerCommonFactory ();
		$commonAttach = $commonFactory->getVersionCommonAttach ();
		$result = $commonAttach->getAttachsByRange ( $startId, $endId );
		return $this->outputDataFlow ( $result );
	}
	
	public function crawlForumRange($startId, $endId) {
		list ( $startId, $endId ) = array (intval ( $startId ), intval ( $endId ) );
		$commonFactory = $this->getVerCommonFactory ();
		$commonForum = $commonFactory->getVersionCommonForum ();
		$result = $commonForum->getForumsByRange ( $startId, $endId );
		return $this->outputDataFlow ( $result );
	}
	
	public function crawlDiaryRange($startId, $endId) {
		list ( $startId, $endId ) = array (intval ( $startId ), intval ( $endId ) );
		$commonFactory = $this->getVerCommonFactory ();
		$commonDiary = $commonFactory->getVersionCommonDiary ();
		$result = $commonDiary->getDiarysByRange ( $startId, $endId );
		return $this->outputDataFlow ( $result );
	}
	
	public function crawlColonyRange($startId, $endId) {
		list ( $startId, $endId ) = array (intval ( $startId ), intval ( $endId ) );
		$commonFactory = $this->getVerCommonFactory ();
		$commonColony = $commonFactory->getVersionCommonColony ();
		$result = $commonColony->getColonysByRange ( $startId, $endId );
		return $this->outputDataFlow ( $result );
	}
	
	public function crawlThreadDelta($startTime, $endTime, $page, $perpage) {
		list ( $startTime, $endTime, $page, $perpage ) = array (intval ( $startTime ), intval ( $endTime ), intval ( $page ), intval ( $perpage ) );
		$page < 1 && $page = 1;
		$perpage < 1 && $perpage = $this->perpage;
		$commonFactory = $this->getVerCommonFactory ();
		$commonThread = $commonFactory->getVersionCommonThread ();
		$result = $commonThread->getThreadsByModifiedTime ( $startTime, $endTime, $page, $perpage );
		return $this->outputDataFlow ( $result );
	}
	
	public function crawlThreadDeltaCount($startTime, $endTime) {
		list ( $startTime, $endTime ) = array (intval ( $startTime ), intval ( $endTime ) );
		$commonFactory = $this->getVerCommonFactory ();
		$commonThread = $commonFactory->getVersionCommonThread ();
		$result = $commonThread->getThreadDeltaCount ( $startTime, $endTime );
		return $this->outputDataFlow ( array (array ('count' => $result ) ) );
	}
	
	public function getAttachDirectoriesForStorage(){
		$commonFactory = $this->getVerCommonFactory();
		$commonAttach = $commonFactory->getVersionCommonAttach();
		$result = $commonAttach->getAttachDirectories();
		return $this->outputDataFlow($result);
	}
	
	public function getAttachesForStorage($dir){
		$commonFactory = $this->getVerCommonFactory();
		$commonAttach = $commonFactory->getVersionCommonAttach();
		$result = $commonAttach->getAttachesForStorage($dir);
		return $this->outputDataFlow($result);
	}
	
	private function getTableDataByPrimaryKey($tableSetting, $tableName, $page) {
		list ( $start, $end ) = $this->getIdRange ( $page );
		$generalDataService = ACloudSysCoreCommon::loadSystemClass ( 'generaldata', 'config.service' );
		$maxId = $this->getMaxPrimaryKeyId ( $tableSetting );
		if ($maxId < 1)
			return array (0, array () );
		$data = $this->getDataByPrimaryKeyRange ( $tableSetting, $start, $end );
		return array ($maxId, $data );
	}
	
	private function getTableDataWithoutPrimaryKey($tableSetting, $tableName, $page) {
		list ( $offset, $limit ) = $this->getPageRange ( $page );
		$generalDataService = ACloudSysCoreCommon::loadSystemClass ( 'generaldata', 'config.service' );
		$countSql = sprintf ( 'SELECT COUNT(*) as count FROM %s', ACloudSysCoreS::sqlMetadata ( $tableSetting ['name'] ) );
		list ( $count ) = $generalDataService->executeSql ( $countSql );
		$count = $count ['count'];
		if ($count < 1)
			return array (0, array () );
		$dataSql = sprintf ( 'SELECT * FROM %s %s', ACloudSysCoreS::sqlMetadata ( $tableSetting ['name'] ), ACloudSysCoreS::sqlLimit ( $offset, $limit ) );
		$data = $generalDataService->executeSql ( $dataSql );
		return array ($count, $data );
	}
	
	private function outputDataFlow($data, $totalPages = null) {
		$charset = ACloudSysCoreCommon::getGlobal ( 'g_charset' );
		$formatService = ACloudSysCoreCommon::loadSystemClass ( 'format' );
		return $formatService->dataFlowXmlFormat ( $data, $charset, $totalPages );
	}
	
	private function getMaxPrimaryKeyId($tableSetting) {
		$generalDataService = ACloudSysCoreCommon::loadSystemClass ( 'generaldata', 'config.service' );
		$countSql = sprintf ( 'SELECT MAX(%s) as count FROM %s', ACloudSysCoreS::sqlMetadata ( $tableSetting ['primary_key'] ), ACloudSysCoreS::sqlMetadata ( $tableSetting ['name'] ) );
		list ( $result ) = $generalDataService->executeSql ( $countSql );
		return $result ['count'];
	}
	
	private function getDataByPrimaryKeyRange($tableSetting, $start, $end) {
		$generalDataService = ACloudSysCoreCommon::loadSystemClass ( 'generaldata', 'config.service' );
		$dataSql = sprintf ( 'SELECT * FROM %s WHERE %s >= %s AND %s <= %s', ACloudSysCoreS::sqlMetadata ( $tableSetting ['name'] ), ACloudSysCoreS::sqlMetadata ( $tableSetting ['primary_key'] ), ACloudSysCoreS::sqlEscape ( $start ), ACloudSysCoreS::sqlMetadata ( $tableSetting ['primary_key'] ), ACloudSysCoreS::sqlEscape ( $end ) );
		return $generalDataService->executeSql ( $dataSql );
	}
	
	private function getIdRange($page) {
		$page = intval ( $page ) > 0 ? intval ( $page ) : 1;
		$start = ($page - 1) * $this->perpage + 1;
		$end = $start + $this->perpage - 1;
		return array ($start, $end );
	}
	
	private function getPageRange($page) {
		$page = intval ( $page ) > 0 ? intval ( $page ) : 1;
		$start = ($page - 1) * $this->perpage;
		$start = intval ( $start );
		return array ($start, $this->perpage, $page );
	}
	
	public function setPerpage($perpage) {
		$perpage = intval ( $perpage );
		if ($perpage < 1)
			return false;
		$this->perpage = $perpage;
		return true;
	}
	
	private function getVerCommonFactory() {
		require_once Wind::getRealPath ( 'ACLOUD_VER:common.ACloudVerCommonFactory' );
		return ACloudVerCommonFactory::getInstance ();
	}
}