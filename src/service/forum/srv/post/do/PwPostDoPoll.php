<?php

defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('SRV:forum.srv.post.do.PwPostDoBase');

/**
 * 帖子发布-投票帖 相关服务
 *
 * @author MingXing Sun <mingxing.sun@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwPostDoPoll.php 23975 2013-01-17 10:20:11Z jieyin $
 * @package forum
 */

class PwPostDoPoll extends PwPostDoBase
{
    public $tid = null;
    public $poll = array();

    public $action;
    public $post_max_size;

    public $user = null;
    public $info = array();

    public function __construct(PwPost $pwpost, $tid = null, $poll = array())
    {
        $this->user = $pwpost->user;

        $this->tid = $tid ? $tid : null;
        $this->poll = $poll;

        $this->action = $this->tid ? 'modify' : 'add';
        $this->post_max_size = @ini_get('post_max_size');
        $this->max_file_uploads = @ini_get('max_file_uploads');
    }

    /**
     * 添加投票与投票关系内容
     */
    public function addThread($tid)
    {
        return $this->addPoll($tid);
    }

    /**
     * 更新投票内容
     */
    public function updateThread($tid)
    {
        return $this->updatePoll($tid);
    }

    /**
     * 设置检查
     */
    public function check($postDm)
    {
        if ($this->action == 'add' && !$this->user->getPermission('allow_add_vote')) {
            return new PwError('VOTE:group.permission.add', array('{grouptitle}' => $this->user->getGroupInfo('name')));
        }
        if (($result = $this->_checkPoll()) !== true) {
            return $result;
        }

        return true;
    }

    public function addPoll($tid)
    {
        if (($attachInfo = $this->uploadOptionImage()) instanceof PwError) {
            return $attachInfo;
        }

        $pollData = $this->poll['poll'];
        $optionData = $this->poll['option'];

        Wind::import('SRV:poll.dm.PwPollDm');

        $pollDm = new PwPollDm(); /* @var $pwPollDm PwPollDm */
        $pollDm->setIsViewResult($pollData['isviewresult']);
        $pollDm->setOptionLimit($pollData['optionlimit']);
        $pollDm->setCreatedUserid($this->user->uid);
        $pollData['regtimelimit'] && $pollDm->setRegtimeLimit(pw::str2time($pollData['regtimelimit']));
        $expiredTime = $pollData['expiredday'] ? intval($pollData['expiredday']) * 86400 + pw::getTime() : 0;
        $pollDm->setExpiredTime($expiredTime);
        $optinNum = $pollData['ismultiple'] ? count($optionData) : 0;
        $pollDm->setOptionLimit(min($optinNum, $pollData['optionlimit']));
        $attachInfo && $pollDm->setIsIncludeImg(1);

        $newPollid = $this->_getPollDS()->addPoll($pollDm);

        Wind::import('SRV:poll.dm.PwPollOptionDm');

        foreach ($optionData as $key => $value) {
            if (!$value) {
                continue;
            }
            $dm = new PwPollOptionDm(); /* @var $pwPollDm PwPollDm */
            $image = isset($attachInfo['optionpic'][$key]) ? $attachInfo['optionpic'][$key]['path'] : '';
            $dm->setContent($value)->setPollid($newPollid)->setImage($image);
            $this->_getPollOptionDS()->add($dm);
        }

        Wind::import('SRV:poll.dm.PwThreadPollDm');

        $threadPollDm = new PwThreadPollDm(); /* @var $threadPollDm PwThreadPollDm */
        $threadPollDm->setTid($tid)->setPollid($newPollid)->setCreatedUserid($this->user->uid);

        $this->_getThreadPollDS()->addPoll($threadPollDm);

        $this->_afterUpdate($newPollid);

        return true;
    }

    public function updatePoll($tid)
    {
        $this->info = $this->getThreadPollBo()->info;
        if ($this->info['poll']['voter_num']) {
            return true;
        }
        if (($attachInfo = $this->uploadOptionImage()) instanceof PwError) {
            return $attachInfo;
        }

        $pollData = $this->poll['poll'];

        Wind::import('SRV:poll.dm.PwPollDm');

        $pollDm = new PwPollDm($this->info['poll_id']); /* @var $pwPollDm PwPollDm */
        $pollDm->setIsViewResult($pollData['isviewresult']);
        $pollDm->setOptionLimit($pollData['optionlimit']);
        $pollDm->setRegtimeLimit($pollData['regtimelimit'] ? pw::str2time($pollData['regtimelimit']) : 0);
        $expiredTime = $pollData['expiredday'] ? intval($pollData['expiredday']) * 86400 + $this->info['poll']['created_time'] : 0;
        $pollDm->setExpiredTime($expiredTime);
        $optinNum = $pollData['ismultiple'] ? $this->info['poll']['optionnum'] + count($this->poll['newoption']) : 0;
        $pollDm->setOptionLimit(min($optinNum, $pollData['optionlimit']));
        $attachInfo && $pollDm->setIsIncludeImg(1);

        $this->_getPollDS()->updatePoll($pollDm);
        $this->_updatePollOption($attachInfo);

        return true;
    }

    private function _updatePollOption($attachInfo)
    {
        $optionInfo = $this->info['option'];
        $optionData = $this->poll['option'];

        Wind::import('SRV:poll.dm.PwPollOptionDm');

        $deleteIds = array();
        foreach (array_keys($optionInfo) as $_id) {
            $attach = isset($attachInfo['optionpic'][$_id]) ? $attachInfo['optionpic'][$_id] : '';
            $optionContent = trim($optionData[$_id]);
            $isUpdate = ($optionInfo[$_id]['content'] != $optionContent || $attach) ? true : false;

            !$optionContent && $deleteIds[] = $_id;

            if (!($isUpdate && $optionContent)) {
                continue;
            }

            $dm = new PwPollOptionDm($_id); /* @var $pwPollDm PwPollDm */
            $optionInfo[$_id]['content'] != $optionData[$_id] && $dm->setContent($optionData[$_id]);

            if ($attach) {
                $dm->setImage($attach['path']);
                $optionImgPath = $optionInfo[$_id]['image'];
                $optionImgPath && $this->_getPollService()->removeImg($optionImgPath);
            }

            $this->_getPollOptionDS()->update($dm);
        }

        if ($deleteIds) {
            $this->_getPollOptionDS()->batchDelete($deleteIds);
        }

        foreach ((array) $this->poll['newoption'] as $key => $value) {
            if (!$value) {
                continue;
            }
            $dm = new PwPollOptionDm(); /* @var $pwPollDm PwPollDm */
            $image = isset($attachInfo['newoptionpic'][$key]) ? $attachInfo['newoptionpic'][$key]['path'] : '';
            $dm->setContent($value)->setPollid($this->info['poll_id'])->setImage($image);
            $this->_getPollOptionDS()->add($dm);
        }

        $this->_afterUpdate($this->info['poll_id']);

        return true;
    }


    private function _afterUpdate($pollid)
    {
        $optionList = $this->_getPollOptionDS()->getByPollid($pollid);
        if (!$optionList) {
            return false;
        }

        $flag = false;
        foreach ($optionList as $value) {
            if (!$value['image']) {
                continue;
            }
            $flag = true;
        }

        Wind::import('SRV:poll.dm.PwPollDm');
        $dm = new PwPollDm($pollid);
        $dm->setIsIncludeImg($flag ? 1 : 0);
        $this->_getPollDs()->updatePoll($dm);

        return true;
    }

    /**
     * 上次投票项图片
     *
     */
    public function uploadOptionImage()
    {
        Wind::import('SRV:upload.action.PwPollUpload');

        $bhv = new PwPollUpload($this->user);

        $upload = new PwUpload($bhv);
        if (($result = $upload->check()) === true) {
            $result = $upload->execute();
        }
        if ($result !== true) {
            return $result == false ? new PwError('operate.fail') : $result;
        }

        return $bhv->getAttachInfo();
    }

    /**
     * 投票验证
     *
     * @return bool
     */
    private function _checkPoll()
    {
        switch ($this->action) {
            case 'modify':
                return $this->_checkInModify();
                break;
            case 'add':
                return $this->_checkInAdd();
                break;
        }

        return true;
    }

    private function _checkInModify()
    {
        $this->info = $this->getThreadPollBo()->info;
        if (!$this->info) {
            return new PwError('VOTE:thread.not.exist');
        }
        if ($this->info['poll']['voter_num']) {
            return true;
        }

        $option = array_merge($this->poll['option'], $this->poll['newoption']);
        $reulst = array();
        foreach ($option as $value) {
            $value = trim($value);
            if (!$value) {
                continue;
            }
            $reulst[] = $value;
        }

        $optionNum = count($reulst);

        if ($optionNum < 2) {
            return new PwError('VOTE:options.illegal');
        }
        if ($optionNum != count(array_unique($reulst))) {
            return new PwError('VOTE:options.repeat');
        }

        return true;
    }

    private function _checkInAdd()
    {
        $option = $this->poll['option'];
        $reulst = array();
        foreach ($option as $value) {
            $value = trim($value);
            if (!$value) {
                continue;
            }
            $reulst[] = $value;
        }

        $optionNum = count($reulst);
        if ($optionNum < 2) {
            return new PwError('VOTE:options.illegal');
        }
        if ($optionNum != count(array_unique($reulst))) {
            return new PwError('VOTE:options.repeat');
        }

        return true;
    }

    public function createHtmlBeforeContent()
    {
        PwHook::template('displayPostPollHtml', 'TPL:bbs.post_poll', true, $this);
    }

    public function dataProcessing($postDm)
    {
        $postDm->setSpecial('poll');

        return $postDm;
    }

    /**
     * get PwThreadPollBo
     *
     * @return PwThreadPollBo
     */
    public function getThreadPollBo()
    {
        static $_instance = null;

        if ($_instance == null) {
            Wind::import('SRV:poll.bo.PwThreadPollBo');
            $_instance = new PwThreadPollBo($this->tid);
        }

        return $_instance;
    }

    /**
     * get PwPollService
     *
     * @return PwPollService
     */
    private function _getPollService()
    {
        return Wekit::load('poll.srv.PwPollService');
    }

    /**
     * get PwPoll
     *
     * @return PwPoll
     */
    private function _getPollDS()
    {
        return Wekit::load('poll.PwPoll');
    }

    /**
     * get PwPollOption
     *
     * @return PwPollOption
     */
    private function _getPollOptionDS()
    {
        return Wekit::load('poll.PwPollOption');
    }

    /**
     * get PwThreadPoll
     *
     * @return PwThreadPoll
     */
    private function _getThreadPollDS()
    {
        return Wekit::load('poll.PwThreadPoll');
    }
}
