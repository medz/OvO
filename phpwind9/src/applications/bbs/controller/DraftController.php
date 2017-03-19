<?php


/**
 * 草稿箱.
 *
 * @author jinlong.panjl <jinlong.panjl@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id$
 */
class DraftController extends PwBaseController
{
    private $maxNum = 10;

    public function beforeAction($handlerAdapter)
    {
        parent::beforeAction($handlerAdapter);
        if ($this->loginUser->uid < 1) {
            $this->showError('BBS:draft.user.not.login');
        }
    }

    /**
     * 添加草稿
     */
    public function doAddAction()
    {
        list($title, $content) = $this->getInput(['atc_title', 'atc_content'], 'post');
        if (!$title || !$content) {
            $this->showError('BBS:draft.content.empty');
        }
        if ($this->_getDraftDs()->countByUid($this->loginUser->uid) >= $this->maxNum) {
            $this->showError('BBS:draft.num.max');
        }
        $draftDm = new PwDraftDm();
        $draftDm->setTitle($title)
                ->setContent($content)
                ->setCreatedUserid($this->loginUser->uid)
                ->setCreatedTime(PW::getTime());
        $this->_getDraftDs()->addDraft($draftDm);
        $this->showMessage('success');
    }

    /**
     * do删除.
     */
    public function doDeleteAction()
    {
        $id = (int) $this->getInput('id', 'post');
        if (!$id) {
            $this->showError('operate.fail');
        }

        $draft = $this->_getDraftDs()->getDraft($id);
        if ($draft['created_userid'] != $this->loginUser->uid) {
            $this->showError('BBS:draft.operater.error');
        }
        $this->_getDraftDs()->deleteDraft($id, $this->loginUser->uid);
        $this->showMessage('success');
    }

    /**
     * 发帖页我的草稿
     */
    public function myDraftsAction()
    {
        $drafts = $this->_getDraftDs()->getByUid($this->loginUser->uid, $this->maxNum);
        $data = [];
        foreach ($drafts as $v) {
            $_tmp['id'] = $v['id'];
            $_tmp['title'] = $v['title'];
            $_tmp['content'] = $v['content'];
            $_tmp['created_time'] = Pw::time2str($v['created_time'], 'auto');
            $data[] = $_tmp;
        }
        Pw::echoJson(['state' => 'success', 'data' => $data]);
        exit;
    }

    /**
     * 草稿DS.
     *
     * @return PwDraft
     */
    protected function _getDraftDs()
    {
        return Wekit::load('draft.PwDraft');
    }
}
