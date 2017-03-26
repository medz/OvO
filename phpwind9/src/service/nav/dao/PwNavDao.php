<?php

Wind::import('SRC:library.base.PwBaseDao');
/**
 * 导航DAO服务
 *
 * @author $Author: gao.wanggao $
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwNavDao.php 24004 2013-01-18 06:18:11Z gao.wanggao $
 */
class PwNavDao extends PwBaseDao
{
    protected $_pk = 'navid';
    protected $_table = 'common_nav';
    protected $_dataStruct = ['navid', 'parentid', 'rootid', 'type', 'sign', 'name', 'style', 'link', 'alt', 'image', 'target', 'isshow', 'orderid'];

    /**
     * 根据ID获取一条导航信息.
     *
     * @param int $navId ID
     *
     * @return array
     */
    public function getNav($navid)
    {
        return $this->_get($navid);
    }

    /**
     * 获取多条导航信息.
     *
     * @param array $navids
     *
     * @return Ambigous <multitype:, multitype:multitype: Ambigous <multitype:, multitype:unknown , mixed> >
     */
    public function fetchNav($navids)
    {
        return $this->_fetch($navids, $this->_pk);
    }

    /**
     * 获取某类型导航列表.
     *
     * @param string $type   导航类型
     * @param int    $isShow 是否显示
     *
     * @return array
     */
    public function getNavByType($type, $isShow)
    {
        $where = 'WHERE type = ? ';
        $_array = [$type];
        if ($isShow < 2) {
            $where .= ' AND isshow = ? ';
            $_array[] = $isShow;
        }
        $sql = $this->_bindSql('SELECT * FROM %s %s ORDER BY rootid ASC,parentid ASC,orderid ASC', $this->getTable(), $where);
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll($_array);
    }

    public function getNavBySign($type, $sign)
    {
        $sql = $this->_bindTable('SELECT *  FROM %s WHERE type = ? AND sign = ? ');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->getOne([$type, $sign]);
    }

    /**
     * 获取顶级导航列表.
     *
     * @param string $type 导航类型
     *
     * @return array
     */
    public function getRootNav($type, $isShow)
    {
        $where = 'WHERE type = ? AND parentid = 0 ';
        $_array = [$type];
        if ($isShow < 2) {
            $where .= ' AND isshow = ? ';
            $_array[] = $isShow;
        }
        $sql = $this->_bindSql('SELECT * FROM %s %s ORDER BY orderid ASC', $this->getTable(), $where);
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll($_array);
    }

    /**
     * 获取顶级导航的子导航列表.
     *
     * @param int $navId 父导航ID
     *
     * @return array
     */
    public function getChildNav($navId, $isShow)
    {
        $where = ' WHERE parentid = ? ';
        $_array = [$navId];
        if ($isShow < 2) {
            $where .= ' AND isshow = ? ';
            $_array[] = $isShow;
        }
        $sql = $this->_bindSql('SELECT * FROM %s %s ORDER BY orderid ASC', $this->getTable(), $where);
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll($_array);
    }

    /**
     * 获取导航最大排序.
     *
     * @param string $type     导航类型
     * @param int    $parentid 父ID
     *
     * @return int
     */
    public function getNavMaxOrder($type = '', $parentid = 0)
    {
        $sql = $this->_bindTable('SELECT MAX(orderid) AS max FROM %s WHERE type = ? AND parentid = ? ');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->getValue([$type, $parentid]);
    }

    /**
     * 添加一条导航.
     *
     * @param array $data
     *
     * @return int
     */
    public function addNav($data)
    {
        return $this->_add($data, true);
    }

    /**
     * 修改一条导航.
     *
     * @param array $data
     *
     * @return bool
     */
    public function updateNav($navid, $data)
    {
        return $this->_update($navid, $data);
    }

    /**
     * 删除一条导航.
     *
     * @param int $navId
     *
     * @return bool
     */
    public function delNav($navid)
    {
        return $this->_delete($navid);
    }
}
