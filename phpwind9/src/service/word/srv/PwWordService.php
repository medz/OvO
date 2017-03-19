<?php

defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 词语过滤服务
 *
 * @author Mingqu Luo <luo.mingqu@gmail.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 *
 * @version $Id: PwWordService.php 17009 2012-08-30 08:19:36Z hejin $
 */
class PwWordService
{
    /**
     * 获得敏感词.
     *
     * @param array $word
     *
     * @return array
     */
    public function findWord($word = [])
    {
        if (empty($word) || !is_array($word)) {
            return [];
        }
        $wordList = $this->_getWordDS()->fetchByWord($word);
        if (!$wordList) {
            return [];
        }
        $result = [];
        foreach ($wordList as $value) {
            $result[] = $value['word'];
        }

        return $result;
    }

    /**
     * 是否存在敏感词.
     *
     * @param string $word
     * @param int    $excludeId 排除ID
     *
     * @return bool
     */
    public function isExistWord($word, $excludeId = 0)
    {
        $data = $this->_getWordDS()->getByWord($word);
        if (!$data) {
            return false;
        }

        if ($excludeId && ($data['word_id'] == $excludeId)) {
            return false;
        }

        return true;
    }

    /**
     * 删除.
     *
     * @param int    $type
     * @param string $keyword
     *
     * @return bool
     */
    public function deleteByCondition($type = 1, $keyword = '')
    {
        $type = intval($type);
        $type = $type < 0 ? 0 : $type;
        if ($type && !$keyword) {
            return $this->_getWordDS()->deleteByType($type);
        }
        if (!$type && $keyword) {
            return $this->_getWordDS()->deleteByKeyword($keyword);
        }
        if ($type && $keyword) {
            return $this->_getWordDS()->deleteByTypeAndKeyword($type, $keyword);
        }

        return $this->_getWordDS()->truncate();
    }

    /**
     * get PwWord.
     *
     * @return PwWord
     */
    private function _getWordDS()
    {
        return Wekit::load('word.PwWord');
    }
}
