<?php
/**
 * 友情链接关系DAO
 *
 * @author jinlong.panjl <jinlong.panjl@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: codetemplates(windframework_docs_zend_8.0).xml 2781 下午03:16:28Z yishuo $
 * @package PwLinkRelationsDao
 */
class PwLinkRelationsDao extends PwBaseDao
{
    protected $_table = 'link_relations';
    protected $_dataStruct = array('lid', 'typeid');

    /**
     * 添加
     *
     * @param  array $data
     * @return int
     */
    public function addLinkRelations($data)
    {
        return $this->_add($data);
    }

    /**
     * 根据lid删除
     *
     * @param  int  $lid
     * @return bool
     */
    public function delRelationsByLid($lid)
    {
        $sql = $this->_bindTable('DELETE FROM %s  WHERE `lid`=?');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->update(array($lid));
    }

    /**
     * 根据lid批量删除
     *
     * @param  int  $lid
     * @return bool
     */
    public function batchDelRelationsByLid($lid)
    {
        $sql = $this->_bindSql('DELETE FROM %s  WHERE `lid` IN %s', $this->getTable(), $this->sqlImplode($lid));

        return $this->getConnection()->createStatement($sql)->execute();
    }

    /**
     * 根据typeid删除
     *
     * @param  int  $typeid
     * @return bool
     */
    public function delRelationsByTypeid($typeid)
    {
        $sql = $this->_bindTable('DELETE FROM %s  WHERE `typeid`=?');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->update(array($typeid));
    }

    /**
     * 根据typeid获取数据
     *
     * @param  int $typeid
     * @return int
     */
    public function getByTypeId($typeid)
    {
        $where = '';
        $param = array();
        if ($typeid != '') {
            $where .= ' WHERE `typeid`=?';
            $param[] = $typeid;
        }
        $sql = $this->_bindSql('SELECT * FROM %s %s', $this->getTable(), $where);
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll($param);
    }

    /**
     * 根据lid获取数据
     *
     * @param  int $lid
     * @return int
     */
    public function getByLinkId($lid)
    {
        $sql = $this->_bindTable('SELECT * FROM %s WHERE `lid`=?', $this->getTable());
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll(array($lid));
    }

    /**
     * 根据链接ID批量获取与类型的对于关系
     *
     * @param  array $linkids
     * @return array
     */
    public function fetchByLinkId($linkids)
    {
        $sql = $this->_bindSql('SELECT * FROM %s WHERE `lid` IN %s', $this->getTable(), $this->sqlImplode($linkids));
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll();
    }

    /**
     * 统计分类数量
     *
     * @return array
     */
    public function countLinkTypes()
    {
        $sql = $this->_bindTable('SELECT typeid,COUNT(*) as linknum FROM %s GROUP BY typeid');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll(array(), 'typeid');
    }
}
