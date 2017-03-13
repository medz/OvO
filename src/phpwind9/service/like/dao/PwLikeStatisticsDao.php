<?php

Wind::import('SRC:library.base.PwBaseDao');
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>.
 *
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwLikeStatisticsDao.php 8553 2012-04-20 06:52:03Z gao.wanggao $
 */
class PwLikeStatisticsDao extends PwBaseDao
{
    protected $_table = 'like_statistics';
    protected $_dataStruct = ['signkey', 'likeid', 'fromid', 'typeid', 'number'];

    public function getInfo($signkey)
    {
        $sql = $this->_bindTable('SELECT * FROM %s WHERE signkey = ? ');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->getOne([$signkey]);
    }

    public function getInfoByLikeid($signkey, $likeid)
    {
        $sql = $this->_bindTable('SELECT * FROM %s WHERE likeid = ? AND signkey = ? ');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->getOne([$likeid, $signkey]);
    }

    public function fetchInfo($signkeys)
    {
        $sql = $this->_bindSql('SELECT * FROM %s WHERE signkey IN %s ', $this->getTable(), $this->sqlImplode($signkeys));
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll([]);
    }

    public function getInfoList($signkey, $offset, $limit, $typeid)
    {
        $where = ' WHERE signkey = ? ';
        $array = [$signkey];
        if ($typeid) {
            $where .= ' AND typeid = ? ';
            $array[] = $typeid;
        }
        $sql = $this->_bindSql('SELECT * FROM %s %s ORDER BY number DESC %s ', $this->getTable(), $where, $this->sqlLimit($limit, $offset));
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll($array);
    }

    public function getMinInfo($signkey)
    {
        $sql = $this->_bindTable('SELECT *  FROM %s WHERE signkey = ? ORDER BY number DESC LIMIT 1 ');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->getOne([$signkey]);
    }

    public function countSignkey($signkey)
    {
        $sql = $this->_bindTable('SELECT COUNT(*) AS count FROM %s WHERE signkey = ? ');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->getValue([$signkey]);
    }

    public function addInfo($data)
    {
        if (!$data = $this->_filterStruct($data)) {
            return false;
        }
        $sql = $this->_bindSql('INSERT INTO %s SET %s', $this->getTable(), $this->sqlSingle($data));
        $this->getConnection()->execute($sql);

        return $this->getConnection()->lastInsertId();
    }

    public function updateInfo($data)
    {
        if (!$data = $this->_filterStruct($data)) {
            return false;
        }
        $signkey = $data['signkey'];
        $likeid = $data['likeid'];
        $sql = $this->_bindSql('UPDATE %s SET %s WHERE likeid = ? AND  signkey = ? ', $this->getTable(), $this->sqlSingle($data));
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->update([$likeid, $signkey]);
    }

    public function deleteInfo($signkey, $likeid)
    {
        $sql = $this->_bindTable('DELETE FROM %s  WHERE signkey = ? AND likeid = ? ');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->update([$signkey, $likeid]);
    }

    public function deleteInfos($signkey)
    {
        $sql = $this->_bindTable('DELETE FROM %s  WHERE signkey = ? ');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->update([$signkey]);
    }
}
