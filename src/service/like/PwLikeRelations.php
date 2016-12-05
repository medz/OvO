<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>.
 *
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwLikeRelations.php 5754 2012-03-10 07:01:17Z gao.wanggao $
 */
class PwLikeRelations
{
    /**
     * 获取内容.
     *
     * @param int $tagid
     */
    public function getLikeRelation($tagid)
    {
        $tagid = (int) $tagid;
        if ($tagid < 1) {
            return array();
        }

        return $this->_getLikeRelationsDao()->getInfo($tagid);
    }

    public function getInfoList($tagid, $start, $limit)
    {
        $tagid = (int) $tagid;
        $limit = (int) $limit;
        $start = (int) $start;

        return $this->_getLikeRelationsDao()->getInfoList($tagid, $start, $limit);
    }

    /**
     * 添加内容.
     *
     * @param int $logid
     * @param int $tagid
     */
    public function addInfo($logid, $tagid)
    {
        $tagid = (int) $tagid;
        $logid = (int) $logid;
        if ($tagid < 1 || $logid < 1) {
            return false;
        }
        $data['logid'] = $logid;
        $data['tagid'] = $tagid;

        return $this->_getLikeRelationsDao()->addInfo($data);
    }

    /**
     * 删除内容.
     *
     * @param int $logid
     * @param int $tagid
     */
    public function deleteInfo($logid, $tagid)
    {
        $tagid = (int) $tagid;
        $logid = (int) $logid;
        if ($tagid < 1 || $logid < 1) {
            return false;
        }

        return $this->_getLikeRelationsDao()->deleteInfo($logid, $tagid);
    }

    /**
     * 删除多条内容.
     *
     * @param int $tagid
     */
    public function deleteInfos($tagid)
    {
        $tagid = (int) $tagid;
        if ($tagid < 1) {
            return false;
        }

        return $this->_getLikeRelationsDao()->deleteInfos($tagid);
    }

    /**
     * 删除内容.
     *
     * @param int $logid
     */
    public function deleteInfosBylogid($logid)
    {
        $logid = (int) $logid;
        if ($logid < 1) {
            return false;
        }

        return $this->_getLikeRelationsDao()->deleteInfosBylogid($logid);
    }

    private function _getLikeRelationsDao()
    {
        return Wekit::loadDao('like.dao.PwLikeRelationsDao');
    }
}
