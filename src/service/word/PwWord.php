<?php

defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 词语过滤DS基础服务
 *
 * @author Mingqu Luo <luo.mingqu@gmail.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwWord.php 17009 2012-08-30 08:19:36Z hejin $
 * @package wind
 */

class PwWord
{
    const NAME_BANED = 1;        //禁用
    const WORD_VERIFIED = 2;    //审核
    const WORD_REPLACE = 3;    //替换

    /**
     * 类型列表
     *
     * @return array
     */
    public function getTypeMap()
    {
        $map = array(
                self::NAME_BANED => '禁用',
                self::WORD_VERIFIED => '审核',
                self::WORD_REPLACE => '替换',
        );

        return $map;
    }

    /**
     * 获取单条敏感词信息
     *
     * @param  int   $wordId
     * @return array
     */
    public function get($wordId)
    {
        $wordId = intval($wordId);
        if ($wordId < 1) {
            return array();
        }

        return $this->_getDao()->get($wordId);
    }

    /**
     * 获得某个敏感词记录
     *
     * @param  string $word
     * @return array
     */
    public function getByWord($word)
    {
        if (!$word) {
            return array();
        }

        return $this->_getDao()->getByWord($word);
    }

    /**
     * 获得某个类型敏感词
     *
     * @param  int   $type
     * @return array
     */
    public function getWordByType($type)
    {
        $type = intval($type);
        if ($type < 1) {
            return array();
        }

        return $this->_getDao()->getByType($type);
    }

    /**
     * 获得多条敏感词信息
     *
     * @param  array $wordIds
     * @return array
     */
    public function fetch($wordIds)
    {
        if (empty($wordIds) || !is_array($wordIds)) {
            return array();
        }

        return $this->_getDao()->fetch($wordIds);
    }

    /**
     * 根据敏感词获得列表
     *
     * @param  array $word
     * @return array
     */
    public function fetchByWord($word = array())
    {
        if (empty($word) || !is_array($word)) {
            return array();
        }

        return $this->_getDao()->fetchByWord($word);
    }

    /**
     * 获得敏感词列表
     *
     * @param  int   $limit
     * @param  int   $offset
     * @return array
     */
    public function getWordList($limit = 20, $offset = 0)
    {
        return $this->_getDao()->getWordList($limit, $offset);
    }

    /**
     * 统计数量
     *
     * @return int
     */
    public function count()
    {
        return $this->_getDao()->count();
    }

    /**
     * 根据不同来源统计敏感词数量
     *
     * @param  int $from 来源 0代表 local 1代表platform
     * @return int
     */
    public function countByFrom($from = 0)
    {
        return $this->_getDao()->countByFrom($from);
    }

    /**
     * 添加
     *
     * @param PwWordDm $dm
     */
    public function add(PwWordDm $dm)
    {
        if (($result = $dm->beforeAdd()) !== true) {
            return $result;
        }

        $result = $this->_getDao()->add($dm->getData());

        return $result;
    }

    /**
     * 删除
     *
     * @param  int  $wordId
     * @return bool
     */
    public function delete($wordId)
    {
        $wordId = intval($wordId);
        if ($wordId < 1) {
            return false;
        }
        $result = $this->_getDao()->delete($wordId);

        return $result;
    }

    /**
     * 根据类型删除
     *
     * @param  int  $type
     * @return bool
     */
    public function deleteByType($type)
    {
        if (!$type) {
            return false;
        }

        return $this->_getDao()->deleteByType($type);
    }

    /**
     * 根据关键字删除
     *
     * @param  string $keyword
     * @return bool
     */
    public function deleteByKeyword($keyword)
    {
        if (!$keyword) {
            return false;
        }

        return $this->_getDao()->deleteByKeyword($keyword);
    }

    /**
     * 根据类型、关键字删除
     *
     * @param  int    $type
     * @param  string $keyword
     * @return bool
     */
    public function deleteByTypeAndKeyword($type, $keyword)
    {
        if (!$type || !$keyword) {
            return false;
        }

        return $this->_getDao()->deleteByTypeAndKeyword($type, $keyword);
    }
    /**
     * 更新
     *
     * @param PwWordDm $dm
     */
    public function update(PwWordDm $dm)
    {
        if (($result = $dm->beforeUpdate()) !== true) {
            return $result;
        }
        $fieldData = $dm->getData();
        if (!$fieldData) {
            return false;
        }

        $result = $this->_getDao()->update($dm->id, $fieldData);

        return $result;
    }

    /**
     * 批量更新
     *
     * @param array    $wordIds
     * @param PwWordDm $dm
     */
    public function batchUpdate($wordIds, PwWordDm $dm)
    {
        if (empty($wordIds) || !is_array($wordIds)) {
            return false;
        }
        if (($result = $dm->beforeUpdate()) !== true) {
            return $result;
        }

        $result = $this->_getDao()->batchUpdate($wordIds, $dm->getData());

        return $result;
    }

    /**
     * 批量删除
     *
     * @param  array $wordIds
     * @return bool
     */
    public function batchDelete($wordIds)
    {
        if (empty($wordIds) || !is_array($wordIds)) {
            return false;
        }
        $result = $this->_getDao()->batchDelete($wordIds);

        return $result;
    }

    /**
     * 是否存在敏感词
     *
     * @param  string $word
     * @param  int    $excludeId 排除ID
     * @return bool
     */
    private function _isExistWord($word, $excludeId = 0)
    {
        $word = $this->getByWord($word);
        if (!$word) {
            return false;
        }

        if ($excludeId && ($word['word_id'] == $excludeId)) {
            return false;
        }

        return true;
    }

    /**
     * 统计(仅服务搜索)
     *
     * @param  PwWordSo $so
     * @return int
     */
    public function countSearchWord(PwWordSo $so)
    {
        return $this->_getDao()->countSearchWord($so->getData());
    }

    /**
     * 仅服务搜索
     *
     * @param  PwWordSo $so
     * @return array
     */
    public function searchWord(PwWordSo $so, $limit = 20, $offset = 0)
    {
        return $this->_getDao()->searchWord($so->getData(), $limit, $offset);
    }

    /**
     * 从数据表直接获得所有敏感词列表(需谨慎)
     *
     * @return array
     */
    public function fetchAllWord()
    {
        return $this->_getDao()->fetchAllWord();
    }

    /**
     * 清空数据(需谨慎)
     *
     */
    public function truncate()
    {
        return $this->_getDao()->truncate();
    }

    /**
     * 类型全部更新(需谨慎仅后台使用)
     *
     */
    public function updateAllByTypeAndRelpace($type, $relpace)
    {
        $fieldData = array('word_type' => $type, 'word_replace' => $relpace);

        return $this->_getDao()->updateAll($fieldData);
    }

    /**
     * 判断是否是替换词
     *
     * @param  int  $type
     * @return bool
     */
    public function isReplaceWord($type)
    {
        return intval($type) == self::WORD_REPLACE;
    }

    /**
     * get PwWordDao
     *
     * @return PwWordDao
     */
    protected function _getDao()
    {
        return Wekit::loadDao('word.dao.PwWordDao');
    }
}
