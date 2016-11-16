<?php


/**
 * pw_domain
 *
 * @author Shi Long <long.shi@alibaba-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $id$
 * @package service.domain.dao
 */
class PwDomainDao extends PwBaseDao
{
    protected $_table = 'domain';
    protected $_pk = 'domain_key';
    protected $_dataStruct = array('domain_key', 'domain_type', 'domain', 'root', 'first', 'id');

    /**
     * 添加一个个性域名
     *
     * @param  array       $data
     * @return bool|number
     */
    public function replaceDomain($data)
    {
        if (!$data = $this->_filterStruct($data)) {
            return false;
        }
        $sql = $this->_bindSql('REPLACE INTO %s SET %s', $this->getTable(), $this->sqlSingle($data));

        return $this->getConnection()->execute($sql);
    }

    /**
     * 根据type更新
     *
     * @param  string        $type
     * @param  array         $data
     * @return bool|Ambigous <number, boolean, rowCount>
     */
    public function updateByDomainType($type, $data)
    {
        if (!$data = $this->_filterStruct($data)) {
            return false;
        }
        $sql = $this->_bindSql('UPDATE %s SET %s WHERE `domain_type` = ?', $this->getTable(), $this->sqlSingle($data));

        return $this->getConnection()->createStatement($sql)->update(array($type));
    }

    /**
     * 根据key更新
     *
     * @param  string        $key
     * @param  array         $data
     * @return bool|Ambigous <number, boolean, rowCount>
     */
    public function updateByDomainKey($key, $data)
    {
        if (!$data = $this->_filterStruct($data)) {
            return false;
        }
        $sql = $this->_bindSql('UPDATE %s SET %s WHERE `domain_key` = ?', $this->getTable(), $this->sqlSingle($data));

        return $this->getConnection()->createStatement($sql)->update(array($key));
    }

    /**
     * 根据type删除
     *
     * @param  unknown_type $type
     * @return number
     */
    public function deleteByDomainType($type)
    {
        $sql = $this->_bindTable('DELETE FROM %s WHERE `domain_type` = ?');

        return $this->getConnection()->createStatement($sql)->update(array($type));
    }

    /**
     * 根据key删除
     *
     * @param  unknown_type $key
     * @return number
     */
    public function deleteByDomainKey($key)
    {
        $sql = $this->_bindTable('DELETE FROM %s WHERE `domain_key` = ?');

        return $this->getConnection()->createStatement($sql)->update(array($key));
    }

    /**
     * 根据key获取
     *
     * @param  unknown_type $key
     * @return Ambigous     <multitype:, multitype:unknown , mixed>
     */
    public function getByDomainKey($key)
    {
        $sql = $this->_bindTable('SELECT * FROM %s WHERE `domain_key` = ?');

        return $this->getConnection()->createStatement($sql)->getOne(array($key));
    }

    /**
     * 根据域名和根域名获取
     *
     * @param  string $domain
     * @param  string $root
     * @return array
     */
    public function getByDomainAndRoot($domain, $root)
    {
        $sql = $this->_bindTable('SELECT * FROM %s WHERE `domain` = ? AND `root` = ?');

        return $this->getConnection()->createStatement($sql)->getOne(array($domain, $root));
    }

    /**
     * 根据首字母查询
     *
     * @return array
     */
    public function getByFirst($first)
    {
        $sql = $this->_bindTable('SELECT * FROM %s WHERE `first` = ?');

        return $this->getConnection()->createStatement($sql)->queryAll(array($first));
    }

    /**
     * 根据类型查询
     *
     * @return array
     */
    public function getByType($type)
    {
        $sql = $this->_bindTable('SELECT * FROM %s WHERE `domain_type` = ?');

        return $this->getConnection()->createStatement($sql)->queryAll(array($type));
    }

    /**
     * 根据域名和类型查询
     *
     * @return array
     */
    public function getByDomainAndType($domain, $type)
    {
        $sql = $this->_bindTable('SELECT * FROM %s WHERE `domain_type` = ? AND `domain` = ?');

        return $this->getConnection()->createStatement($sql)->queryAll(array($type, $domain));
    }

    /**
     * 根据域名查询
     *
     * @return array
     */
    public function getByDomain($domain)
    {
        $sql = $this->_bindTable('SELECT * FROM %s WHERE `domain` = ?');

        return $this->getConnection()->createStatement($sql)->queryAll(array($domain));
    }

    /**
     * 获取所有域名
     *
     */
    public function getAll()
    {
        $sql = $this->_bindTable('SELECT * FROM %s');

        return $this->getConnection()->query($sql)->fetchAll($this->_pk);
    }

    /**
     * 根据类型和id查询
     *
     * @return array
     */
    public function getByTypeAndId($type, $id)
    {
        $sql = $this->_bindTable('SELECT * FROM %s WHERE `domain_type` = ? AND `id` = ?');

        return $this->getConnection()->createStatement($sql)->getOne(array($type, $id));
    }

    /**
     * 根据某一类型和id批量查询
     *
     * @return array
     */
    public function fetchByTypeAndId($type, $ids)
    {
        $sql = $this->_bindSql('SELECT * FROM %s WHERE `domain_type` = ? AND `id` IN %s', $this->getTable(), $this->sqlImplode($ids));

        return $this->getConnection()->createStatement($sql)->queryAll(array($type), 'id');
    }
}
