<?php


/**
 * 话题DM.
 *
 * @author jinlong.panjl <jinlong.panjl@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id$
 */
class PwTagDm extends PwBaseDm
{
    public $tag_id;

    public function __construct($tagId = 0)
    {
        $this->tag_id = (int) $tagId;
    }

    /**
     * 设置顺序.
     *
     * @param int $vieworder
     *
     * @return PwTagDm
     */
    public function setVieworder($vieworder)
    {
        $this->_data['vieworder'] = $vieworder;

        return $this;
    }

    /**
     * 设置名称.
     *
     * @param string $name
     *
     * @return PwTagDm
     */
    public function setName($name)
    {
        $this->_data['tag_name'] = $name;

        return $this;
    }

    /**
     * 设置parent.
     *
     * @param int $parentTagId
     *
     * @return PwTagDm
     */
    public function setParent($parentTagId)
    {
        $this->_data['parent_tag_id'] = $parentTagId;

        return $this;
    }

    /**
     * 设置是否热门.
     *
     * @param int $ifHot
     *
     * @return PwTagDm
     */
    public function setIfhot($ifhot)
    {
        $this->_data['ifhot'] = $ifhot;

        return $this;
    }

    /**
     * 获取是否有封面.
     *
     * @param int $iflogo
     *
     * @return PwTagDm
     */
    public function setIflogo($iflogo)
    {
        return $this->_data['iflogo'] = $iflogo;
    }

    /**
     * 设置logo.
     *
     * @param string $tagLogo
     *
     * @return PwTagDm
     */
    public function setTagLogo($tagLogo)
    {
        $this->_data['tag_logo'] = $tagLogo;

        return $this;
    }

    /**
     * 设置摘要
     *
     * @param string $excerpt
     *
     * @return PwTagDm
     */
    public function setExcerpt($excerpt)
    {
        $this->_data['excerpt'] = $excerpt;

        return $this;
    }

    /**
     * 设置seo标题.
     *
     * @param string $seoTitle
     *
     * @return PwTagDm
     */
    public function setSeoTitle($seoTitle)
    {
        $this->_data['seo_title'] = $seoTitle;

        return $this;
    }

    /**
     * 设置seoDescript.
     *
     * @param string $seoDescript
     *
     * @return PwTagDm
     */
    public function setSeoDescript($seoDescript)
    {
        $this->_data['seo_description'] = $seoDescript;

        return $this;
    }

    /**
     * 设置seo关键字.
     *
     * @param string $seoDescription
     *
     * @return PwTagDm
     */
    public function setSeoKeywords($seoKeywords)
    {
        $this->_data['seo_keywords'] = $seoKeywords;

        return $this;
    }

    /**
     * 设置内容关系数.
     *
     * @param int $contentCount
     *
     * @return PwTagDm
     */
    public function setContentCount($contentCount)
    {
        $this->_data['content_count'] = intval($contentCount);

        return $this;
    }

    /**
     * 设置被关注数.
     *
     * @param int $attentionCount
     *
     * @return PwTagDm
     */
    public function setAttentionCount($attentionCount)
    {
        $this->_data['attention_count'] = intval($attentionCount);

        return $this;
    }

    /**
     * increase关联内容数量 $num允许负数.
     *
     * @param int $attentionCount
     *
     * @return PwTagDm
     */
    public function addContentCount($num)
    {
        $this->_increaseData['content_count'] = intval($num);

        return $this;
    }

    /**
     * increase关注数量.
     *
     * @param int $attentionCount
     *
     * @return PwTagDm
     */
    public function addAttentionCount($num)
    {
        $this->_increaseData['attention_count'] = intval($num);

        return $this;
    }

    /**
     * 设置话题初始创建人.
     *
     * @param int $uid
     *
     * @return PwTagDm
     */
    public function setCreateUid($uid)
    {
        $this->_data['created_userid'] = $uid;

        return $this;
    }

    /**
     * 设置分类名称.
     *
     * @param string $categoryName
     *
     * @return PwTagDm
     */
    public function setCategoryName($categoryName)
    {
        $this->_data['category_name'] = $categoryName;

        return $this;
    }

    /**
     * 设置分类别名.
     *
     * @param string $alias
     *
     * @return PwTagDm
     */
    public function setCategoryAlias($alias)
    {
        $this->_data['alias'] = $alias;

        return $this;
    }

    /**
     * 设置话题来自应用类别ID.
     *
     * @param int $typeId
     *
     * @return PwTagDm
     */
    public function setTypeId($typeId)
    {
        $this->_data['type_id'] = $typeId;

        return $this;
    }

    /**
     * 设置话题来自应用类别唯一值（如帖子 tid）.
     *
     * @param int $paramId
     *
     * @return PwTagDm
     */
    public function setParamId($paramId)
    {
        $this->_data['param_id'] = $paramId;

        return $this;
    }

    /**
     * 设置内容是否显示.
     *
     * @param int $ifcheck
     *
     * @return PwTagDm
     */
    public function setIfCheck($ifcheck)
    {
        $this->_data['ifcheck'] = $ifcheck;

        return $this;
    }

    /**
     * 设置创建时间.
     *
     * @param int $createdTime
     *
     * @return PwTagDm
     */
    public function setCreatedTime($createdTime)
    {
        $this->_data['created_time'] = $createdTime;

        return $this;
    }

    /**
     * 设置分类.
     *
     * @param int $categoryId
     *
     * @return PwTagDm
     */
    public function setCategoryId($categoryId)
    {
        $this->_data['category_id'] = $categoryId;

        return $this;
    }

    /**
     * 获取创建人.
     *
     * @return int
     */
    public function getCreateUid()
    {
        return $this->_data['created_userid'];
    }

    /**
     * 获取是否热门字段.
     *
     * @return int
     */
    public function getIfhot()
    {
        return $this->_data['ifhot'];
    }

    /**
     * 获取分类.
     *
     * @param int $ifcheck
     *
     * @return PwTagDm
     */
    public function getCategoryId()
    {
        return $this->_data['category_id'];
    }

    /**
     * 设置内容关系tagid（主要为帖子阅读页、详细页查询服务）.
     *
     * @param int $contentTagId
     *
     * @return PwTagDm
     */
    public function setContentTagId($contentTagId)
    {
        $this->_data['content_tag_id'] = (int) $contentTagId;

        return $this;
    }

    protected function _beforeUpdate()
    {
        return $this->checkTagName();
    }

    protected function _beforeAdd()
    {
        $this->_data['created_time'] = Pw::getTime();

        return $this->checkTagName();
    }

    public function checkTagName($tagName = '')
    {
        $tagName = $tagName ? $tagName : $this->_data['tag_name'];
        if (!$tagName) {
            return true;
        }
        $maxLength = 15;
        if (($result = $this->isNameHasIllegalChar($tagName)) !== false) {
            return $result;
        }
        if (Pw::strlen($tagName) > $maxLength) {
            return new PwError('TAG:tagname.length.error', array('{maxlength}' => $maxLength));
        }
        if (Pw::strlen($this->_data['excerpt']) > 255) {
            return new PwError('TAG:excerpt.length.error', array('{maxlength}' => 255));
        }

        return true;
    }

    private function isNameHasIllegalChar($tagName)
    {
        if (0 >= preg_match('/^[\x7f-\xff\dA-Za-z\.\_]+$/', $tagName)) {
            return new PwError('TAG:error.tagname');
        }

        return false;
    }
}
