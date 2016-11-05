<?php

Wind::import('SRV:design.srv.model.PwDesignModelBase');
/**
 * the last known user to change this file in the repository  <$LastChangedBy: jieyin $>
 * <note>
 *  decorateAddProperty 为插入表单值修饰
 *  decorateEditProperty 为修改表单值修饰
 *  getData 获取数据
 * </note>
 * @author $Author: jieyin $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwDesignTagDataService.php 22678 2012-12-26 09:22:23Z jieyin $
 * @package
 */
class PwDesignTagDataService extends PwDesignModelBase
{
    public function decorateAddProperty($model)
    {
        $data = array();
        $data['categorys'] = $this->_getTagCateGorys();

        return $data;
    }

    public function decorateEditProperty($moduleBo)
    {
        $model = $moduleBo->getModel();
        $property = $moduleBo->getProperty();
        $data = array();
        $data['categorys'] = $this->_getTagCateGorys();

        return $data;
    }

    protected function getData($field, $order, $limit, $offset)
    {
        Wind::import('SRV:tag.vo.PwTagSo');
        $so = new PwTagSo();

        $field['tag_ids'] && $so->setTagId(explode(' ', $field['tag_ids']));
        $field['category_id'] && $so->setCategoryId($field['category_id']);
        $so->setIflogo($field['islogo']);
        $so->setIfhot(1);
        switch ($field['order']) {
            case 0:
                $so->orderbyCreatedTime();
                break;
            case 1:
                $so->orderbyAttentionCount();
                break;
            case 2:
                $so->orderbyContentCount();
                break;
        }
        $list = Wekit::load('tag.PwTagSearch')->searchTag($so, $limit, $offset);
        if (!$list) {
            return array();
        }
        foreach ($list as $k => $v) {
            $list[$k]['tagid'] = $v['tag_id'];
            $list[$k]['tag_name'] = $this->_formatTitle($v['tag_name']);
            $list[$k]['url'] = WindUrlHelper::createUrl('tag/index/view', array('name' => $v['tag_name']), '', 'pw');
            $list[$k]['logo'] = Pw::getPath($v['tag_logo']);
            $list[$k]['attention_count'] = $v['attention_count'];
            $list[$k]['content_count'] = $v['content_count'];
            $list[$k]['excerpt'] = $v['excerpt'];
            $list[$k]['thumb_attach'] = $v['tag_logo'] ? $v['tag_logo'] : '';
        }

        return $list;
    }

    private function _getTagCateGorys()
    {
        $cateGorys = $this->_getTagCateGoryDs()->getAllCategorys();
        $data[] = '全部';
        foreach ($cateGorys as $v) {
            $data[$v['category_id']] = $v['category_name'];
        }

        return $data;
    }

    private function _getTagCateGoryDs()
    {
        return Wekit::load('tag.PwTagCateGory');
    }

    private function _getModelDs()
    {
        return Wekit::load('design.PwDesignModel');
    }
    /**
     * PwDesignComponent
     *
     * @return PwDesignComponent
     */
    private function _getComponentDs()
    {
        return Wekit::load('design.PwDesignComponent');
    }
}
