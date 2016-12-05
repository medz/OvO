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
 * @version $Id: PwMedalUpload.php 28882 2013-05-28 10:51:23Z gao.wanggao $
 */
class PwMedalUpload extends PwUploadAction
{
    public $key;
    public $width;
    public $height;
    public $dir;
    public $filename;
    private $mime = array();

    public function __construct($key, $width, $height)
    {
        $this->key = $key;
        $this->width = $width;
        $this->height = $height;
        $this->ftype = array('jpeg' => 2000, 'jpg' => 2000, 'png' => '2000', 'gif' => 2000);
        $this->mime = array('image/jpeg', 'image/png', 'image/jpg', 'image/gif');
    }

    /**
     * @see PwUploadAction.check
     */
    public function check()
    {
        if (!$_FILES[$this->key]['size']) {
            return new PwError('MEDAL:image.empty.fail');
        }

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
        return  $this->dir = 'medal/';
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
        return array(
            array($this->filename, $this->dir, $this->width, $this->height, 0),
        );
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
        return true;
    }

    /**
     * @see PwUploadAction.update
     */
    public function update($uploaddb)
    {
        foreach ($uploaddb as $key => $value) {
            $this->attachs = array(
                'name'     => $value['name'],
                'type'     => $value['type'],
                'path'     => $this->dir,
                'filename' => $this->filename,
                'size'     => $value['size'],
                'width'    => $this->width,
                'height'   => $this->height,
            );
        }

        return true;
    }

    public function getAids()
    {
        return array_keys($this->attachs);
    }

    public function getAttachInfo()
    {
        //$path  = Wekit::getGlobal('url', 'attach').'/';
        //return array('path' => $path, 'folder' => $this->folder, 'filename' => $this->filename);
        return $this->attachs;
    }
}
