<?php

defined('WEKIT_VERSION') || exit('Forbidden');
Wind::import('SRV:word.PwWord');
/**
 * 词语过滤对外接口.
 *
 * @author Mingqu Luo <luo.mingqu@gmail.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 *
 * @version $Id: PwWordFilter.php 23428 2013-01-09 10:17:46Z jinlong.panjl $
 */
class PwWordFilter
{
    const WORD_ALL_KEY = 'word';
    const WORD_REPLACE_KEY = 'word_replace';

    public $isTip; //是否敏感词提示
    public $word;

    private static $_instance = null;
    private $_algorithms;

    public function __construct($algorithms = 'DFA')
    {
        $this->isTip = intval(Wekit::C('word', 'istip'));
        $this->_algorithms = $algorithms;
    }

    public static function getInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * 返回替换以后的敏感词 （帖子替换敏感词扫描）.
     *
     * @param string $str     需要扫描的内容
     * @param int    $version 帖子表或者回复表中的word_version
     *
     * @return string
     */
    public function replaceWord($str, $version)
    {
        $word_version = Wekit::C('bbs', 'word_version');
        if ($version == $word_version) {
            return $str;
        }
        $replaceWord = $this->getReplaceWord();

        return $this->_getAlgorithms($replaceWord)->replace($str);
    }

    /**
     * 检测敏感词（直接返回） | 有就返回true ，没有返回false.
     *
     * @param string $str
     *
     * @return bool
     */
    public function filter($str)
    {
        $this->word = $this->fetchAllWord();

        return $this->_getAlgorithms($this->word)->check($str);
    }

    /**
     * 检测是否含有禁止敏感词 （帖子用）.
     *
     * @param string $content
     *
     * @return string||bool|pwerror
     */
    public function filterWord($str)
    {
        $this->word = $this->fetchAllWord();
        list($type, $words) = $this->_getAlgorithms($this->word)->match($str);
        if (!$words) {
            return array(0, array());
        }

        return array($type, $words, $this->isTip);
    }

    /**
     * 格式数据.
     *
     * @param array $data
     *
     * @return array
     */
    private function _buildWord($data)
    {
        if (!is_array($data) || !$data) {
            return array();
        }
        $result = array();
        foreach ($data as $value) {
            $result[] = implode('|', array($value['word'], $value['word_type'], $value['word_replace']));
        }

        return $result;
    }

    /**
     * 获得所有敏感词列表(需谨慎).
     *
     * @return array
     */
    public function fetchAllWord()
    {
        return Wekit::cache()->get(self::WORD_ALL_KEY);
    }

    /**
     * 从数据库获得所有敏感词列表.
     *
     * @return array
     */
    public function fetchAllWordNoCache()
    {
        $words = $this->_buildWord($this->_getWordDS()->fetchAllWord());

        return $this->_getAlgorithms()->createData($words);
    }

    /**
     * 从cache获得所有替换敏感词列表.
     *
     * @return array
     */
    public function getReplaceWord()
    {
        return Wekit::cache()->get(self::WORD_REPLACE_KEY);
    }

    /**
     * 从数据库获得所有替换敏感词列表.
     *
     * @return array
     */
    public function getReplaceWordNoCache()
    {
        $words = $this->_buildWord($this->_getWordDS()->getWordByType(PwWord::WORD_REPLACE));

        return $this->_getAlgorithms()->createData($words);
    }

    public function updateCache()
    {
        $words = $this->_buildWord($this->_getWordDS()->fetchAllWord());
        $this->updateAllWordCache($this->_getAlgorithms()->createData($words));
        $replaceWords = $this->_buildWord($this->_getWordDS()->getWordByType(PwWord::WORD_REPLACE));
        $this->updateReplaceWordCache($this->_getAlgorithms()->createData($replaceWords));

        $configService = new PwConfigSet('bbs');
        $config = Wekit::C('bbs');
        $wordVersion = $config['word_version'] + 1;
        $configService->set('word_version', $wordVersion)->flush();

        return true;
    }

    public function updateAllWordCache($data)
    {
        Wekit::cache()->set(self::WORD_ALL_KEY, $data);

        return true;
    }

    public function updateReplaceWordCache($data)
    {
        Wekit::cache()->set(self::WORD_REPLACE_KEY, $data);

        return true;
    }

    private function _getAlgorithms($data = array())
    {
        $algorithms = strtolower($this->_algorithms);
        $className = sprintf('PwFilter%s', ucfirst($algorithms));
        Wind::import('SRV:word.srv.filter.'.$className);

        return new $className($data);
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
