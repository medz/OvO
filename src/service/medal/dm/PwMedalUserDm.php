<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>.
 *
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwMedalUserDm.php 4875 2012-02-27 05:25:25Z gao.wanggao $
 */
class PwMedalUserDm extends PwBaseDm
{
    public $uid;

    public function __construct($uid = null)
    {
        if (isset($uid)) {
            $this->uid = (int) $uid;
        }
    }

    public function setMedals($medals)
    {
        $this->_data['medals'] = implode(',', $medals);

        return $this;
    }

    public function setCounts($count)
    {
        $this->_data['counts'] = (int) $count;

        return $this;
    }

    public function setExpiredTime($time)
    {
        $this->_data['expired_time'] = (int) $time;

        return $this;
    }

    public function setMedalbrand($brands)
    {
        $this->_data['medal_brand'] = $brands;

        return $this;
    }

    protected function _beforeAdd()
    {
        if (empty($this->uid)) {
            return new PwError('MEDAL:fail');
        }

        return true;
    }

    protected function _beforeUpdate()
    {
        if (empty($this->uid)) {
            return new PwError('MEDAL:fail');
        }

        return true;
    }
}
