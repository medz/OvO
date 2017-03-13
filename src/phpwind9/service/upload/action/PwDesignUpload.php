<?php

defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('COM:utility.WindUtility');

/**
 * the last known user to change this file in the repository  <$LastChangedBy: jieyin $>.
 *
 * @author $Author: jieyin $ Foxsee@aliyun.com
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwDesignUpload.php 23975 2013-01-17 10:20:11Z jieyin $
 */
class PwDesignUpload extends PwUploadAction
{
    public function __construct()
    {
        $this->ftype = ['zip' => 2000, 'txt' => 2000];
        $this->ifftp = 0;
    }

    /**
     * @see PwUploadAction.check
     */
    public function check()
    {
        if (!$_FILES) {
            return new PwError('file.empty.fail');
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
        $prename = substr(md5(Pw::getTime().WindUtility::generateRandStr(8)), 10, 15);
        $this->filename = $prename.'.'.$file->ext;

        return $this->filename;
    }

    /**
     * @see PwUploadAction.getSaveDir
     */
    public function getSaveDir(PwUploadFile $file)
    {
        return  $this->dir = '_tmp/';
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
