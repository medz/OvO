<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );

define ( 'POST_INVALID_PARAMS', 301 );

class ACloudVerCommonPost extends ACloudVerCommonBase {
	
	public function getPrimaryKeyAndTable() {
		return array ('bbs_posts', 'pid' );
	}
	
	/**
	 * 获取一个帖子的回复列表
	 *
	 * @param int $tid 帖子id
	 * @param int $limit
	 * @param int $offset
	 * @param bool sort 
	 * return array
	 */
	public function getPost($tid, $sort, $offset, $limit) {
		list ( $tid, $sort, $offset, $limit ) = array (intval ( $tid ), ( bool ) $sort, intval ( $offset ), intval ( $limit ) );
		$result = $this->getThread ()->getPostByTid ( $tid, $limit, $offset, $sort );
		if ($result instanceof PwError)
			return $this->buildResponse ( - 1, $result->getError () );
		return $this->buildResponse ( 0, $result );
	}
	
	/**
	 * 获取用户的回复
	 *
	 * @param int $uid 用户id
	 * @param int $limit 个数
	 * @param int $offset 起始偏移量
	 * return array
	 */
	public function getPostByUid($uid, $offset, $limit) {
		list ( $uid, $offset, $limit ) = array (intval ( $uid ), intval ( $offset ), intval ( $limit ) );
		$user = new PwUserBo ( $uid );
		if (! $user->isExists ())
			return $this->buildResponse ( THREAD_USER_NOT_EXIST );
		$result = $this->getThread ()->getPostByUid ( $uid, $limit, $offset );
		if ($result instanceof PwError)
			return $this->buildResponse ( - 1, $result->getError () );
		return $this->buildResponse ( 0, $result );
	}
	
	/**
	 * 获取用户(A)在帖子(B)中的回复
	 *
	 * @param int $tid
	 * @param int $uid
	 * @param int $limit
	 * @param int $offset
	 * @return array
	 */
	public function getPostByTidAndUid($tid, $uid, $offset, $limit) {
		list ( $uid, $tid, $offset, $limit ) = array (intval ( $uid ), intval ( $tid ), intval ( $offset ), intval ( $limit ) );
		$user = new PwUserBo ( $uid );
		if (! $user->isExists ())
			return $this->buildResponse ( THREAD_USER_NOT_EXIST );
		$result = $this->getThread ()->getPostByTidAndUid ( $tid, $uid, $limit, $offset );
		if ($result instanceof PwError)
			return $this->buildResponse ( - 1, $result->getError () );
		return $this->buildResponse ( 0, $result );
	}
	
	/**
	 * 发送回复
	 * @param int $tid
	 * @param int $uid
	 * @param string $title
	 * @param string $content
	 * return bool
	 */
	public function sendPost($tid, $uid, $title, $content) {
		$userBo = new PwUserBo ( $uid );
		if (! $userBo->isExists ())
			return $this->buildResponse ( THREAD_USER_NOT_EXIST );
		Wind::import ( 'SRV:forum.srv.PwPost' );
		Wind::import ( 'SRV:forum.srv.post.PwReplyPost' );
		$postAction = new PwReplyPost ( $tid );
		$pwPost = new PwPost ( $postAction );
		$info = $pwPost->getInfo ();
		$title == 'Re:' . $info ['subject'] && $title = '';
		$postDm = $pwPost->getDm ();
		$postDm->setTitle ( $title )->setContent ( $content )->setAuthor ( $uid, $userBo->username, $userBo->ip );
		if (($result = $pwPost->execute ( $postDm )) !== true) {
			$this->buildResponse ( - 1, $result->getError () );
		}
		return $this->buildResponse ( 0, $result );
	}
	
	public function shieldPost($pid, $tid) {
	
	}
	
	public function getPostsByRange($startId, $endId) {
		list ( $startId, $endId ) = array (intval ( $startId ), intval ( $endId ) );
		if ($startId < 0 || $startId > $endId || $endId < 1)
			return array ();
		$sql = sprintf ( "SELECT * FROM %s WHERE ischeck = 1 AND pid >= %s AND pid <= %s", ACloudSysCoreS::sqlMetadata ( '{{bbs_posts}}' ), ACloudSysCoreS::sqlEscape ( $startId ), ACloudSysCoreS::sqlEscape ( $endId ) );
		$query = Wind::getComponent ( 'db' )->query ( $sql );
		$result = $query->fetchAll ( null, PDO::FETCH_ASSOC );
		if (! ACloudSysCoreS::isArray ( $result ))
			return array ();
		return $this->buildPostData ( $result );
	}
	
	public function getPostMaxId() {
		$sql = sprintf ( 'SELECT MAX(pid) as count FROM %s', ACloudSysCoreS::sqlMetadata ( '{{bbs_posts}}' ) );
		$query = Wind::getComponent ( 'db' )->query ( $sql );
		return current ( $query->fetch ( PDO::FETCH_ASSOC ) );
	}
	
	private function buildPostData($data) {
		list ( $result, $siteUrl ) = array (array (), ACloudSysCoreCommon::getGlobal ( 'g_siteurl', $_SERVER ['SERVER_NAME'] ) );
		foreach ( $data as $value ) {
			$value ['threadurl'] = 'http://' . $siteUrl . '/read.php?tid=' . $value ['tid'];
			$value ['forumurl'] = 'http://' . $siteUrl . '/index.php?m=bbs&c=thread&fid=' . $value ['fid'];
			$result [$value ['pid']] = $value;
		}
		return $result;
	}
	
	private function getThread() {
		return Wekit::load ( 'SRV:forum.PwThread' );
	}
}