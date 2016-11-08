<?php

defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('LIB:upload.PwUploadAction');
Wind::import('SRV:attach.dm.PwThreadAttachDm');
Wind::import('SRV:forum.bo.PwForumBo');
Wind::import('COM:utility.WindUtility');

/**
 * 上传组件
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwAttReplaceUpload.php 23975 2013-01-17 10:20:11Z jieyin $
 * @package upload
 */

class PwAttReplaceUpload extends PwUploadAction
{
    public $aid;
    public $attach;
    public $forum;
    public $user;

    public function __construct($user, $aid)
    {
        $this->user = ($user instanceof PwUserBo) ? $user : new PwUserBo($user);
        $this->aid = $aid;
        $this->attach = Wekit::load('attach.PwThreadAttach')->getAttach($aid);
        $this->forum = new PwForumBo($this->attach['fid']);
        $this->ftype = Wekit::C('attachment', 'extsize');
    }

    /**
     * @see PwUploadAction.check
     */
    public function check()
    {
        if (!$this->attach) {
            return new PwError('attach.exists.not');
        }
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
            Pw::deleteAttach($this->attach['path'], $this->attach['ifthumb']);
            $att = new PwThreadAttachDm($this->aid);
            $att->setName($value['name']);
            $att->setType($value['type']);
            $att->setSize($value['size']);
            $att->setPath($value['fileuploadurl']);
            $att->setIfthumb($value['ifthumb']);
            $att->setCreatedUser($this->user->uid);
            $att->setCreatedTime(Pw::getTime());
            $att->setApp('thread');
            $srv->updateAttach($att);

            if ($this->attach['tid'] && $this->attach['pid'] == 0 && $this->attach['type'] != $value['type']) {
                Wind::import('SRV:forum.dm.PwTopicDm');
                $dm = new PwTopicDm($this->attach['tid']);
                $dm->setHasAttach($value['type'], true);
                if (!Wekit::load('attach.PwThreadAttach')->countType($this->attach['tid'], 0, $this->attach['type'])) {
                    $dm->setHasAttach($this->attach['type'], false);
                }
                Wekit::load('forum.PwThread')->updateThread($dm);
            }

            $this->attachs[$this->aid] = array(
                'aid' => $this->aid,
                'name' => $value['name'],
                'type' => $value['type'],
                'path' => $value['fileuploadurl'],
                'size' => $value['size'],
                'descrip' => $value['descrip'],
                'ifthumb' => $value['ifthumb'],
            );
            break;
        }

        return true;
    }

    public function getAttachInfo()
    {
        $array = current($this->attachs);

        return array('aid' => $array['aid'], 'path' => Pw::getPath($array['path']), 'name' => $array['name']);
    }
}
