<?php

/**
 * 附件操作.
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @license http://www.phpwind.com
 *
 * @version $Id: AttachController.php 28798 2013-05-24 06:20:13Z jieyin $
 */
class AttachController extends PwBaseController
{
    public function run()
    {
    }

    public function downloadAction()
    {
        $aid = (int) $this->getInput('aid', 'get');
        $submit = (int) $this->getInput('submit', 'post');
        $attach = Wekit::load('attach.PwThreadAttach')->getAttach($aid);
        if (!$attach) {
            $this->showError('BBS:thread.buy.attach.error');
        }
         
        $forum = new PwForumBo($attach['fid']);
        if (!$forum->isForum()) {
            $this->showError('data.error');
        }
        if ($attach['cost'] && !$this->loginUser->isExists()) {
            $this->showError('download.fail.login.not', 'bbs/attach/download');
        }
        if (!$forum->allowDownload($this->loginUser)) {
            if (!$this->loginUser->isExists()) {
                $this->showError('download.fail.login.not', 'bbs/attach/download');
            }
            $this->showError(array('BBS:forum.permissions.download.allow', array('{grouptitle}' => $this->loginUser->getGroupInfo('name'))));
        }
        if (!$forum->foruminfo['allow_download'] && !$this->loginUser->getPermission('allow_download')) {
            if (!$this->loginUser->isExists()) {
                $this->showError('download.fail.login.not', 'bbs/attach/download');
            }
            $this->showError(array('permission.download.allow', array('{grouptitle}' => $this->loginUser->getGroupInfo('name'))));
        }
         
        $creditBo = PwCreditBo::getInstance();
        // 购买积分检查
        if (($attach = $this->_checkAttachCost($attach)) instanceof PwError) {
            $this->showError($attach->getError());
        }
        // 下载积分检查
        if (($reduceDownload = $this->_checkAttachDownload('download_att', $attach, $forum)) instanceof PwError) {
            $this->showError($reduceDownload->getError());
        }

        //下载积分提示
        $lang = Wind::getComponent('i18n');
        if (1 == $this->loginUser->getPermission('allow_download') && $reduceDownload && $attach['cost']) {
            $dataShow = $lang->getMessage('BBS:thread.attachbuy.message.all', array('{buyCount}' => -$attach['cost'].$creditBo->cType[$attach['ctype']], '{downCount}' => rtrim($reduceDownload, ',')));
        } elseif (1 == $this->loginUser->getPermission('allow_download') && $reduceDownload && !$attach['cost']) {
            $dataShow = $lang->getMessage('BBS:thread.attachbuy.message.download', array('{downCount}' => rtrim($reduceDownload, ',')));
        } elseif ($attach['cost']) {
            $dataShow = $lang->getMessage('BBS:thread.attachbuy.message.buy', array('{count}' => $this->loginUser->getCredit($attach['ctype']).$creditBo->cType[$attach['ctype']], '{buyCount}' => -$attach['cost'].$creditBo->cType[$attach['ctype']]));
        } else {
            $dataShow = $lang->getMessage('BBS:thread.attachbuy.message.success');
        }
        !$submit && $this->showMessage($dataShow);

        //购买积分操作
        $this->_operateBuyCredit($attach);

        // 下载积分
        if ($reduceDownload) {
             
            $dm = new PwThreadAttachBuyDm();
            $dm->setAid($aid)
                ->setCreatedUserid($this->loginUser->uid)
                ->setCreatedTime(Pw::getTime())
                ->setCtype($attach['ctype'])
                ->setCost($attach['cost']);
            Wekit::load('attach.PwThreadAttachDownload')->add($dm);
            $this->_operateCredit('download_att', $forum);
        }
        //更新附件点击数
         
        $dm = new PwThreadAttachDm($aid);
        $dm->addHits(1);
        Wekit::load('attach.PwThreadAttach')->updateAttach($dm);

        $filename = basename($attach['path']);
        $fileext = substr(strrchr($attach['path'], '.'), 1);
        $filesize = 0;
        if (strpos(Wind::getApp()->getRequest()->getServer('HTTP_USER_AGENT'), 'MSIE') !== false && $fileext == 'torrent') {
            $attachment = 'inline';
        } else {
            $attachment = 'attachment';
        }
        $attach['name'] = trim(str_replace('&nbsp;', ' ', $attach['name']));
        if (strtoupper(Wekit::V('charset')) == 'UTF-8') {
            $attach['name'] = Pw::convert($attach['name'], 'gbk', 'utf-8');
        }

        $filesize = 0;
        $fgeturl = Wind::getComponent('storage')->getDownloadUrl($attach['path']);

        if (strpos($fgeturl, 'http') !== 0) {
            if (!is_readable($fgeturl)) {
                $this->showError('BBS:thread.buy.attach.error');
            }
            $filesize = filesize($fgeturl);
        }

        $timestamp = Pw::getTime();
        $ctype = '';
        switch ($fileext) {
            case 'pdf':
                $ctype = 'application/pdf';
                break;
            case 'rar':
            case 'zip':
                $ctype = 'application/zip';
                break;
            case 'doc':
                $ctype = 'application/msword';
                break;
            case 'xls':
                $ctype = 'application/vnd.ms-excel';
                break;
            case 'ppt':
                $ctype = 'application/vnd.ms-powerpoint';
                break;
            case 'gif':
                $ctype = 'image/gif';
                break;
            case 'png':
                $ctype = 'image/png';
                break;
            case 'jpeg':
            case 'jpg':
                $ctype = 'image/jpeg';
                break;
            case 'wav':
                $ctype = 'audio/x-wav';
                break;
            case 'mpeg':
            case 'mpg':
            case 'mpe':
                $ctype = 'video/x-mpeg';
                break;
            case 'mov':
                $ctype = 'video/quicktime';
                break;
            case 'avi':
                $ctype = 'video/x-msvideo';
                break;
            case 'txt':
                $ctype = 'text/plain';
                break;
            default:
                $ctype = 'application/octet-stream';
        }
        ob_end_clean();
        header('Last-Modified: '.gmdate('D, d M Y H:i:s', $timestamp + 86400).' GMT');
        header('Expires: '.gmdate('D, d M Y H:i:s', $timestamp + 86400).' GMT');
        header('Cache-control: max-age=86400');
        header('Content-Encoding: none');
        header("Content-Disposition: $attachment; filename=\"{$attach['name']}\"");
        header("Content-type: $ctype");
        header('Content-Transfer-Encoding: binary');
        $filesize && header("Content-Length: $filesize");
        $i = 1;
        while (!@readfile($fgeturl)) {
            if (++$i > 3) {
                break;
            }
        }
        exit();
    }

    public function deleteAction()
    {
        $aid = $this->getInput('aid', 'post');
        if (!$aid) {
            $this->showError('operate.fail');
        }
        if (!$attach = Wekit::load('attach.PwThreadAttach')->getAttach($aid)) {
            $this->showError('data.error');
        }

         
        $forum = new PwForumBo($attach['fid']);
        if (!$forum->isForum()) {
            $this->showError('data.error');
        }

        if ($this->loginUser->uid != $attach['created_userid']) {
            if (!$this->loginUser->getPermission('operate_thread.deleteatt', $forum->isBM($this->loginUser->username))) {
                $this->showError('permission.attach.delete.deny');
            }
            if (!$this->loginUser->comparePermission($attach['created_userid'])) {
                $this->showError(array('permission.level.deleteatt', array('{grouptitle}' => $this->loginUser->getGroupInfo('name'))));
            }
        }

        Wekit::load('attach.PwThreadAttach')->deleteAttach($aid);
        Pw::deleteAttach($attach['path'], $attach['ifthumb']);
        if ($this->loginUser->uid != $attach['created_userid']) {
            Wekit::load('log.srv.PwLogService')->addDeleteAtachLog($this->loginUser, $attach);
        }

        if ($attach['tid']) {
            if (!$attach['pid']) {
                $thread = Wekit::load('forum.PwThread')->getThread($attach['tid'], PwThread::FETCH_ALL);
                 
                $dm = new PwTopicDm($attach['tid']);
                if (!Wekit::load('attach.PwThreadAttach')->countType($attach['tid'], 0, $attach['type'])) {
                    $dm->setHasAttach($attach['type'], false);
                }
                if (!Pw::getstatus($thread['tpcstatus'], PwThread::STATUS_OPERATORLOG) && $this->loginUser->uid != $attach['created_userid']) {
                    $dm->setOperatorLog(true);
                }
            } else {
                $thread = Wekit::load('forum.PwThread')->getPost($attach['pid']);
                 
                $dm = new PwReplyDm($attach['pid']);
            }
            if ($thread['aids'] > 0) {
                $thread['aids']--;
            }
            $dm->setAids($thread['aids']);
            if (($content = str_replace('[attachment='.$aid.']', '', $thread['content'])) != $thread['content']) {
                $dm->setContent($content);
            }
            if (!$attach['pid']) {
                Wekit::load('forum.PwThread')->updateThread($dm);
            } else {
                Wekit::load('forum.PwThread')->updatePost($dm);
            }
        }

        $this->showMessage('success');
    }

    public function recordAction()
    {
        list($aid, $page) = $this->getInput(array('aid', 'page'));
        $perpage = 10;
        $page < 1 && $page = 1;

        list($offset, $limit) = Pw::page2limit($page, $perpage);
        $count = Wekit::load('attach.PwThreadAttachBuy')->countByAid($aid);
        if (!$count) {
            $this->showError('BBS:thread.buy.error.norecord');
        }
         
        $record = Wekit::load('attach.PwThreadAttachBuy')->getByAid($aid, $limit, $offset);
        !$record && $this->showError('BBS:thread.buy.error.norecord');
        $users = Wekit::load('user.PwUser')->fetchUserByUid(array_keys($record));

        $data = array();
        $cType = PwCreditBo::getInstance()->cType;
        foreach ($record as $key => $value) {
            $data[] = array(
                'uid'          => $value['created_userid'],
                'username'     => $users[$value['created_userid']]['username'],
                'cost'         => $value['cost'],
                'ctype'        => $cType[$value['ctype']],
                'created_time' => Pw::time2str($value['created_time']),
            );
        }

        $totalpage = ceil($count / $perpage);
        $nextpage = $page + 1;
        $nextpage = $nextpage > $totalpage ? $totalpage : $nextpage;

        $this->setOutput(array('data' => $data, 'totalpage' => $totalpage, 'page' => $nextpage), 'data');
        $this->showMessage('success');
    }

    private function _getDownloadCredit($operation, PwUserBo $user, PwCreditBo $creditBo, $creditset = array())
    {
        $strategy = $creditBo->getStrategy($operation);
        if ($this->_checkCreditSetEmpty($strategy['credit']) && $this->_checkCreditSetEmpty($creditset['credit'])) {
            return false;
        }

        //如果外部有积分设置传入则使用外部的积分设置策略
        if (!empty($creditset['limit']) || ($creditset['credit'] && false === $this->_checkCreditSetEmpty($creditset['credit']))) {
            $strategy['limit'] = $creditset['limit'];
            $strategy['credit'] = $creditset['credit'];
        }
        if ($strategy['limit']) {
            $count = $creditBo->getOperateCount($user->uid, $operation);
            if ($count >= $strategy['limit']) {
                return false;
            }
        }

        return $strategy['credit'];
    }

    private function _checkCreditSetEmpty($credit)
    {
        foreach ($credit as $key => $value) {
            if ($value) {
                return false;
            }
        }

        return true;
    }

    /**
     * 检查购买积分.
     */
    protected function _checkAttachCost($attach)
    {
        if (!$attach['cost']) {
            return $attach;
        }
        $user = Wekit::getLoginUser();
        if ($attach['created_userid'] == $user->uid) {
            $attach['cost'] = 0;

            return $attach;
        }
        $attachbuy = Wekit::load('attach.PwThreadAttachBuy');
        if (!$attachbuy->getByAidAndUid($attach['aid'], $user->uid)) {
            $myCredit = $user->getCredit($attach['ctype']);
            if ($attach['cost'] > $myCredit) {
                 
                $creditBo = PwCreditBo::getInstance();
                $creditType = $creditBo->cType[$attach['ctype']];

                return new PwError('BBS:thread.buy.error.credit.notenough', array('{myCredit}' => $myCredit.$creditType, '{count}' => -$attach['cost'].$creditType));
            }
        } else {
            $attach['cost'] = 0;
        }

        return $attach;
    }

    /**
     * 下载购买积分.
     */
    protected function _checkAttachDownload($operate, $attach, PwForumBo $forum)
    {
        $user = Wekit::getLoginUser();
        if (1 != $user->getPermission('allow_download')) {
            return false;
        }
         
        $creditBo = PwCreditBo::getInstance();
        $forumCredit = $forum->getCreditSet($operate);
        $downloadCredit = $this->_getDownloadCredit($operate, $user, $creditBo, $forumCredit);
        if (!$downloadCredit) {
            return false;
        }
        if (!$user->isExists()) {
            return new PwError('download.fail.login.not');
        }
        $attachdownload = Wekit::load('attach.PwThreadAttachDownload');
        $ifDown = $attachdownload->getByAidAndUid($attach['aid'], $user->uid);
        if ($ifDown) {
            return false;
        }
        $reduceDownload = '';
        foreach ($downloadCredit as $k => $v) {
            $tv = $v;
            ($attach['ctype'] == $k) && $tv = $v - $attach['cost'];
            $vt = $tv > 0 ? '+'.$tv : $tv;
            if (-$tv > $user->getCredit($k)) {
                return new PwError('BBS:thread.download.error.credit.notenough', array('{myCredit}' => $this->loginUser->getCredit($k).$creditBo->cType[$k], '{count}' => $vt.$creditBo->cType[$k]));
            }
            $v && $reduceDownload .= ($v > 0 ? '+'.abs($v) : $v).$creditBo->cType[$k].',';
        }

        return $reduceDownload;
    }

    /**
     * 更新积分.
     */
    protected function _operateCredit($operate, PwForumBo $forum)
    {
         
        $credit = PwCreditBo::getInstance();
        $user = Wekit::getLoginUser();
        $credit->operate($operate, $user, true, array('forumname' => $forum->foruminfo['name']), $forum->getCreditSet($operate));
        $credit->execute();
    }

    protected function _operateBuyCredit($attach)
    {
        $user = Wekit::getLoginUser();
        if (!$attach['cost'] || $attach['created_userid'] == $user->uid) {
            return false;
        }
         
        $creditBo = PwCreditBo::getInstance();
         
        $dm = new PwThreadAttachBuyDm();
        $dm->setAid($attach['aid'])
            ->setCreatedUserid($user->uid)
            ->setCreatedTime(Pw::getTime())
            ->setCtype($attach['ctype'])
            ->setCost($attach['cost']);
        Wekit::load('attach.PwThreadAttachBuy')->add($dm);

        $creditBo->addLog('attach_buy', array($attach['ctype'] => -$attach['cost']), $user, array(
            'name' => $attach['name'],
        ));
        $creditBo->set($user->uid, $attach['ctype'], -$attach['cost'], true);

        $user = new PwUserBo($attach['created_userid']);
        if (($max = $user->getPermission('sell_credit_range.maxincome')) && Wekit::load('attach.PwThreadAttachBuy')->sumCost($attach['aid']) > $max) {
        } else {
            $creditBo->addLog('attach_sell', array($attach['ctype'] => $attach['cost']), $user, array(
                'name' => $attach['name'],
            ));
            $creditBo->set($user->uid, $attach['ctype'], $attach['cost'], true);
        }
        $creditBo->execute();
    }
}
