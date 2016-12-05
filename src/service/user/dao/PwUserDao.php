<?php
/**
 * 用户数据扩展表.
 *
 * @author Shi Long <long.shi@alibaba-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwUserDao.php 21135 2012-11-29 02:10:03Z jieyin $
 */
class PwUserDao extends PwBaseDao
{
    protected $_table = 'user';
    protected $_pk = 'uid';
    protected $_dataStruct = array('uid', 'username', 'email', 'password', 'regdate', 'realname', 'status', 'groupid', 'memberid', 'groups');

    /**
     * 根据用户ID获得用户的扩展数据.
     *
     * @param int $uid 用户ID
     *
     * @return array
     */
    public function getUserByUid($uid)
    {
        return $this->_get($uid);
    }

    /**
     * 根据用户名获得用户信息.
     *
     * @param string $username
     *
     * @return array
     */
    public function getUserByName($username)
    {
        $sql = $this->_bindTable('SELECT * FROM %s WHERE username=?');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->getOne(array($username));
    }

    /**
     * 根据用户的email获得用户信息.
     *
     * @param string $email
     *
     * @return array
     */
    public function getUserByEmail($email)
    {
        $sql = $this->_bindTable('SELECT * FROM %s WHERE email=?');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->getOne(array($email));
    }

    /**
     * 根据用户ID列表获取ID.
     *
     * @param array $uids
     *
     * @return array
     */
    public function fetchUserByUid($uids)
    {
        return $this->_fetch($uids, 'uid');
    }

    /**
     * 根据用户名列表批量获得用户信息.
     *
     * @param array $usernames
     *
     * @return array
     */
    public function fetchUserByName($usernames)
    {
        $sql = $this->_bindSql('SELECT * FROM %s WHERE username IN %s', $this->getTable(), $this->sqlImplode($usernames));
        $rst = $this->getConnection()->query($sql);

        return $rst->fetchAll('uid');
    }

    /**
     * 插入用户扩展数据.
     *
     * @param array $fields 用户数据
     *
     * @return bool|Ambigous <number, boolean, rowCount>
     */
    public function addUser($fields)
    {
        $this->_add($fields, false);

        return true;
    }

    /**
     * 根据用户ID更新用户扩展数据.
     *
     * @param int   $uid    用户ID
     * @param array $fields 用户扩展数据
     *
     * @return bool|int
     */
    public function editUser($uid, $fields, $increaseFields = array(), $bitFields = array())
    {
        return $this->_update($uid, $fields, $increaseFields, $bitFields);
    }

    /**
     * 删除用户数据.
     *
     * @param int $uid 用户ID
     *
     * @return int
     */
    public function deleteUser($uid)
    {
        return $this->_delete($uid);
    }

    /**
     * 批量删除用户信息.
     *
     * @param array $uids 用户ID
     *
     * @return int
     */
    public function batchDeleteUser($uids)
    {
        return $this->_batchDelete($uids);
    }
}
