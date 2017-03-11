<?php

Wind::import('ADMIN:library.AdminBaseController');
/**
 * 公告管理前台展示逻辑处理.
 *
 * 1. run 权限入口
 * </code>
 *
 * @author MingXing Sun <mingxing.sun@aliyun-inc.com> 2012-01-12
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 *
 * @version $Id: IndexController.php 3219 2012-01-12 06:43:45Z mingxing.sun $
 */
class IndexController extends PwBaseController
{
    public $page = 1;
    public $perpage = 10;

    public function run()
    {
        $aid = $this->getInput('aid', 'get');
        $page = $this->getInput('page');
        $this->page = $page < 1 ? 1 : intval($page);
        list($start, $limit) = Pw::page2limit($this->page, $this->perpage);
        $total = $this->_getPwAnnounceDs()->countAnnounceByTime(Pw::str2time(Pw::time2str(Pw::getTime(), 'Y-m-d')));
        $list = $total ? $this->_getPwAnnounceService()->formatAnnouncesUsername($this->_getPwAnnounceDs()->getAnnounceByTimeOrderByVieworder(Pw::str2time(Pw::time2str(Pw::getTime(), 'Y-m-d')), $limit, $start)) : array();
        $this->setOutput($total, 'total');
        $this->setOutput($list, 'list');
        $this->setOutput($aid, 'aid');
        $this->setOutput($this->page, 'page');
        $this->setOutput($this->perpage, 'perpage');
    }

    /**
     * 加载PwUser Ds 服务
     *
     * @return PwAnnounceDs
     */
    private function _getPwUser()
    {
        return Wekit::load('user.PwUser');
    }

    /**
     * 加载PwAnnounce Ds 服务
     *
     * @return PwAnnounceDs
     */
    private function _getPwAnnounceDs()
    {
        return Wekit::load('announce.PwAnnounce');
    }

    /**
     * 获取公告管理基本表 DS服务
     *
     * @return PwAnnounce
     */
    private function _getPwAnnounceService()
    {
        return Wekit::load('announce.srv.PwAnnounceService');
    }
}
