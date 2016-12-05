<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>.
 *
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwLikeContent.php 8487 2012-04-19 08:09:57Z gao.wanggao $
 */
class PwLikeContent
{
    const THREAD = 1;
    const POST = 2;
    const WEIBO = 3;
    const APP = 9;

    /**
     * 对typeid进行类型绑定
     * Enter description here ...
     *
     * @param int $typeid
     */
    public function transformTypeid($typeid = self::THREAD)
    {
        switch ($typeid) {
            case self::THREAD:
                return 'thread';
            case self::POST:
                return 'post';
            case self::WEIBO:
                return 'weibo';
            case self::APP:
                return 'app';
            default:
                return false;
        }
    }

    /**
     * 获取一条内容.
     *
     * @param  $likeid
     */
    public function getLikeContent($likeid)
    {
        $likeid = (int) $likeid;
        if ($likeid < 1) {
            return array();
        }

        return $this->_getLikeContentDao()->getInfo($likeid);
    }

    /**
     * 批量获取内容.
     *
     * @param array $likeids
     */
    public function fetchLikeContent($likeids)
    {
        if (!is_array($likeids) || count($likeids) < 1) {
            return array();
        }

        return $this->_getLikeContentDao()->fetchInfo($likeids);
    }

    /**
     * 根据typeid和fromid获取内容.
     *
     * @param int $tid
     * @param int $pid
     */
    public function getInfoByTypeidFromid($typeid = self::THREAD, $fromid = 0)
    {
        $typeid = (int) $typeid;
        $fromid = (int) $fromid;
        if ($typeid < 1 && $fromid < 1) {
            return array();
        }

        return $this->_getLikeContentDao()->getInfoByTypeidFromid($typeid, $fromid);
    }

    /**
     * 添加内容.
     *
     * @param PwLikeDm $dm
     */
    public function addInfo(PwLikeDm $dm)
    {
        $resource = $dm->beforeAdd();
        if ($resource instanceof PwError) {
            return $resource;
        }

        return $this->_getLikeContentDao()->addInfo($dm->getData());
    }

    /**
     * 更新内容.
     *
     * @param PwLikeDm $dm
     */
    public function updateInfo(PwLikeDm $dm)
    {
        $resource = $dm->beforeUpdate();
        if ($resource instanceof PwError) {
            return $resource;
        }

        return $this->_getLikeContentDao()->updateInfo($dm->likeid, $dm->getData());
    }

    /**
     * 更新最后回复ID
     * Enter description here ...
     *
     * @param int $likeid
     * @param int $pid
     */
    public function updateLastPid($likeid, $pid)
    {
        $likeid = (int) $likeid;
        $pid = (int) $pid;
        if ($likeid < 1 || $pid < 1) {
            return false;
        }
        $data['reply_pid'] = $pid;

        return $this->_getLikeContentDao()->updateInfo($likeid, $data);
    }

    /**
     * 更新最近喜欢用户列表().
     *
     * @param int $likeid
     * @param int $uid
     * @param int $number 缓存个数
     */
    public function updateUsers($likeid, $uid, $number = 10)
    {
        $likeid = (int) $likeid;
        $uid = (int) $uid;
        if ($likeid < 1) {
            return false;
        }
        if ($uid < 1) {
            return false;
        }
        $data = array();
        $info = $this->_getLikeContentDao()->getInfo($likeid);
        !$info['users'] && $info['users'] == array();
        $_users = explode(',', $info['users']);
        array_unshift($_users, $uid);
        if (count($_users) > $number) {
            array_pop($_users);
        }
        $data['users'] = implode(',', $_users);

        return $this->_getLikeContentDao()->updateInfo($likeid, $data);
    }

    /**
     * 删除内容.
     *
     * @param int $likeid
     */
    public function deleteInfo($likeid)
    {
        $likeid = (int) $likeid;
        if ($likeid < 1) {
            return false;
        }

        return $this->_getLikeContentDao()->deleteInfo($likeid);
    }

    private function _getLikeContentDao()
    {
        return Wekit::loadDao('like.dao.PwLikeContentDao');
    }
}
