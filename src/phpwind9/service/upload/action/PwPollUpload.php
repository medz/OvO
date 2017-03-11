<?php

defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('COM:utility.WindUtility');

/**
 * 投票图片服务
 *
 * @author MingXing Sun <mingxing.sun@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwPollUpload.php 23975 2013-01-17 10:20:11Z jieyin $
 */
class PwPollUpload extends PwUploadAction
{
    public $dir;
    public $filename;
    public $user;

    public function __construct($user)
    {
        $this->user = ($user instanceof PwUserBo) ? $user : new PwUserBo($user);
        $this->ftype = array('jpg' => 2000, 'jpeg' => 2000, 'png' => '2000', 'gif' => 2000);
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
        $prename = substr(md5(Pw::getTime().WindUtility::generateRandStr(8)), 10, 15);
        $this->filename = $prename.'.'.$file->ext;

        return $this->filename;
    }

    /**
     * @see PwUploadAction.getSaveDir
     */
    public function getSaveDir(PwUploadFile $file)
    {
        list($y, $m, $d) = explode('-', date('Y-m-d', Pw::getTime()));
        $this->dir = "poll/$y/$m/";

        return  $this->dir;
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
            array($filename, 'thumb/'.$dir, 120, 120, 0, 1),
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
        //$ds = $this->_getAttachDs();
        //
        foreach ($uploaddb as $key => $value) {
            /*
            $dm = new PwPollAttachDm();
            $dm->setName($value['name']);
            $dm->setType($value['type']);
            $dm->setSize($value['size']);
            $dm->setPath($value['fileuploadurl']);
            $dm->setIfthumb($value['ifthumb']);
            $dm->setCreatedUser($this->user->uid);
            $dm->setCreatedTime(Pw::getTime());
            $dm->setApp('poll');
            $aid = $ds->addAttach($dm);
            */
            $this->attachs[$value['attname']][$value['id']] = array(
                'id'   => $value['id'],
                'name' => $value['name'],
                'type' => $value['type'],
                'path' => $value['fileuploadurl'],
                'size' => $value['size'],
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

    /**
     * get PwAttach.
     *
     * @return PwAttach
     */
    protected function _getAttachDs()
    {
        return Wekit::load('attach.PwAttach');
    }
}
