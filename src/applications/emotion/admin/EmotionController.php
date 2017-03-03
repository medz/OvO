<?php

Wind::import('ADMIN:library.AdminBaseController');
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>.
 *
 * @author $Author$ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id$
 */
class EmotionController extends AdminBaseController
{
    public function run()
    {
        $folderList = $this->_getEmotionService()->getFolderList();
        $catList = $this->_getEmotionCategoryDs()->getCategoryList();
        foreach ($catList as $k => $cat) {
            /*$_apps = explode('|', $cat['emotion_apps']);
            $_appName = '';
            foreach ((array)$_apps AS $_app) {
                $_appName .= $this->_getEmotionService()->getAppcationList($_app) .',';
            }
            $catList[$k]['apps'] = $_apps;
            $catList[$k]['appsname'] = $_appName;*/
            if (Pw::inArray($cat['emotion_folder'], $folderList)) {
                foreach ($folderList as $key => $folder) {
                    if ($cat['emotion_folder'] == $folder) {
                        unset($folderList[$key]);
                    }
                }
            }
        }

        $this->setOutput($this->_getEmotionService()->getAppcationList(), 'appList');
        $this->setOutput($catList, 'catList');
        $this->setOutput($folderList, 'folderList');
    }

    public function dorunAction()
    {
        $this->getRequest()->isPost() || $this->showError('operate.fail');

        $isopens = $this->getInput('isopen', 'post');
        $catids = $this->getInput('catid', 'post');
        is_int($catids) && $catids = array($catids);
        $orderIds = $this->getInput('category_orderid', 'post');
        $catnames = $this->getInput('category_name', 'post');
        //$apps = $this->getInput('apps','post');
        if (!$catids) {
            $this->showError('ADMIN:fail');
        }
         
        foreach ($catids as $k => $v) {
            if (!$catnames[$v]) {
                $this->showError('ADMIN:catname.empty');
            }
            $dm = new PwEmotionCategoryDm($v);
            $dm->setCategoryMame($catnames[$v])
                ->setEmotionApps(array('bbs'))
                ->setOrderId($orderIds[$v])
                ->setIsopen($isopens[$v]);
            $this->_getEmotionCategoryDs()->updateCategory($dm);
        }
        $this->showMessage('MEDAL:success');
    }

    public function doaddAction()
    {
        $this->getRequest()->isPost() || $this->showError('operate.fail');

         
        $dm = new PwEmotionCategoryDm();
        $dm->setCategoryMame($this->getInput('catname', 'post'))
            ->setEmotionFolder($this->getInput('folder', 'post'))
            ->setEmotionApps(array('bbs'))
            ->setOrderId((int) $this->getInput('orderid', 'post'))
            ->setIsopen(1);
        $resource = $this->_getEmotionCategoryDs()->addCategory($dm);
        if ($resource instanceof PwError) {
            $this->showError($resource->getError());
        }
        $this->showMessage('MEDAL:success');
    }

    public function deletecateAction()
    {
        $cateId = (int) $this->getInput('cateid', 'post');
        if (!$cateId) {
            $this->showError('operate.fail');
        }

        $this->_getEmotionCategoryDs()->deleteCategory($cateId);
        $this->_getEmotionDs()->deleteEmotionByCatid($cateId);

        $this->showMessage('success', 'emotion/emotion/run/', true);
    }

    public function emotionAction()
    {
        $catId = (int) $this->getInput('catid', 'get');
        $category = $this->_getEmotionCategoryDs()->getCategory($catId);
        if (!$folder = $category['emotion_folder']) {
            $this->showError('ADMIN:fail');
        }
        $emotionList = $this->_getEmotionDs()->getListByCatid($catId);
        $folderEmotion = $this->_getEmotionService()->getFolderIconList($folder);
        foreach ($emotionList as $key => $emotion) {
            $emotionList[$key]['sign'] = '[s:'.($emotion['emotion_name'] ? $emotion['emotion_name'] : $emotion['emotion_id']).']';
            foreach ($folderEmotion as $k => $val) {
                if ($emotion['emotion_icon'] == $val) {
                    unset($folderEmotion[$k]);
                }
            }
        }
        $url = Wekit::getGlobal('url', 'res').'/images/emotion/';
        $this->setOutput($emotionList, 'emotionList');
        $this->setOutput($folderEmotion, 'folderEmotion');
        $this->setOutput($folder, 'folder');
        $this->setOutput($catId, 'catid');
        $this->setOutput($url, 'iconUrl');
    }

    public function dobatchaddAction()
    {
        $emotionIds = $this->getInput('emotionid', 'post');
        is_int($emotionIds) && $emotionIds = array($emotionIds);
        $emotionNames = $this->getInput('emotionname', 'post');
        $icons = $this->getInput('icon', 'post');
        $orderIds = $this->getInput('orderid', 'post');
        $catId = (int) $this->getInput('catid', 'post');
        $category = $this->_getEmotionCategoryDs()->getCategory($catId);
        if (!$folder = $category['emotion_folder']) {
            $this->showError('ADMIN:fail');
        }
         
        foreach ($emotionIds as $v => $vv) {
            if (!$icons[$v]) {
                continue;
            }
            $dm = new PwEmotionDm();
            $dm->setCategoryId($catId)
                ->setEmotionFolder($folder)
                ->setEmotionName($emotionNames[$v])
                ->setEmotionIcon($icons[$v])
                ->setVieworder($orderIds[$v]);
            $this->_getEmotionDs()->addEmotion($dm);
        }
        $this->showMessage('MEDAL:success');
    }

    public function dobatcheditAction()
    {
        $emotionIds = $this->getInput('emotionid', 'post');
        is_int($emotionIds) && $emotionIds = array($emotionIds);
        $emotionNames = $this->getInput('emotionname', 'post');
        $orderIds = $this->getInput('orderid', 'post');
        $isuseds = $this->getInput('isused', 'post');
         
        foreach ($emotionIds as $k => $v) {
            $dm = new PwEmotionDm($emotionIds[$k]);
            $dm->setEmotionName($emotionNames[$k])
                ->setVieworder($orderIds[$k])
                ->setIsused($isuseds[$k]);
            $this->_getEmotionDs()->updateEmotion($dm);
        }
        $this->showMessage('MEDAL:success');
    }

    public function dousedAction()
    {
        $emotionId = (int) $this->getInput('emotionid', 'post');
        $used = (int) $this->getInput('used', 'post');
        if ($emotionId < 1) {
            $this->showError('ADMIN:fail');
        }
        $used = $used > 0 ? 1 : 0;
         
        $dm = new PwEmotionDm($emotionId);
        $dm->setIsused($used);
        $resource = $this->_getEmotionDs()->updateEmotion($dm);
        if ($resource instanceof PwError) {
            $this->showError($resource->getError());
        }
        $this->showMessage('MEDAL:success');
    }

    private function _getEmotionService()
    {
        return Wekit::load('emotion.srv.PwEmotionService');
    }

    private function _getEmotionDs()
    {
        return Wekit::load('emotion.PwEmotion');
    }

    private function _getEmotionCategoryDs()
    {
        return Wekit::load('emotion.PwEmotionCategory');
    }
}
