<?php


/**
 * Pw_seo表的dao.
 *
 * @author Shi Long <long.shi@alibaba-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 *
 * @version $Id$
 */
class PwSeoDao extends PwBaseDao
{
    protected $_table = 'seo';
    protected $_dataStruct = ['mod', 'page', 'param', 'title', 'keywords', 'description'];

    /**
     * 批量更新或添加seo数据.
     *
     * @param array $data
     *
     * @return bool
     */
    public function batchReplaceSeo($data)
    {
        if (! is_array($data)) {
            return false;
        }
        $tmp = [];
        foreach ($data as $v) {
            $v = $this->_filterStruct($v);
            $v && $tmp[] = [
                $v['mod'],
                $v['page'],
                $v['param'] ? $v['param'] : 0,
                $v['title'],
                $v['keywords'],
                $v['description'], ];
        }
        if (empty($tmp)) {
            return false;
        }
        $sql = $this->_bindSql(
            'REPLACE INTO %s (`mod`, `page`, `param`, `title`, `keywords`, `description`) VALUES %s',
            $this->getTable(), $this->sqlMulti($tmp));

        return $this->getConnection()->execute($sql);
    }

    /**
     * 获取单条记录.
     *
     * @param string $mod
     * @param string $page
     * @param string $param
     *
     * @return array
     */
    public function getByModAndPageAndParam($mod, $page, $param)
    {
        $sql = $this->_bindTable('SELECT * FROM %s WHERE `mod` = ? AND `page` = ? AND `param` = ?');

        return $this->getConnection()->createStatement($sql)->getOne([$mod, $page, $param]);
    }

    /**
     * 根据模式和页面批量获取.
     *
     * @param string $mod
     * @param string $page
     *
     * @return array
     */
    public function getByModAndPage($mod, $page)
    {
        $sql = $this->_bindTable('SELECT * FROM %s WHERE `mod` = ? AND `page` = ?');
        $result = $this->getConnection()->createStatement($sql)->queryAll([$mod, $page]);

        return $this->_buildResult($result);
    }

    /**
     * 根据模式获取.
     *
     * @param string $mod
     *
     * @return array
     */
    public function getByMod($mod)
    {
        $sql = $this->_bindTable('SELECT * FROM %s WHERE `mod` = ?');
        $result = $this->getConnection()->createStatement($sql)->queryAll([$mod]);

        return $this->_buildResult($result);
    }

    /**
     * 根据参数获取多个seo数据.
     *
     * @param string $mod
     * @param string $page
     * @param array  $params
     *
     * @return array
     */
    public function getByParams($mod, $page, $params = [])
    {
        $sql = $this->_bindSql('SELECT * FROM %s WHERE `mod` = ? AND `page` = ? AND `param` IN %s',
            $this->getTable(), $this->sqlImplode($params));
        $result = $this->getConnection()->createStatement($sql)->queryAll([$mod, $page]);

        return $this->_buildResult($result);
    }

    /**
     * 组装数据.
     *
     * @param array $result
     *
     * @return array
     */
    private function _buildResult($result)
    {
        $seo = [];
        foreach ($result as $v) {
            if (! $v['param']) {
                $seo[$v['page']][0] = [
                    'title'       => $v['title'],
                    'keywords'    => $v['keywords'],
                    'description' => $v['description'], ];
            } else {
                $seo[$v['page']][$v['param']] = [
                    'title'       => $v['title'],
                    'keywords'    => $v['keywords'],
                    'description' => $v['description'], ];
            }
        }

        return $seo;
    }
}
