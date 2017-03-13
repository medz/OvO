<?php
/**
 * 广告服务
 *
 * @author Zhu Dong <zhudong0808@gmail.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 *
 * @version $Id: codetemplates(windframework_docs_zend_8.0).xml 2781 2011-09-22 03:59:17Z zhudong $
 */
class PwAd
{
    public function getAllAd()
    {
        return $this->_getPwAdDao()->getAllAd();
    }

    public function getAdType()
    {
        return [
                '1' => '全站通用',
                '2' => '论坛列表页和阅读页',
                '3' => '论坛阅读页楼层广告',
                '4' => '门户频道',
                '5' => '普通',
        ];
    }

    public function addAdPosition(PwAdDm $dm)
    {
        if (($result = $dm->beforeAdd()) !== true) {
            return false;
        }

        return $this->_getPwAdDao()->addAdPosition($dm->getData());
    }

    public function editAdPosition(PwAdDm $dm)
    {
        if (($result = $dm->beforeUpdate()) !== true) {
            return false;
        }

        return $this->_getPwAdDao()->editAdPosition($dm->pid, $dm->getData());
    }

    public function getByPid($pid)
    {
        return $this->_getPwAdDao()->get($pid);
    }

    public function getByIdentifier($identifier)
    {
        return $this->_getPwAdDao()->getByIdentifier($identifier);
    }

    public function getModes()
    {
        return [
                'bbs' => [
                        'name' => '论坛',
                        'src'  => [
                                'announce.index',
                                'bbs.thread',
                                'bbs.read',
                                'default.index',
                                'bbs.forum',
                        ],
                ],
                'space' => [
                        'name' => '空间',
                        'src'  => [
                                'space.index',
                        ],
                ],
                'area' => [
                        'name' => '门户',
                        'src'  => [
                                'area.index',
                        ],
                ],
        ];
    }

    public function getDefaultPosition()
    {
        return [
                'Site.NavBanner' => [
                        'name'    => '导航通栏',
                        'type_id' => '1',
                        'width'   => '960',
                        'height'  => '70',
                ],
                'Site.Footer1' => [
                        'name'    => '底部横幅1',
                        'type_id' => '1',
                        'width'   => '960',
                        'height'  => '70',
                ],
                'Site.Footer2' => [
                        'name'    => '底部横幅2',
                        'type_id' => '1',
                        'width'   => '960',
                        'height'  => '70',
                ],
                'Site.FloatLeft' => [
                        'name'    => '对联广告（左）',
                        'type_id' => '1',
                        'width'   => '120',
                        'height'  => '250',
                ],
                'Site.FloatRight' => [
                        'name'    => '对联广告（右）',
                        'type_id' => '1',
                        'width'   => '120',
                        'height'  => '250',
                ],
                'Site.PopupNotice' => [
                        'name'    => '弹窗广告',
                        'type_id' => '1',
                        'width'   => '200',
                        'height'  => '200',
                ],
                'Thread.Top' => [
                        'name'    => '论坛帖子列表页面上',
                        'type_id' => '2',
                        'width'   => '705',
                        'height'  => '70',
                ],
                'Thread.Btm' => [
                        'name'    => '论坛帖子列表页面下',
                        'type_id' => '2',
                        'width'   => '705',
                        'height'  => '70',
                ],
                'Read.Layer.TidUp' => [
                        'name'    => '帖子楼层广告[帖子上方]',
                        'type_id' => '3',
                        'width'   => '400',
                        'height'  => '50',
                ],
                'Read.Layer.TidDown' => [
                        'name'    => '帖子楼层广告[帖子下方]',
                        'type_id' => '3',
                        'width'   => '400',
                        'height'  => '50',
                ],
                'Read.Layer.TidRight' => [
                        'name'    => '帖子楼层广告[帖子右侧]',
                        'type_id' => '3',
                        'width'   => '100',
                        'height'  => '200',
                ],
                'Read.Layer.User' => [
                        'name'    => '帖子楼层广告[用户信息下方]',
                        'type_id' => '3',
                        'width'   => '100',
                        'height'  => '100',
                ],
                'Read.Layer.TidAmong' => [
                        'name'    => '帖子楼层广告[楼层中间]',
                        'type_id' => '3',
                        'width'   => '960',
                        'height'  => '70',
                ],
                'Site.Sider.User' => [
                        'name'    => '右侧个人信息下',
                        'type_id' => '1',
                        'width'   => '230',
                        'height'  => '230',
                ],
                'Site.Sider.Btm' => [
                        'name'    => '右侧底部',
                        'type_id' => '1',
                        'width'   => '230',
                        'height'  => '230',
                ],
                'Common.Topic.Top' => [
                        'name'    => '话题页内容聚合上',
                        'type_id' => '5',
                        'width'   => '700',
                        'height'  => '60',
                ],
                'Common.Topic.Btm' => [
                        'name'    => '话题页内容聚合下',
                        'type_id' => '5',
                        'width'   => '700',
                        'height'  => '60',
                ],
                'Common.Attention.Btm' => [
                        'name'    => '我的关注发布框下',
                        'type_id' => '1',
                        'width'   => '700',
                        'height'  => '60',
                ],
        ];
    }

    public function getPages()
    {
        return [
                'bbs.thread' => '帖子列表页面',
                'bbs.read'   => '帖子阅读页面',
        ];
    }

    private function _getPwAdDao()
    {
        return Wekit::loadDao('SRV:advertisement.dao.PwAdDao');
    }
}
