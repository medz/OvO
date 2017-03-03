<?php

defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('COM:utility.WindUtility');

/**
 * 上传组件.
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwAttUpload.php 23975 2013-01-17 10:20:11Z jieyin $
 */
class PwAttUpload extends PwUploadAction
{
    public $forum;
    public $user;
    public $flashatt;

    public function __construct(PwUserBo $user, PwForumBo $forum, $flashatt = array())
    {
        $this->user = $user;
        $this->forum = $forum;
        $this->ftype = Wekit::C('attachment', 'extsize');
        $this->flashatt = $flashatt;
    }

    /**
     * @see PwUploadAction.check
     */
    public function check()
    {
        if (!$this->user->isExists()) {
            return new PwError('login.not');
        }
        if (!$this->forum->isForum()) {
            return new PwError('FORUM_IS_NOT_EXISTS');
        }
        if (($result = $this->forum->allowUpload($this->user)) !== true) {
            return new PwError('BBS:forum.permissions.upload.allow', array('{grouptitle}' => $this->user->getGroupInfo('name')));
        }
        if (!$this->forum->foruminfo['allow_upload'] && !$this->user->getPermission('allow_upload')) {
            return new PwError('permission.upload.allow', array('{grouptitle}' => $this->user->getGroupInfo('name')));
        }
        if ($uploadPerday = $this->user->getPermission('uploads_perday')) {
            $count = PwUpload::countUploadedFile() + count($this->flashatt);
            $todayupload = $this->user->info['lastpost'] < Pw::getTdtime() ? 0 : $this->user->info['todayupload'];
            if ($count > 0 && ($count + $todayupload) > $uploadPerday) {
                return new PwError('permission.upload.nums.perday', array('{nums}' => $uploadPerday));
            }
        }

        return true;
    }

    /**
     * @see PwUploadAction.allowType
     */
    public function allowType($key)
    {
        return $key == 'attachment';
    }

    /**
     * @see PwUploadAction.getSaveName
     */
    public function getSaveName(PwUploadFile $file)
    {
        $prename = substr(md5(Pw::getTime().$file->id.WindUtility::generateRandStr(8)), 10, 15);
        $filename = $this->forum->fid."_{$this->user->uid}_$prename.".$file->ext;

        return $filename;
    }

    /**
     * @see PwUploadAction.getSaveDir
     */
    public function getSaveDir(PwUploadFile $file)
    {
        return date('ym').'/thread/';
    }

    /**
     * @see PwUploadAction.allowThumb
     */
    public function allowThumb()
    {
        if ($ifthumb = $this->forum->forumset['ifthumb']) {
            return $ifthumb != 2;
        }

        return Wekit::C('attachment', 'thumb');
    }

    /**
     * @see PwUploadAction.getThumbInfo
     */
    public function getThumbInfo($filename, $dir)
    {
        $config = Wekit::C('attachment');
        if ($this->forum->forumset['ifthumb'] == 1 && ($this->forum->forumset['thumbwidth'] || $this->forum->forumset['thumbheight'])) {
            $config['thumb.size.width'] = $this->forum->forumset['thumbwidth'];
            $config['thumb.size.height'] = $this->forum->forumset['thumbheight'];
        }

        return array(
            array($filename, 'thumb/'.$dir, $config['thumb.size.width'], $config['thumb.size.height'], $config['thumb']),
            array($filename, 'thumb/mini/'.$dir, 200, 200, 1),
        );
    }

    /**
     * @see PwUploadAction.allowWaterMark
     */
    public function allowWaterMark()
    {
        if ($water = $this->forum->forumset['water']) {
            return $water != 2;
        }
        $config = Wekit::C('attachment');

        return $config['mark.markset'] && in_array('bbs', $config['mark.markset']);
    }

    /**
     * @see PwUploadAction.getWaterMarkInfo
     */
    public function getWaterMarkInfo()
    {
        if ($this->forum->forumset['water'] == 1 && $this->forum->forumset['waterimg']) {
            return array('type' => 1, 'file' => $this->forum->forumset['waterimg']);
        }

        return array();
    }

    public function transfer()
    {
        if (empty($this->flashatt)) {
            return false;
        }
        $deltmp = array();
        $attach = $this->_getService()->getTmpAttachByUserid($this->user->uid);
        foreach ($attach as $rt) {
            $aid = $rt['aid'];
            if (!isset($this->flashatt[$aid])) {
                Pw::deleteAttach($rt['path'], $rt['ifthumb']);
                $deltmp[] = $aid;
                continue;
            }
            $value = $this->flashatt[$aid];
            $dm = new PwThreadAttachDm($aid);
            $dm->setDescrip($value['desc']);
            if ($value['cost'] > 0 && $this->forum->forumset['allowsell'] && $this->user->getPermission('allow_thread_extend.sell')) {
                if (($max = $this->user->getPermission('sell_credit_range.maxprice')) > 0 && $value['cost'] > $max) {
                    $value['cost'] = $max;
                }
                if (!in_array($value['ctype'], $this->user->getPermission('sell_credits', false, array()))) {
                    $value['ctype'] = key(PwCreditBo::getInstance()->cType);
                }
                $dm->setSpecial(2)
                    ->setCost($value['cost'])
                    ->setCtype($value['ctype']);
            }
            $this->_getService()->updateAttach($dm);

            $this->attachs[$aid] = array(
                'aid'     => $aid,
                'name'    => $rt['name'],
                'type'    => $rt['type'],
                'path'    => $rt['fileuploadurl'],
                'size'    => $rt['size'],
                'descrip' => $value['desc'],
                'ifthumb' => $rt['ifthumb'],
            );
        }
        if ($deltmp) {
            $this->_getService()->batchDeleteAttach($deltmp);
        }

        return true;
    }

    /**
     * @see PwUploadAction.update
     */
    public function update($uploaddb)
    {
        $this->transfer();
        $srv = $this->_getService();
        foreach ($uploaddb as $key => $value) {
            $att = new PwThreadAttachDm();
            $att->setName($value['name']);
            $att->setType($value['type']);
            $att->setSize($value['size']);
            $att->setPath($value['fileuploadurl']);
            $att->setIfthumb($value['ifthumb']);
            $att->setCreatedUser($this->user->uid);
            $att->setCreatedTime(Pw::getTime());
            $att->setApp('thread');
            $aid = $srv->addAttach($att);

            $this->attachs[$aid] = array(
                'aid'     => $aid,
                'name'    => $value['name'],
                'type'    => $value['type'],
                'path'    => $value['fileuploadurl'],
                'size'    => $value['size'],
                'descrip' => $value['descrip'],
                'ifthumb' => $value['ifthumb'],
            );
        }

        return true;
    }

    public function getAids()
    {
        return array_keys($this->attachs);
    }

    public function getIfupload()
    {
        if (!$this->attachs) {
            return 0;
        }
        $ifupload = 0;
        foreach ($this->attachs as $key => $value) {
            switch ($value['type']) {
                case 'img':
                    $p = 1; break;
                case 'txt':
                    $p = 2; break;
                default:
                    $p = 4;
            }
            $ifupload |= $p;
        }

        return $ifupload;
        //$array = end($this->attachs);
        //return $array['type'] == 'img' ? 1 : ($array['type'] == 'txt' ? 2 : 3);
    }

    public function getAttachInfo()
    {
        $array = current($this->attachs);
        $path = Wekit::getGlobal('url', 'attach').'/'.$array['path'];
        //list($path) = geturl($array['attachurl'], 'lf', $array['ifthumb']&1);
        return array('aid' => $array['aid'], 'path' => $path);
    }

    protected function _getService()
    {
        return Wekit::load('attach.PwThreadAttach');
    }
}
