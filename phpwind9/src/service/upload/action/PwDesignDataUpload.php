<?php

defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('COM:utility.WindUtility');

/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>.
 *
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwDesignDataUpload.php 28882 2013-05-28 10:51:23Z gao.wanggao $
 */
class PwDesignDataUpload extends PwUploadAction
{
    public $moduleid = 0;
    public $key = 0;
    public $mime = [];

    public function __construct($key, $moduleid)
    {
        $this->ftype = ['jpg' => 2000, 'jpeg' => 2000, 'png' => 2000, 'gif' => 2000];
        $this->mime = ['image/jpg', 'image/jpeg', 'image/png', 'image/gif'];
        $this->ifftp = 0;
        $this->moduleid = (int) $moduleid;
        $this->key = $key;
    }

    /**
     * @see PwUploadAction.check
     */
    public function check()
    {
        return true;
    }

    /**
     * @see PwUploadAction.allowType
     */
    public function allowType($key)
    {
        if ($key == $this->key) {
            return true;
        }
    }

    /**
     * @see PwUploadAction.getSaveName
     */
    public function getSaveName(PwUploadFile $file)
    {
        $prename = substr(md5(Pw::getTime().WindUtility::generateRandStr(8)), 10, 15);
        $this->filename = $prename.'.'.$file->ext;

        return $this->filename;
    }

    /**
     * @see PwUploadAction.getSaveDir
     */
    public function getSaveDir(PwUploadFile $file)
    {
        return  $this->dir = 'module/'.$this->moduleid.'/';
    }

    /**
     * @see PwUploadAction.allowThumb
     */
    public function allowThumb()
    {
        return false;
    }

    /**
     * @see PwUploadAction.getThumbInfo
     */
    public function getThumbInfo($filename, $dir)
    {
        return [];
    }

    /**
     * @see PwUploadAction.allowWaterMark
     */
    public function allowWaterMark()
    {
        return false;
    }

    public function transfer()
    {
        return false;
    }

    /**
     * @see PwUploadAction.update
     */
    public function update($uploaddb)
    {
        foreach ($uploaddb as $key => $value) {
            $this->attachs = [
                'name'     => $value['name'],
                'type'     => $value['type'],
                'path'     => $this->dir,
                'filename' => $this->filename,
                'size'     => $value['size'],
                'ext'      => $value['ext'],
            ];
        }

        return true;
    }

    public function getAttachInfo()
    {
        return $this->attachs;
    }
}
