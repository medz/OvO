<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>.
 *
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwLikeDm.php 12966 2012-06-28 02:06:41Z gao.wanggao $
 */
class PwLikeDm extends PwBaseDm
{
    public $likeid;

    public function __construct($likeid = null)
    {
        if (isset($likeid)) {
            $this->likeid = (int) $likeid;
        }
    }

    public function setTypeid($typeid)
    {
        $this->_data['typeid'] = (int) $typeid;

        return $this;
    }

    public function setFromid($fromid)
    {
        $this->_data['fromid'] = (int) $fromid;

        return $this;
    }

    public function setIsspecial($isspecial)
    {
        $this->_data['isspecial'] = (int) $isspecial;

        return $this;
    }

    public function setUsers($uid)
    {
        $this->_data['users'] = (int) $uid;

        return $this;
    }

    public function setCreatedTime($time = 0)
    {
        $this->_data['created_time'] = (int) $time;

        return $this;
    }

    public function setReplypid($pid = 0)
    {
        $this->_data['reply_pid'] = (int) $pid;

        return $this;
    }

    /**
     * 无字段对应,用于被喜欢用户的记录.
     *
     * @param int $uid
     */
    public function setBeLikeUid($uid)
    {
        $this->_data['belikeuid'] = (int) $uid;

        return $this;
    }

    protected function _beforeAdd()
    {
        return true;
    }

    protected function _beforeUpdate()
    {
        if ($this->likeid < 1) {
            return new PwError('BBS:like.likeid.empty');
        }

        return true;
    }
}
