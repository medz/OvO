<?php
/**
 * App喜欢来源扩展
 * the last known user to change this file in the repository  <$LastChangedBy$>.
 *
 * @author $Author$ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id$
 */
class PwLikeSourceDao extends PwBaseDao
{
    protected $_pk = 'sid';
    protected $_table = 'like_source';
    protected $_dataStruct = array('sid', 'subject', 'source_url', 'from_app', 'fromid', 'like_count');

    public function getSource($sid)
    {
        return $this->_get($sid);
    }

    public function getSourceByAppAndFromid($fromapp, $fromid)
    {
        $sql = $this->_bindTable('SELECT * FROM %s WHERE from_app = ? AND fromid = ? ');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->getOne(array($fromapp, $fromid));
    }

    public function fetchSource($sids)
    {
        return $this->_fetch($sids, 'sid');
    }

    public function addSource($data)
    {
        return $this->_add($data, true);
    }

    public function deleteSource($sid)
    {
        return $this->_delete($sid);
    }

    public function updateSource($sid, $data)
    {
        return $this->_update($sid, $data);
    }
}
