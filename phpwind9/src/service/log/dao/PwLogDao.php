<?php

Wind::import('SRC:library.base.PwBaseDao');

/**
 * 前台管理日志  dAO服务
 *
 * @author xiaoxia.xu<xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 *
 * @version $Id: PwLogDao.php 24747 2013-02-20 03:13:43Z jieyin $
 */
class PwLogDao extends PwBaseDao
{
    protected $_table = 'log';
    protected $_pk = 'id';
    protected $_dataStruct = ['id', 'typeid', 'created_userid', 'created_time', 'operated_uid', 'created_username', 'operated_username', 'ip', 'fid', 'tid', 'pid', 'extends', 'content'];

    /**
     * 根据tid获得该帖子的相关管理日志.
     *
     * @param int $tid
     * @param int $pid
     * @param int $limit
     * @param int $start
     *
     * @return array
     */
    public function getLogByTid($tid, $pid, $limit, $start = 0)
    {
        $sql = $this->_bindSql('SELECT * FROM %s WHERE tid = ? AND pid = ? ORDER BY id DESC %s', $this->getTable(), $this->sqlLimit($limit, $start));

        return $this->getConnection()->createStatement($sql)->queryAll([$tid, $pid], 'id');
    }

    public function fetchLogByTid($tids, $typeid)
    {
        $sql = $this->_bindSql('SELECT * FROM %s WHERE tid IN %s AND pid=0 AND typeid IN %s', $this->getTable(), $this->sqlImplode($tids), $this->sqlImplode($typeid));
        $rst = $this->getConnection()->query($sql);

        return $rst->fetchAll();
    }

    /**
     * 添加日志.
     *
     * @param array $data
     *
     * @return int
     */
    public function addLog($data)
    {
        return $this->_add($data);
    }

    /**
     * 批量添加日志.
     *
     * @param array $datas
     *
     * @return int
     */
    public function batchAddLog($datas)
    {
        $clear = $fields = [];
        foreach ($datas as $key => $_item) {
            if (! ($_item = $this->_filterStruct($_item))) {
                continue;
            }
            $_temp = [];
            $_temp['created_userid'] = $_item['created_userid'];
            $_temp['created_username'] = $_item['created_username'];
            $_temp['operated_uid'] = $_item['operated_uid'];
            $_temp['operated_username'] = $_item['operated_username'];
            $_temp['created_time'] = $_item['created_time'];
            $_temp['typeid'] = $_item['typeid'];
            $_temp['fid'] = $_item['fid'];
            $_temp['tid'] = $_item['tid'];
            $_temp['ip'] = $_item['ip'];
            $_temp['extends'] = $_item['extends'];
            $_temp['content'] = $_item['content'];
            $_temp['pid'] = $_item['pid'];
            $clear[] = $_temp;
        }
        if (! $clear) {
            return false;
        }
        $sql = $this->_bindSql('INSERT INTO %s (`created_userid`, `created_username`, `operated_uid`, `operated_username`, `created_time`, `typeid`, `fid`, `tid`, `ip`, `extends`, `content`, `pid`) VALUES %s', $this->getTable(), $this->sqlMulti($clear));

        return $this->getConnection()->execute($sql);
    }

    /**

     * 根据日志ID删除某条日志.
     *
     * @param int $id
     *
     * @return int
     */
    public function deleteLog($id)
    {
        return $this->_delete($id);
    }

    /**
     * 根据日志ID列表删除日志.
     *
     * @param array $ids
     *
     * @return int
     */
    public function batchDeleteLog($ids)
    {
        return $this->_batchDelete($ids);
    }

    /**
     * 清除某个时间段之前的日志.
     *
     * @param int $time
     *
     * @return int
     */
    public function clearLogBeforeDatetime($time)
    {
        $sql = $this->_bindTable('DELETE FROM %s WHERE created_time < ?');

        return $this->getConnection()->createStatement($sql)->execute([$time], true);
    }

    /**
     * 根据条件搜索日志.
     *
     * @param array $condition
     * @param int   $limit
     * @param int   $offset
     */
    public function search($condition, $limit = 10, $offset = 0)
    {
        list($where, $params) = $this->_buildCondition($condition);
        $sql = $this->_bindSql('SELECT * FROM %s %s ORDER BY id DESC %s', $this->getTable(), $where, $this->sqlLimit($limit, $offset));

        return $this->getConnection()->createStatement($sql)->queryAll($params);
    }

    /**
     * 根据搜索条件统计结果.
     *
     * @param array $condition
     *
     * @return int
     */
    public function countSearch($condition)
    {
        list($where, $params) = $this->_buildCondition($condition);
        $sql = $this->_bindSql('SELECT COUNT(*) FROM %s %s', $this->getTable(), $where);

        return $this->getConnection()->createStatement($sql)->getValue($params);
    }

    /**
     * 后台搜索.
     *
     * @param array $condition
     *
     * @return array
     */
    private function _buildCondition($condition)
    {
        $where = $params = [];
        foreach ($condition as $_k => $_v) {
            if (! $_v) {
                continue;
            }
            switch ($_k) {
                case 'operated_username':
                case 'created_username':
                case 'ip':
                    $where[] = "{$_k} LIKE ?";
                    $params[] = $_v.'%';
                    break;
                case 'operated_uid':
                case 'created_userid':
                    if (! is_array($_v)) {
                        $_v = [$_v];
                    }
                    $where[] = $this->_bindSql('%s IN (%s)', $_k, $this->sqlImplode($_v));
                    break;
                case 'typeid':
                case 'fid':
                    $where[] = "{$_k} = ?";
                    $params[] = $_v;
                    break;
                case 'start_time':
                    $where[] = 'created_time >= ?';

                    $params[] = $_v;
                    break;
                case 'end_time':
                    $where[] = 'created_time <= ?';
                    $params[] = $_v;

                    break;
                case 'keywords':
                    $where[] = 'content LIKE ?';

                    $params[] = '%'.$_v.'%';

                    break;
                default:
                    break;
            }
        }

        return $where ? [' WHERE '.implode(' AND ', $where), $params] : ['', []];
    }
}
