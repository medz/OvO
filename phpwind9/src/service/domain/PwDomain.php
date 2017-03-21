<?php
/**
 * domain-DS.
 *
 * @author Shi Long <long.shi@alibaba-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 *
 * @version $Id$
 */
class PwDomain
{
    /**
     * 添加一个个性域名.
     *
     * @param array $data
     *
     * @return bool|number
     */
    public function replaceDomain(PwDomainDm $dm)
    {
        if (($r = $dm->beforeUpdate()) instanceof PwError) {
            return $r;
        }

        return $this->_domainDao()->replaceDomain($dm->getData());
    }

    /**
     * 根据type更新.
     *
     * @param PwDomainDm $dm
     *
     * @return bool|Ambigous <number, boolean, rowCount>
     */
    public function updateByDomainType(PwDomainDm $dm)
    {
        if (! $type = $dm->getField('domain_type')) {
            return new PwError('REWRITE:domain_type_null');
        }
        if (($r = $dm->beforeUpdate()) instanceof PwError) {
            return $r;
        }

        return $this->_domainDao()->updateByDomainType($type, $dm->getData());
    }

    /**
     * 根据key更新.
     *
     * @param PwDomainDm $dm
     *
     * @return bool|Ambigous <number, boolean, rowCount>
     */
    public function updateByDomainKey(PwDomainDm $dm)
    {
        if (! $key = $dm->getField('domain_key')) {
            return new PwError('DOMAIN:domain_key_null');
        }
        if (($r = $dm->beforeUpdate()) instanceof PwError) {
            return $r;
        }

        return $this->_domainDao()->updateByDomainKey($key, $dm->getData());
    }

    /**
     * 根据type删除.
     *
     * @param string $type
     *
     * @return number
     */
    public function deleteByDomainType($type)
    {
        if (! $type) {
            return false;
        }

        return $this->_domainDao()->deleteByDomainType($type);
    }

    /**
     * 根据key删除.
     *
     * @param unknown_type $key
     *
     * @return bool|Ambigous <number, number>
     */
    public function deleteByDomainKey($key)
    {
        if (! $key) {
            return false;
        }

        return $this->_domainDao()->deleteByDomainKey($key);
    }

    /**
     * 根据key获取.
     *
     * @param unknown_type $key
     *
     * @return Ambigous <multitype:, multitype:unknown , mixed>
     */
    public function getByDomainKey($key)
    {
        if (! $key) {
            return [];
        }

        return $this->_domainDao()->getByDomainKey($key);
    }

    /**
     * 根据域名和根域名获取.
     *
     * @param string $domain
     * @param string $root
     *
     * @return array
     */
    public function getByDomainAndRoot($domain, $root)
    {
        if (! $domain || ! $root) {
            return [];
        }

        return $this->_domainDao()->getByDomainAndRoot($domain, $root);
    }

    /**
     * 仅供计划任务用.
     *
     * @return array
     */
    public function getByFirst($first)
    {
        return $this->_domainDao()->getByFirst($first);
    }

    /**
     * 根据类型查询.
     *
     * @param string $appType
     *
     * @return array
     */
    public function getByType($type)
    {
        return $this->_domainDao()->getByType($type);
    }

    /**
     * 根据域名和类型查询.
     *
     * @param string $domain
     * @param string $type
     *
     * @return array
     */
    public function getByDomainAndType($domain, $type)
    {
        return $this->_domainDao()->getByDomainAndType($domain, $type);
    }

    /**
     * 根据域名查询.
     *
     * @param string $domain
     * @param string $type
     *
     * @return array
     */
    public function getByDomain($domain)
    {
        return $this->_domainDao()->getByDomain($domain);
    }

    /**
     * 获取所有域名.
     */
    public function getAll()
    {
        return $this->_domainDao()->getAll();
    }

    /**
     * 根据Id和类型查询.
     *
     * @param string $type
     * @param int    $id
     *
     * @return array
     */
    public function getByTypeAndId($type, $id)
    {
        list($type, $id) = [trim($type), intval($id)];
        if (! $type || $id < 1) {
            return [];
        }

        return $this->_domainDao()->getByTypeAndId($type, $id);
    }

    /**
     * 根据某一类型和id批量查询.
     *
     * @param string $type
     * @param array  $ids
     *
     * @return array
     */
    public function fetchByTypeAndId($type, $ids)
    {
        $type = trim($type);
        if (! $type || ! is_array($ids) || count($ids) < 1) {
            return [];
        }

        return $this->_domainDao()->fetchByTypeAndId($type, $ids);
    }

    /**
     * @return PwDomainDao
     */
    private function _domainDao()
    {
        return Wekit::loadDao('domain.dao.PwDomainDao');
    }
}
