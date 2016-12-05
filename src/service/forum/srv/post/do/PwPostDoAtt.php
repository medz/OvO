<?php

defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('SRV:forum.srv.post.do.PwPostDoBase');

Wind::import('SRV:upload.action.PwAttUpload');
Wind::import('SRV:attach.dm.PwThreadAttachDm');

/**
 * 帖子发布-附件 相关服务
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwPostDoAtt.php 23975 2013-01-17 10:20:11Z jieyin $
 */
class PwPostDoAtt extends PwPostDoBase
{
    public $post;
    public $action;
    public $attach;
    public $fid;

    protected $_alterattach = array();

    public function __construct(PwPost $pwpost, $flashatt)
    {
        $bhv = new PwAttUpload($pwpost->user, $pwpost->forum, $flashatt);
        $this->post = $pwpost;
        $this->action = new PwUpload($bhv);
        $this->attach = $pwpost->getAttachs();
        $this->fid = $pwpost->forum->fid;
    }

    public function check($postDm)
    {
        return $this->action->check();
    }

    public function hasAttach()
    {
        return $this->attach ? true : false;
    }

    public function createHtmlBeforeContent()
    {
        //include Wind::getRealPath('TPL:bbs.post_att.htm', true);
        //displayPostAttHtml($this);
    }

    public function editAttachs($oldatt_desc, $oldatt_cost, $oldatt_ctype)
    {
        is_array($oldatt_desc) || $oldatt_desc = array();
        is_array($oldatt_cost) || $oldatt_cost = array();
        is_array($oldatt_ctype) || $oldatt_ctype = array();
        foreach ($this->attach as $key => $value) {
            $isImg = ($value['type'] == 'img');
            $v = array(
                /*'special' => isset($oldatt_special[$key]) ? $oldatt_special[$key] : $value['special'],*/
                'ctype'   => isset($oldatt_ctype[$key]) ? $oldatt_ctype[$key] : $value['ctype'],
                'cost'    => isset($oldatt_cost[$key]) ? $oldatt_cost[$key] : $value['cost'],
                'descrip' => isset($oldatt_desc[$key]) ? $oldatt_desc[$key] : $value['descrip'],
            );
            if ($v['cost'] > 0 && $this->post->forum->forumset['allowsell'] && $this->post->user->getPermission('allow_thread_extend.sell')) {
                $v['special'] = 2;
                if (($max = $this->post->user->getPermission('sell_credit_range.maxprice')) > 0 && $v['cost'] > $max) {
                    $v['cost'] = $max;
                }
                if (!in_array($v['ctype'], $this->post->user->getPermission('sell_credits', false, array()))) {
                    Wind::import('SRV:credit.bo.PwCreditBo');
                    $v['ctype'] = key(PwCreditBo::getInstance()->cType);
                }
            } else {
                $v['cost'] = $v['special'] = 0;
                $v['ctype'] = '';
            }
            foreach ($v as $_k1 => $_v1) {
                if ($_v1 != $value[$_k1]) {
                    $this->_alterattach[$key] = $v;
                    $this->attach[$key] = array_merge($value, $v);
                    break;
                }
            }
        }
    }

    public function dataProcessing($postDm)
    {
        if (($result = $this->action->execute()) !== true) {
            return $result;
        }
        $postDm->setAids($this->action->getUploadNum() + count($this->attach));
        $ifupload = $this->action->getIfupload();
        if ($ifupload & 1) {
            $postDm->setHasImage(true);
        }
        if ($ifupload & 2) {
            $postDm->setHasTxt(true);
        }
        if ($ifupload & 4) {
            $postDm->setHasZip(true);
        }

        return $postDm;
    }

    public function addThread($tid)
    {
        if ($aids = $this->action->getAids()) {
            $dm = new PwThreadAttachDm();
            $dm->setFid($this->fid);
            $dm->setTid($tid);
            $this->_getService()->batchUpdateAttach($aids, $dm);
            $this->post->getUserDm()->addTodayupload(count($aids));
            $this->_operateCredit('upload_att');
        }
    }

    public function updateThread($tid)
    {
        $this->addThread($tid);
        $this->updateAttach();
    }

    public function addPost($pid, $tid)
    {
        if ($aids = $this->action->getAids()) {
            $dm = new PwThreadAttachDm();
            $dm->setFid($this->fid);
            $dm->setPid($pid);
            $dm->setTid($tid);
            $this->_getService()->batchUpdateAttach($aids, $dm);
            $this->post->getUserDm()->addTodayupload(count($aids));
            $this->_operateCredit('upload_att');
        }
    }

    public function updatePost($pid, $tid)
    {
        $this->addPost($pid, $tid);
        $this->updateAttach();
    }

    public function updateAttach()
    {
        if (!$this->_alterattach) {
            return;
        }
        foreach ($this->_alterattach as $key => $v) {
            $dm = new PwThreadAttachDm($key);
            $dm->setDescrip($v['descrip'])
                ->setSpecial($v['special'])
                ->setCost($v['cost'])
                ->setCtype($v['ctype']);
            $this->_getService()->updateAttach($dm);
        }
    }

    /**
     * 更新积分.
     */
    protected function _operateCredit($operate)
    {
        Wind::import('SRV:credit.bo.PwCreditBo');
        $credit = PwCreditBo::getInstance();
        $aids = $this->action->getAids();
        foreach ($aids as $v) {
            $credit->operate($operate, $this->post->user, true, array('forumname' => $this->post->forum->foruminfo['name']), $this->post->forum->getCreditSet($operate));
        }
        $credit->execute();
    }

    /**
     * Enter description here ...
     *
     * @return PwThreadAttach
     */
    protected function _getService()
    {
        return Wekit::load('attach.PwThreadAttach');
    }
}
