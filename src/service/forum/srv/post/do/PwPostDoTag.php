<?php

defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('SRV:forum.srv.post.do.PwPostDoBase');
Wind::import('SRV:tag.dm.PwTagDm');
/**
 * 帖子发布 - 话题
 *
 * @author jinlong.panjl <jinlong.panjl@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id$
 * @package wind
 */
class PwPostDoTag extends PwPostDoBase
{
    private $loginUser;
    private $defaultType = 'threads';
    private $tagNames = array();
    private $typeId = '';

    public function __construct(PwPost $pwpost, $tagNames)
    {
        $this->loginUser = $pwpost->user;
        $tagNames = $tagNames ? $tagNames : array();
        $this->tagNames = array_unique($tagNames);
        $this->typeId = $this->_getService()->getTypeIdByTypeName($this->defaultType);
    }

    public function addThread($tid)
    {
        $this->_getService()->addTags($this->_buildTagDm($tid));
    }

    public function updateThread($tid)
    {
        $this->_getService()->updateTags($this->typeId, $tid, $this->_buildTagDm($tid));
    }

    public function getDm()
    {
        return new PwTagDm();
    }

    public function dataProcessing($postDm)
    {
        if (!is_array($this->tagNames) || !$this->tagNames) {
            return $postDm;
        }

        $postDm->setTags(implode(',', $this->tagNames));

        return $postDm;
    }

    private function _buildTagDm($tid)
    {
        if (!is_array($this->tagNames) || !$this->tagNames) {
            return false;
        }
        $dmArray = array();
        foreach ($this->tagNames as $value) {
            $value = trim($value);
            $dm = $this->getDm();
            $dmArray[$value] =
                $dm->setName($value)
                    ->setTypeId($this->typeId)
                    ->setParamId($tid)
                    ->setIfhot(1)
                    ->setCreateUid($this->loginUser->uid)
            ;
        }

        return $dmArray;
    }

    public function check($postDm)
    {
        if (!is_array($this->tagNames) || !$this->tagNames) {
            return true;
        }
        $count = count($this->tagNames);
        foreach ($this->tagNames as $v) {
            $dm = $this->getDm();
            $dm->setName($v);
            if (($return = $dm->beforeAdd()) instanceof PwError) {
                return $return;
            }
        }
        if ($count > 5) {
            return new PwError('Tag:tagnum.exceed');
        }
        if ($count && $this->loginUser->getPermission('tag_allow_add') < 1) {
            return new PwError('TAG:right.tag_allow_add.error');
        }

        return true;
    }

    /**
     * Enter description here ...
     *
     * @return PwTagService
     */
    protected function _getService()
    {
        return Wekit::load('tag.srv.PwTagService');
    }
}
