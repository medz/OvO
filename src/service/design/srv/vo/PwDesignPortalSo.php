<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>.
 *
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwDesignPortalSo.php 11652 2012-06-11 10:09:24Z gao.wanggao $
 */
class PwDesignPortalSo
{
    protected $_data = array();

    public function getData()
    {
        return $this->_data;
    }

    public function setIsopen($isopen)
    {
        $this->_data['isopen'] = (int) $isopen;
    }

    public function setCreatedUid($uid)
    {
        $this->_data['created_uid'] = (int) $uid;
    }
}
