<?php

Wind::import('SRV:design.srv.model.PwDesignModelBase');
/**
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwDesignSearchbarDataService.php 24726 2013-02-18 06:15:04Z gao.wanggao $
 */
class PwDesignSearchbarDataService extends PwDesignModelBase
{
    public function decorateAddProperty($model)
    {
        $data['searchbar'] = '<div class="design_layout_search">
		<form method="post" action="{@url:search/search/run}">
		<div><input type="text" name="keyword" speech="" placeholder="搜索其实很简单" accesskey="s"></div>
		<button type="submit"><span>搜索</span></button>
		</form></div>';

        return $data;
    }

    public function decorateEditProperty($moduleBo)
    {
        $data['searchbar'] = '<div class="design_layout_search">
		<form method="post" action="{@url:search/search/run}">
		<div><input type="text" name="keyword" speech="" placeholder="搜索其实很简单" accesskey="s"></div>
		<button type="submit"><span>搜索</span></button>
		</form></div>';

        return $data;
    }

    public function decorateSaveProperty($property, $moduleid)
    {
        $property['html_tpl'] = $property['html'];
        $property['limit'] = 1;

        return $property;
    }

    protected function getData($field, $order, $limit, $offset)
    {
        $data[0]['html'] = $field['html'];

        return $data;
    }
}
