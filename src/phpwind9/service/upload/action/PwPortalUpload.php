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
 * @version $Id: PwPortalUpload.php 28882 2013-05-28 10:51:23Z gao.wanggao $
 */
class PwPortalUpload extends PwUploadAction
{
    private $id = 0;
    private $mime = [];

    public function __construct($id)
    {
        $this->id = $id;
        $this->ftype = ['jpeg' => 2000, 'jpg' => 2000, 'png' => '2000', 'gif' => 2000];
        $this->mime = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
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
        return true;
    }

    /**
     * @see PwUploadAction.getSaveName
     */
    public function getSaveName(PwUploadFile $file)
    {
        $this->filename = $this->id.'.'.$file->ext;

        return $this->filename;
    }

    /**
     * @see PwUploadAction.getSaveDir
     */
    public function getSaveDir(PwUploadFile $file)
    {
        return  $this->dir = 'portal/';
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
        return [
            [$this->filename, $this->dir, $this->width, $this->height, 0],
        ];
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
                'width'    => $this->width,
                'height'   => $this->height,
            ];
        }

        return true;
    }

    public function getAttachInfo()
    {
        return $this->attachs;
    }
}
