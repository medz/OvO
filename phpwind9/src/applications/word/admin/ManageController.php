<?php

defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('ADMIN:library.AdminBaseController');

/**
 * 词语过滤Controller.
 *
 * @author Mingqu Luo <luo.mingqu@gmail.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 *
 * @version $Id: ManageController.php 28865 2013-05-28 03:34:43Z jieyin $
 */
class ManageController extends AdminBaseController
{
    private $_configName = 'word';

    public function run()
    {
        $total = $this->_getWordDS()->count();

        $this->setOutput($total, 'total');
        $this->setOutput($total ? $this->_getWordDS()->getWordList() : [], 'wordList');
        $this->setOutput($this->_getWordDS()->getTypeMap(), 'typeList');
        $this->setOutput(Wekit::C($this->_configName), 'config');
        $this->setOutput(1, 'page');
        $this->setOutput(20, 'perpage');
        $this->setOutput([], 'args');
    }

    public function addAction()
    {
        $this->setOutput($this->_getWordDS()->getTypeMap(), 'typeList');
    }

    public function doaddAction()
    {
        $word = $this->getInput('word', 'post');
        $word['word'] = trim($word['word']);
        if (! $word['word']) {
            $this->showError('WORD:word.empty');
        }
        if (! $word['type']) {
            $this->showError('WORD:type.empty');
        }

        $wordList = explode("\n", $word['word']);
        $wordList = array_unique($wordList);

        $wordService = $this->_getWordService();
        $findWord = $wordService->findWord($wordList);
        if ($findWord) {
            $existWord = implode(',', $findWord);
            $this->showError(['WORD:show.exist.word', ['{showword}' => $existWord]]);
        }

        if ($this->_getWordDS()->isReplaceWord($word['type']) && ! $word['replace']) {
            $this->showError('WORD:replaceword.empty');
        }

        foreach ($wordList as $value) {
            if (! $value) {
                continue;
            }

            $dm = new PwWordDm(); /* @var $dm PwWordDm */
            $dm->setWord($value)->setWordType($word['type']);
            $this->_getWordDS()->isReplaceWord($word['type']) && $dm->setWordReplace(($word['replace'] ? $word['replace'] : '****'));
            $result = $this->_getWordDS()->add($dm);

            if ($result instanceof PwError) {
                $this->showError($result->getError());
            }
        }
        $this->_getWordFilter()->updateCache();
        $this->showMessage('success');
    }

    public function editAction()
    {
        $id = intval($this->getInput('id'));
        if (! $id) {
            $this->showError('WORD:id_not_exist');
        }

        $this->setOutput($this->_getWordDS()->get($id), 'detail');
        $this->setOutput($this->_getWordDS()->getTypeMap(), 'typeList');
    }

    public function doeditAction()
    {
        list($id, $word) = $this->getInput(['id', 'word'], 'post');

        if (! $id) {
            $this->showError('WORD:id_not_exist');
        }

        $word['word'] = trim($word['word']);

        if (! $word['word']) {
            $this->showError('WORD:word.empty');
        }
        if (! $word['type']) {
            $this->showError('WORD:type.empty');
        }

        $wordService = $this->_getWordService();

        if ($wordService->isExistWord($word['word'], $id)) {
            $this->showError('WORD:word.is.exist');
        }

        if ($this->_getWordDS()->isReplaceWord($word['type']) && ! $word['replace']) {
            $this->showError('WORD:replaceword.empty');
        }

        $dm = new PwWordDm($id); /* @var $dm PwWordDm */

        $dm->setWord($word['word'])->setWordType($word['type']);
        $word['replace'] = $this->_getWordDS()->isReplaceWord($word['type']) ? ($word['replace'] ? $word['replace'] : '****') : '';
        $dm->setWordReplace($word['replace']);

        if (($result = $this->_getWordDS()->update($dm))instanceof PwError) {
            $this->showError($result->getError());
        }
        $this->_getWordFilter()->updateCache();
        $this->showMessage('success');
    }

    public function deleteAction()
    {
        $id = intval($this->getInput('id'), 'post');
        if (! $id) {
            $this->showError('WORD:id_not_exist');
        }

        $this->_getWordDS()->delete($id);
        $this->_getWordFilter()->updateCache();
        $this->showMessage('success');
    }

    public function batchdeleteAction()
    {
        list($ids, $checkAll) = $this->getInput(['ids', 'checkall'], 'post');

        if ($checkAll) {
            list($type, $keyword) = $this->getInput(['type', 'keyword']);
            $this->_getWordService()->deleteByCondition($type, $keyword);
            $this->_getWordFilter()->updateCache();
            $this->showMessage('success');
        }

        if (empty($ids) || ! is_array($ids)) {
            $this->showError('WORD:no_operate_object');
        }

        $this->_getWordDS()->batchDelete($ids);
        $this->_getWordFilter()->updateCache();
        $this->showMessage('success');
    }

    public function batcheditAction()
    {
        list($ids, $checkAll) = $this->getInput(['ids', 'checkall']);
        if (empty($ids) || ! is_array($ids)) {
            $this->showError('WORD:no_operate_object');
        }

        $wordList = $this->_getWordDS()->fetch($ids);

        $word = $wordIds = [];
        foreach ($wordList as $key => $value) {
            $word[] = $value['word'];
            $wordIdList[] = $value['word_id'];
        }

        $word = array_unique($word);

        $this->setOutput($word ? implode("\n", $word) : '', 'word');
        $this->setOutput($wordIdList ? implode(',', $wordIdList) : '', 'wordId');
        $this->setOutput($this->_getWordDS()->getTypeMap(), 'typeList');
        $this->setOutput($checkAll, 'checkall');
    }

    public function dobatcheditAction()
    {
        list($word, $checkAll) = $this->getInput(['word', 'checkall'], 'post');

        if ($checkAll) {
            $wordService = $this->_getWordService();
            $word['replace'] = $this->_getWordDS()->isReplaceWord($word['type']) ? ($word['replace'] ? $word['replace'] : '****') : '';
            $this->_getWordDS()->updateAllByTypeAndRelpace($word['type'], $word['replace']);
            $this->showMessage('success');
        }

        $ids = $word['ids'] ? explode(',', $word['ids']) : [];
        $ids = array_unique($ids);

        if (empty($ids) || ! is_array($ids)) {
            $this->showError('operate.fail');
        }

        $wordService = $this->_getWordService();
        if ($this->_getWordDS()->isReplaceWord($word['type']) && ! $word['replace']) {
            $this->showError('WORD:replaceword.empty');
        }

        $dm = new PwWordDm(); /* @var $dm PwWordDm */

        $dm->setWordType($word['type'] ? $word['type'] : 1);
        $word['replace'] && $dm->setWordReplace($word['replace']);

        if (($result = $this->_getWordDS()->batchUpdate($ids, $dm))instanceof PwError) {
            $this->showError($result->getError());
        }
        $this->_getWordFilter()->updateCache();
        $this->showMessage('success');
    }

    public function searchAction()
    {
        list($keyword, $type, $ischeckAll, $page, $perpage) = $this->getInput(['keyword', 'type', '_check', 'page', 'perpage']);

        $page < 1 && $page = 1;

        $perpage = $perpage ? $perpage : 20;

        list($offset, $limit) = Pw::page2limit($page, $perpage);

        $wordSo = new PwWordSo(); /* @var $wordSo PwWordSo */

        $keyword && $wordSo->setWord($keyword);
        $type > 0 && $wordSo->setWordType($type);

        $total = $this->_getWordDS()->countSearchWord($wordSo);
        $wordList = $total ? $this->_getWordDS()->searchWord($wordSo, $limit, $offset) : [];

        $this->setOutput($total, 'total');
        $this->setOutput($wordList, 'wordList');
        $this->setOutput($this->_getWordDS()->getTypeMap(), 'typeList');
        $this->setOutput(Wekit::C($this->_configName), 'config');
        $this->setOutput($page, 'page');
        $this->setOutput('search', 'action');
        $this->setOutput($perpage, 'perpage');
        $this->setOutput($ischeckAll, 'ischeckAll');
        $this->setOutput([
            'keyword' => $keyword,
            'type'    => $type,
            '_check'  => $ischeckAll,
            'perpage' => $perpage,
        ], 'args');

        $this->setTemplate('manage_run');
    }

    public function exportAction()
    {
        $wordService = $this->_getWordService();
        $word = $this->_getWordDS()->fetchAllWord();

        $content = '';
        foreach ($word as $value) {
            $content .= sprintf('%s|%s', $value['word'], $value['word_type']);
            $content .= $this->_getWordDS()->isReplaceWord($value['word_type']) ? sprintf('|%s', $value['word_replace']) : '';
            $content .= "\r\n";
        }

        $filename = sprintf('%s.txt', 'PwFilterWord');
        header('Last-Modified: '.gmdate('D, d M Y H:i:s', Pw::getTime() + 86400).' GMT');
        header('Cache-control: no-cache');
        header('Content-Encoding: none');
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        header('Content-type: txt');
        header('Content-Length: '.strlen($content));
        echo $content;
        exit;
    }

    public function importAction()
    {
    }

    public function doimportAction()
    {
        $bhv = new PwWordUpload();
        $upload = new PwUpload($bhv);

        if (($result = $upload->check()) === true) {
            $result = $upload->execute();
        }

        if ($result !== true) {
            $error = $result->getError();
            if (is_array($error)) {
                list($error) = $error;
                if ($error == 'upload.ext.error') {
                    $this->showError('WORD:ext.error');
                }
            }

            $this->showError($result->getError());
        }

        $source = $bhv->getAbsoluteFile();

        if (! WindFile::isFile($source)) {
            $this->showError('operate.fail');
        }

        $content = WindFile::read($source);

        pw::deleteAttach($bhv->dir.$bhv->filename, 0, $bhv->isLocal);
        $content = explode("\n", $content);

        if (! $content) {
            $this->showError('WORD:import.data.empty');
        }

        $wordService = $this->_getWordService();
        $typeMap = $this->_getWordDS()->getTypeMap();

        foreach ($content as $value) {
            list($word, $type, $replace) = $this->_parseTextUseInImport($value, $typeMap);

            if (! $word || ! $type || ($wordService->isExistWord($word))) {
                continue;
            }

            $dm = new PwWordDm(); /* @var $dm PwWordDm */
            $dm->setWord($word)->setWordType($type);
            $replace = $this->_getWordDS()->isReplaceWord($type) ? ($replace ? $replace : '****') : '';
            $dm->setWordReplace($replace);

            $this->_getWordDS()->add($dm);
        }
        $this->_getWordFilter()->updateCache();
        $this->showMessage('success');
    }

    public function setconfigAction()
    {
        $config = $this->getInput('config');

        $configService = new PwConfigSet($this->_configName);
        $configService->set('istip', intval($config['tip']))->flush();

        $this->showMessage('success');
    }

    public function _parseTextUseInImport($text, $typeMap)
    {
        list($word, $type, $replace) = explode('|', $text);

        $word = trim($word);
        $type = in_array($type, array_keys($typeMap)) ? $type : 1;
        $replace = trim($replace);

        return [$word, $type, $replace];
    }

    private function _syncHelper()
    {
        $syncStatus = $this->_getWordSyncService()->status;

        $this->setOutput($syncStatus, 'syncStatus');

        if (! $syncStatus) {
            return false;
        }

        $this->_getWordSyncService()->setSyncType(($this->_getWordDS()->countByFrom(1) ? 'increase' : 'all'));

        $this->setOutput([
                        'lasttime' => $this->_getWordSyncService()->lastTimeFromPlatform,
                        'syncnum'  => $this->_getWordSyncService()->getSyncNum(),
        ], 'sync');

        return true;
    }

    /**
     * get PwWordService.
     *
     * @return PwWordService
     */
    private function _getWordService()
    {
        return Wekit::load('word.srv.PwWordService');
    }

    /**
     * get PwWordFilter.
     *
     * @return PwWordFilter
     */
    private function _getWordFilter()
    {
        return Wekit::load('word.srv.PwWordFilter');
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
