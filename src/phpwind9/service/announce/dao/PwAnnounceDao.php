<?php

defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 公告管理基础表dao服务
 *
 * @author MingXing Sun <mingxing.sun@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwAnnounceDao.php 5140 2012-02-29 08:21:33Z mingxing.sun $
 */
class PwAnnounceDao extends PwBaseDao
{
    protected $_table = 'announce';
    protected $_pk = 'aid';
    protected $_dataStruct = ['aid', 'vieworder', 'created_userid', 'typeid', 'url', 'subject', 'content', 'start_date', 'end_date'];

    /**
     * 添加一条公告信息.
     *
     * @param array $fields
     *
     * @return int
     */
    public function addAnnounce($fields)
    {
        return $this->_add($fields);
    }

    /**
     * 删除一条公告信息.
     *
     * @param int $aid
     *
     * @return bool
     */
    public function deleteAnnounce($aid)
    {
        return $this->_delete($aid);
    }

    /**
     * 批量删除公告信息.
     *
     * @param array $aids
     *
     * @return bool
     */
    public function batchDeleteAnnounce($aids)
    {
        return $this->_batchDelete($aids);
    }

    /**
     * 更新一条公告信息.
     *
     * @param int   $aid
     * @param array $fields
     *
     * @return bool
     */
    public function updateAnnounce($aid, $fields)
    {
        return $this->_update($aid, $fields);
    }

    /**
     * 获取公告信息.
     *
     * @param $offset
     * @param $limit
     *
     * @return array
     */
    public function getAnnounceOrderByVieworder($limit, $offset)
    {
        $sql = $this->_bindSql('SELECT * FROM %s ORDER BY vieworder ASC %s', $this->getTable(), $this->sqlLimit($limit, $offset));
        $smt = $this->getConnection()->query($sql);

        return $smt->fetchAll('aid');
    }

    /**
     * 通过时间获取公告信息
     * 业务为获取正在发布中的公告信息.
     *
     * @param $time
     * @param $offset
     * @param $limit
     *
     * @return array
     */
    public function getAnnounceByTimeOrderByVieworder($time, $limit, $offset)
    {
        $sql = $this->_bindSql('SELECT * FROM %s WHERE start_date <= ? AND end_date >= ? ORDER BY vieworder ASC %s', $this->getTable(), $this->sqlLimit($limit, $offset));
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll([$time, $time], 'aid');
    }

    /**
     * 获取公告数.
     *
     * @return int
     */
    public function countAnnounce()
    {
        $sql = $this->_bindSql('SELECT COUNT(*) as count FROM %s ', $this->getTable());
        $smt = $this->getConnection()->query($sql);

        return $smt->fetchColumn();
    }

    /**
     * 获取某一时间内的公告数
     * 业务为获取发布中公告的数量值
     *
     * @param int $time
     *
     * @return int
     */
    public function countAnnounceByTime($time)
    {
        $sql = $this->_bindSql('SELECT COUNT(*) as count FROM %s WHERE start_date <= ? AND end_date >= ? ', $this->getTable());
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->getValue([$time, $time]);
    }

    /**
     * 获取一条公告信息.
     *
     * @param int $aid
     *
     * @return array
     */
    public function getAnnounce($aid)
    {
        return $this->_get($aid);
    }
}
