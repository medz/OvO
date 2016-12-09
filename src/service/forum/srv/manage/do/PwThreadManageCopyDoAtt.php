<?php

defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('SRV:forum.srv.manage.do.PwThreadManageCopyDoBase');
Wind::import('SRV:attach.dm.PwThreadAttachDm');

/**
 * 帖子复制 - 附件.
 *
 * @author jinlong.panjl <jinlong.panjl@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id$
 */
class PwThreadManageCopyDoAtt extends PwThreadManageCopyDoBase
{
    protected $attachs = array();

    public function copyThread(PwTopicDm $topicDm, $newTid)
    {
        $ifupload = $topicDm->getField('ifupload');
        if (!$ifupload) {
            return;
        }
        $tid = $topicDm->tid;
        $this->attachs = $this->_getService()->getAttachByTid($tid, array(0));
        if (!$this->attachs) {
            return;
        }
        foreach ($this->attachs as $v) {
            $dm = new PwThreadAttachDm();
            $dm->setFid($v['fid'])
                ->setTid($newTid)
                ->setPid($v['pid'])
                ->setWidth($v['width'])
                ->setHeight($v['height'])
                ->setSpecial($v['special'])
                ->setCost($v['cost'])
                ->setCtype($v['ctype'])
                ->addHits($v['hits'])
                ->setName($v['name'])
                ->setType($v['type'])
                ->setSize($v['size'])
                ->setPath($v['path'])
                ->setIfthumb($v['ifthumb'])
                ->setCreatedUser($v['created_userid'])
                ->setCreatedTime($v['created_time'])
                ->setApp($v['app'])
                ->setDescrip($v['descrip']);
            $this->_getService()->addAttach($dm);
        }
    }

    /**
     * Enter description here ...
     *
     * @return PwThreadAttach
     */
    protected function _getService()
    {
        return Wekit::load('attach.PwThreadAttach');
    }
}
