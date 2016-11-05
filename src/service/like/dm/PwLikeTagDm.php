<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwLikeTagDm.php 14218 2012-07-18 07:31:50Z gao.wanggao $
 * @package
 */
class PwLikeTagDm extends PwBaseDm
{
    public $tagid;

    public function __construct($tagid = null)
    {
        if (isset($tagid)) {
            $this->tagid = (int) $tagid;
        }
    }

    public function setUid($uid)
    {
        $this->_data['uid'] = intval($uid);

        return $this;
    }

    public function setTagname($tagname)
    {
        $this->_data['tagname'] = Pw::substrs($tagname, 10);

        return $this;
    }

    public function setNumber($number)
    {
        $this->_data['number'] = intval($number);

        return $this;
    }

    protected function _beforeAdd()
    {
        if (empty($this->_data['tagname'])) {
            return new PwError('BBS:like.tagname.empty');
        }

        return true;
    }

    protected function _beforeUpdate()
    {
        if ($this->tagid < 1) {
            return new PwError('BBS:like.tagid.empty');
        }
        if (empty($this->_data['tagname'])) {
            return new PwError('BBS:like.tagname.not.empty');
        }

        return true;
    }
}
