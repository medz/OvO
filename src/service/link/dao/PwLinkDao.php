<?php

Wind::import('SRC:library.base.PwBaseDao');

/**
 * 友情链接DAO
 *
 * @author jinlong.panjl <jinlong.panjl@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: codetemplates(windframework_docs_zend_8.0).xml 2781 下午03:16:53Z yishuo $
 * @package PwLinkDao
 */
class PwLinkDao extends PwBaseDao
{
    protected $_pk = 'lid';
    protected $_table = 'link';
    protected $_dataStruct = array('lid', 'vieworder', 'name', 'url', 'descrip', 'logo', 'iflogo', 'ifcheck', 'contact');

    /**
     * 添加一条链接
     *
     * @param  array $data
     * @return int
     */
    public function addLink($data)
    {
        return $this->_add($data);
    }

    /**
     * 删除一条链接
     *
     * @param  int  $lid
     * @return bool
     */
    public function delete($lid)
    {
        return $this->_delete($lid);
    }

    /**
     * 删除多条信息
     *
     * @param  array $lids
     * @return bool
     */
    public function batchDelete($lids)
    {
        return $this->_batchDelete($lids);
    }

    /**
     * 修改一条信息
     *
     * @param  int   $lid
     * @param  array $data
     * @return bool
     */
    public function updateLink($lid, $data)
    {
        return $this->_update($lid, $data);
    }

    /**
     * 获取一条信息
     *
     * @param  int   $lid
     * @return array
     */
    public function getLink($lid)
    {
        return $this->_get($lid);
    }

    /**
     * 获取链接数量
     *
     * @param  int   $ifcheck 0 未审核| 1已审核
     * @return array
     */
    public function countLinks($ifcheck)
    {
        $where = '';
        $param = array();
        if ($ifcheck !== '') {
            $where .= ' WHERE `ifcheck`=?';
            $param[] = $ifcheck;
        }
        $sql = $this->_bindSql('SELECT COUNT(*) FROM %s %s', $this->getTable(), $where);
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->getValue($param);
    }

    /**
     * 获取链接
     *
     * @param  int   $ifcheck 0 未审核| 1已审核
     * @param  int   $start
     * @param  int   $limit
     * @return array
     */
    public function getLinks($start, $limit, $ifcheck)
    {
        $where = '';
        $param = array();
        if ($ifcheck !== '') {
            $where .= ' WHERE `ifcheck`=?';
            $param[] = $ifcheck;
        }
        $sql = $this->_bindSql('SELECT * FROM %s %s ORDER BY `vieworder` ASC '.$this->sqlLimit($limit, $start), $this->getTable(), $where);
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll($param, 'lid');
    }

    /**
     * 根据lids获取链接
     *
     * @param  array $lids
     * @return array
     */
    public function getLinksByLids($lids)
    {
        $where = $lids ? ' `lid` IN '.$this->sqlImplode($lids).' AND ' : '';
        $sql = $this->_bindTable("SELECT * FROM %s WHERE $where ifcheck = 1 ORDER BY `vieworder` ASC ");
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll(array(), 'lid');
    }
}
