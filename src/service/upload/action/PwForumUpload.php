<?php

defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('LIB:upload.PwUploadAction');
Wind::import('COM:utility.WindUtility');

/**
 * 上传组件
 *
 * @author Mingqu Luo<luo.mingqu@gmail.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwForumUpload.php 23975 2013-01-17 10:20:11Z jieyin $
 * @package wind
 */

class PwForumUpload extends PwUploadAction
{
    public $key;
    public $fid;

    public function __construct($key, $fid)
    {
        $this->key = $key;
        $this->fid = $fid;
        $this->ftype = array('jpg' => 2000, 'png' => '2000', 'gif' => 2000);
    }

    public function check()
    {
        return true;
    }

    public function allowType($key)
    {
        if ($key != $this->key) {
            return false;
        }

        return true;
    }

    public function getSaveName(PwUploadFile $file)
    {
        return $this->fid.'.'.$file->ext;
    }

    public function getSaveDir(PwUploadFile $file)
    {
        return 'forum/'.$this->key.'/';
    }

    public function allowThumb()
    {
        return false;
    }

    public function getThumbInfo($filename, $dir)
    {
        return array(
            array($filename, $dir, 400, 400, 1),
        );
    }

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
                'attname' => $value['attname'],
                'type' => $value['type'],
                'path' => $value['fileuploadurl'],
                'size' => $value['size'],
                'width' => $this->width,
                'height' => $this->height,
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
        return $this->attachs;
    }
}
