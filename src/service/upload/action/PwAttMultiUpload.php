<?php

defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('SRV:attach.dm.PwThreadAttachDm');
Wind::import('SRV:forum.bo.PwForumBo');
Wind::import('COM:utility.WindUtility');

/**
 * 上传组件.
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwAttMultiUpload.php 23975 2013-01-17 10:20:11Z jieyin $
 */
class PwAttMultiUpload extends PwUploadAction
{
    public $forum;
    public $user;

    public function __construct($user, $forum)
    {
        $this->user = ($user instanceof PwUserBo) ? $user : new PwUserBo($user);
        $this->forum = ($forum instanceof PwForumBo) ? $forum : new PwForumBo($forum);
        $this->ftype = Wekit::C('attachment', 'extsize');
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
            return new PwError('BBS:forum.fid.select');
        }
        if (($result = $this->forum->allowUpload($this->user)) !== true) {
            return new PwError('BBS:forum.permissions.upload.allow', array('{grouptitle}' => $this->user->getGroupInfo('name')));
        }
        if (!$this->forum->foruminfo['allow_upload'] && !$this->user->getPermission('allow_upload')) {
            return new PwError('permission.upload.allow', array('{grouptitle}' => $this->user->getGroupInfo('name')));
        }

        return true;
    }

    /**
     * @see PwUploadAction.allowType
     */
    public function allowType($key)
    {
        return true;
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
            array($filename, 'thumb/mini/'.$dir, 200, 200, 2),
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

    /**
     * @see PwUploadAction.update
     */
    public function update($uploaddb)
    {
        $srv = Wekit::load('attach.PwThreadAttach');
        foreach ($uploaddb as $key => $value) {
            $value['name'] = WindConvert::convert($value['name'], Wind::getApp()->getResponse()->getCharset(), 'utf-8');
            $att = new PwThreadAttachDm();
            $att->setName($value['name']);
            $att->setType($value['type']);
            $att->setSize($value['size']);
            $att->setPath($value['fileuploadurl']);
            $att->setIfthumb($value['ifthumb']);
            $att->setCreatedUser($this->user->uid);
            $att->setCreatedTime(Pw::getTime());
            if ($value['thumb'] && $value['thumb'][0]) {
                $att->setWidth($value['thumb'][0][2]);
                $att->setHeight($value['thumb'][0][3]);
            }
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

    public function getAttachInfo()
    {
        if (!$this->attachs) {
            return array();
        }
        $array = current($this->attachs);
        $result = array('aid' => $array['aid']);
        if ($array['type'] == 'img') {
            $result['path'] = Pw::getPath($array['path']);
            $result['thumbpath'] = Pw::getPath($array['path'], $array['ifthumb']);
        }

        return $result;
    }
}
