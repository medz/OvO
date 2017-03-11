<?php


/**
 * 门户数据 - 友情链接.
 *
 * @author jinlong.panjl <jinlong.panjl@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id$
 */
class PwDesignLinkDataService extends PwDesignModelBase
{
    public function decorateAddProperty($model)
    {
        $data = array();
        $data['linkType'] = $this->_getCateGorys();

        return $data;
    }

    public function decorateEditProperty($moduleBo)
    {
        $model = $moduleBo->getModel();
        $property = $moduleBo->getProperty();
        $data = array();
        $data['linkType'] = $this->_getCateGorys();

        return $data;
    }

    protected function getData($field, $order, $limit, $offset)
    {
        $so = new PwLinkSo();

        $so->setIfcheck(1);
        $field['linkType'] && $so->setTypeid($field['linkType']);

        $field['isLog'] != -1 && $so->setLogo($field['isLog']);
        $list = $this->_getLinkDs()->searchLink($so, $limit, $offset);
        if (!$list) {
            return array();
        }
        foreach ($list as $k => $v) {
            $list[$k]['lid'] = $v['lid'];
            $list[$k]['name'] = $this->_formatTitle($v['name']);
            $list[$k]['url'] = $v['url'];
            $list[$k]['logo'] = $v['logo'];
            $list[$k]['contact'] = $v['contact'];
        }

        return $list;
    }

    private function _getCateGorys()
    {
        $cateGorys = $this->_getLinkDs()->getAllTypes();
        $data = array(0 => '全部');
        foreach ($cateGorys as $v) {
            $data[$v['typeid']] = $v['typename'];
        }

        return $data;
    }

    /**
     * PwLink.
     *
     * @return PwLink
     */
    private function _getLinkDs()
    {
        return Wekit::load('link.PwLink');
    }
}
