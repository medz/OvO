<?php
/**
 * 用户权限dao服务
 *
 * @author peihong.zhangph <peihong.zhangph@aliyun-inc.com> Nov 2, 2011
 *
 * @link http://www.phpwind.com
 *
 * @copyright 2011 phpwind.com
 * @license
 *
 * @version $Id: PwUserPermissionGroupsDao.php 24736 2013-02-19 09:24:40Z jieyin $
 */
class PwUserPermissionGroupsDao extends PwBaseDao
{
    protected $_table = 'user_permission_groups';
    protected $_dataStruct = ['gid', 'rkey', 'rtype', 'rvalue', 'vtype'];

    /**
     * 设置用户组权限.
     *
     * @param array $fields
     */
    public function setGroupPermission($fields)
    {
        $sql = $this->_bindSql('REPLACE INTO %s (`gid`,`rkey`,`rtype`,`rvalue`,`vtype`) VALUES %s', $this->getTable(), $this->sqlMulti($fields));

        return $this->getConnection()->execute($sql);
    }

    /**
     * 获取某会员组的权限.
     *
     * @param string $gid
     * @param array  $keys
     */
    public function getPermissions($gid, $keys = [])
    {
        $sql = $this->_bindTable('SELECT * FROM %s WHERE gid=?');
        $keys && $sql .= ' AND rkey IN'.$this->sqlImplode($keys);
        $smt = $this->getConnection()->createStatement($sql);

        return $this->_format($smt->queryAll([$gid], 'rkey'));
    }

    public function getPermissionByRkey($rkey)
    {
        $sql = $this->_bindTable('SELECT * FROM %s WHERE rkey=?');
        $smt = $this->getConnection()->createStatement($sql);

        return $this->_format($smt->queryAll([$rkey], 'gid'));
    }

    public function getPermissionByRkeyAndGids($rkey, $gids)
    {
        $sql = $this->_bindSql('SELECT * FROM %s WHERE rkey=? AND gid IN %s', $this->getTable(), $this->sqlImplode($gids));
        $smt = $this->getConnection()->createStatement($sql);

        return $this->_format($smt->queryAll([$rkey], 'gid'));
    }

    /**
     * 获取某类rkey的权限.
     *
     * @param string $rkeys
     * @param array
     */
    public function fetchPermissionByRkey($rkeys)
    {
        $sql = $this->_bindSql('SELECT * FROM %s WHERE rkey IN %s', $this->getTable(), $this->sqlImplode($rkeys));
        $smt = $this->getConnection()->query($sql);

        return $this->_format($smt->fetchAll());
    }

    public function fetchPermissionByGid($gids)
    {
        $sql = $this->_bindSql('SELECT * FROM %s WHERE gid IN %s', $this->getTable(), $this->sqlImplode($gids));
        $rst = $this->getConnection()->query($sql);

        return $this->_format($rst->fetchAll());
    }

    /**
     * 删除某用户组所有权限.
     *
     * @param int $gid
     */
    public function deletePermissionsByGid($gid)
    {
        $sql = $this->_bindTable('DELETE FROM %s WHERE gid=?');

        return $this->getConnection()->createStatement($sql)->update([$gid]);
    }

    /**
     * 删除某用户组所有权限.
     *
     * @param int   $gid
     * @param array $keys
     */
    public function batchDeletePermissionByGidAndKeys($gid, $keys)
    {
        $sql = $this->_bindSql('DELETE FROM %s WHERE gid=? AND rkey IN %s', $this->getTable(), $this->sqlImplode($keys));

        return $this->getConnection()->createStatement($sql)->update([$gid]);
    }

    protected function _format($result)
    {
        foreach ($result as $key => $value) {
            $value['vtype'] == 'array' && $value['rvalue'] = unserialize($value['rvalue']);
            $result[$key] = $value;
        }

        return $result;
    }
}
