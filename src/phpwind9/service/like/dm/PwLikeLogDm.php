<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>.
 *
 * @author $Author$ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id$
 */
class PwLikeLogDm extends PwBaseDm
{
    public $logid;

    public function __construct($logid = null)
    {
        if (isset($logid)) {
            $this->logid = (int) $logid;
        }
    }

    public function setLikeid($likeid)
    {
        $this->_data['likeid'] = (int) $likeid;

        return $this;
    }

    public function setUid($uid)
    {
        $this->_data['uid'] = (int) $uid;

        return $this;
    }

    public function setTagids($tagids)
    {
        $this->_data['tagids'] = implode(',', $tagids);

        return $this;
    }

    public function setCreatedTime($time = 0)
    {
        $this->_data['created_time'] = (int) $time;

        return $this;
    }

    protected function _beforeAdd()
    {
        return true;
    }

    protected function _beforeUpdate()
    {
        if ($this->logid < 1) {
            return new PwError('BBS:like.logid.empty');
        }

        return true;
    }
}
