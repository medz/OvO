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
 * @version $Id: PwAvatarUpload.php 28882 2013-05-28 10:51:23Z gao.wanggao $
 * @package upload
 */

class PwAvatarUpload extends PwUploadAction
{
    public $isLocal = true;

    public $user;
    public $udir;
    private $mime = array();

    public function __construct(PwUserBo $user)
    {
        $this->ftype = array('jpg' => 2000, 'png' => 2000, 'jpeg' => 2000);
        $this->user = $user;
        $this->udir = Pw::getUserDir($this->user->uid);
        $this->mime = array('image/jpg', 'image/jpeg', 'image/png', 'image/gif');
    }

    /**
     * @see PwUploadAction.check
     */
    public function check()
    {
        if (!$this->user->isExists()) {
            return new PwError('login.not');
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
        return $this->user->uid.'.jpg';
    }

    /**
     * @see PwUploadAction.getSaveDir
     */
    public function getSaveDir(PwUploadFile $file)
    {
        return 'avatar/'.$this->udir.'/';
    }

    /**
     * @see PwUploadAction.allowThumb
     */
    public function allowThumb()
    {
        return true;
    }

    /**
     * @see PwUploadAction.getThumbInfo
     */
    public function getThumbInfo($filename, $dir)
    {
        return array(
            array($this->user->uid.'.jpg', $dir, 200, 200, 2),
            array($this->user->uid.'_middle.jpg', $dir, 120, 120, 0),
            array($this->user->uid.'_small.jpg', $dir, 50, 50, 0),
        );
    }

    /**
     * @see PwUploadAction.allowWaterMark
     */
    public function allowWaterMark()
    {
        return false;
        //return $this->forum->forumset['watermark'];
    }

    /**
     * @see PwUploadAction.update
     */
    public function update($uploaddb)
    {
        return true;
    }

    public function getAids()
    {
        return array_keys($this->attachs);
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
