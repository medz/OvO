<?php

Wind::import('LIB:base.PwBaseController');
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: IndexController.php 25125 2013-03-05 03:29:29Z gao.wanggao $
 * @package
 */

class IndexController extends PwBaseController
{
    public function run()
    {
        $id = (int) $this->getInput('id', 'get');
        $portal = $this->_getPortalDs()->getPortal($id);
        if (!$portal) {
            $this->showError('page.status.404');
        }
        if (!$portal['isopen']) {
            $permissions = $this->_getPermissionsService()->getPermissionsForUserGroup($this->loginUser->uid);
            if ($permissions < 1) {
                $this->showError('page.status.404');
            }
        }

        $this->setOutput($portal, 'portal');
        if ($portal['navigate']) {
            $this->setOutput($this->headguide($portal['title']), 'headguide');
        }
        if ($portal['template']) {
            $url = WindUrlHelper::checkUrl(PUBLIC_THEMES.'/portal/local/'.$portal['template'], PUBLIC_URL);
            $design['url']['css'] = $url.'/css';
            $design['url']['images'] = $url.'/images';
            $design['url']['js'] = $url.'/js';
            Wekit::setGlobal($design, 'design');
            $this->setTemplate('THEMES:portal.local.'.$portal['template'].'.template.index');
        } else {
            $this->setTemplate('TPL:special.index_run');
        }
        //$this->getForward()->getWindView()->compileDir = 'DATA:design.default.' . $id;
        $this->setT($portal['template'], 'THEMES:portal.local');
        Wind::import('SRV:seo.bo.PwSeoBo');
        $seoBo = PwSeoBo::getInstance();
        $seoBo->init('area', 'custom', $id);
        $seoBo->set('{pagename}', $portal['title']);
        Wekit::setV('seo', $seoBo);
    }

    protected function headguide($protalname)
    {
        $bbsname = Wekit::C('site', 'info.name');
        $headguide = '<a href="'.WindUrlHelper::createUrl('').'" title="'.$bbsname.'" class="home">首页</a>';

        return $headguide.'<em>&gt;</em>'.WindSecurity::escapeHTML($protalname);
    }


    private function _getPortalDs()
    {
        return Wekit::load('design.PwDesignPortal');
    }

    protected function _getPermissionsService()
    {
        return Wekit::load('design.srv.PwDesignPermissionsService');
    }

    protected function setT($theme, $themePack)
    {
        $this->getForward()->getWindView()->setTheme($theme, $themePack);
    }
}
