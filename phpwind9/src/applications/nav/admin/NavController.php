<?php

Wind::import('ADMIN:library.AdminBaseController');

/**
 * 导航模块控制器.
 *
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>
 *
 * @author $Author: gao.wanggao $
 *
 * @version $Id: NavController.php 3897 2012-01-17 07:08:43Z gao.wanggao $
 */
class NavController extends AdminBaseController
{
    private $_navType = '';

    /**
     * 导航列表.
     */
    public function run()
    {
        $this->_getNavType();
        $this->_navTab();
        $navList = $this->_getNavDs()->getNavByType($this->_navType, 2);
        $this->setOutput($navList, 'navList');
        $this->setOutput(Wekit::C('site', 'homeUrl'), 'homeUrl');
    }

    /**
     * 导航批量修改处理器.
     */
    public function dorunAction()
    {
        $this->getRequest()->isPost() || $this->showError('operate.fail');

        $homeUrl = '';
        Wekit::load('SRV:nav.dm.PwNavDm');
        $dms = $newDms = $datas = $newdatas = [];
        list($posts, $newposts, $navtype) = $this->getInput(['data', 'newdata', 'navtype'], 'post');
        $homeid = $this->getInput('home', 'post');
        foreach ($posts as $post) {
            if (! $post['name'] || ! $navtype) {
                continue;
            }
            if ($navtype == 'my') {
                $router = $post['sign'];
            } else {
                $router = Wind::getComponent('router')->getRoute('pw')->matchUrl($post['link']);
            }
            Wekit::load('SRV:nav.dm.PwNavDm');
            $dm = new PwNavDm($post['navid']);
            $dm->setName($post['name'])
                ->setLink($post['link'])
                ->setSign($router)
                ->setOrderid($post['orderid'])
                ->setIsshow($post['isshow']);
            $resource = $dm->beforeUpdate();
            if ($resource instanceof PwError) {
                $this->showError($resource->getError());
                break;
            }
            $dms[] = $dm;
            if ($post['navid'] == $homeid) {
                $homeUrl = $post['link'];
            }
        }
        if ($newposts) {
            foreach ($newposts as $k => $newpost) {
                if (! $newpost['name'] || ! $navtype) {
                    continue;
                }
                if ($navtype == 'my') {
                    $router = $newpost['sign'];
                } else {
                    $router = Wind::getComponent('router')->getRoute('pw')->matchUrl($newpost['link']);
                }
                Wekit::load('SRV:nav.dm.PwNavDm');
                list($isroot, $id) = explode('_', $k);
                $dm = new PwNavDm();
                if ($isroot == 'root') {
                    $dm->setParentid(0);
                } elseif ($isroot == 'child') {
                    if (is_numeric($newpost['parentid'])) {
                        $dm->setParentid($newpost['parentid']);
                    } else {
                        $dm->setParentid((int) $resource);
                    }
                }
                $dm->setName($newpost['name'])
                    ->setLink($newpost['link'])
                    ->setSign($router)
                    ->setOrderid($newpost['orderid'])
                    ->setIsshow($newpost['isshow'])
                    ->setTempid($newpost['tempid'])
                    ->setType($navtype);
                $resource = $this->_getNavDs()->addNav($dm);
                if ($resource instanceof PwError) {
                    $this->showError($resource->getError());
                    break;
                }
                if ($homeid == 'home_'.$k) {
                    $homeUrl = $newpost['link'];
                }
            }
        }
        if ($homeUrl) {
            $config = new PwConfigSet('site');
            $homeRouter = Wind::getComponent('router')->getRoute('pw')->matchUrl($homeUrl);
            if ($homeRouter === false) {
                $this->showError('ADMIN:nav.out.link');
            }
            $config->set('homeUrl', $homeUrl)
                ->set('homeRouter', $homeRouter)
                ->flush();
        }
        $this->_getNavDs()->updateNavs($dms);
        $this->_getNavService()->updateConfig();
        $this->showMessage('ADMIN:success');
    }

    /**
     * 导航修改表单.
     */
    public function editAction()
    {
        $navId = $this->getInput('navid', 'get');
        $navInfo = $this->_getNavDs()->getNav($navId);
        if (empty($navInfo)) {
            $resource = new PwError('ADMIN:nav.edit.fail.error.navid');
            $this->showError($resource->getError());
        }
        list($navInfo['color'], $navInfo['bold'], $navInfo['italic'], $navInfo['underline']) = explode('|', $navInfo['style']);
        $navInfo['font'] = 'style=';
        ! empty($navInfo['color']) && $navInfo['font'] .= 'color:'.$navInfo['color'].';';
        ! empty($navInfo['bold']) && $navInfo['font'] .= 'font-weight:bold;';
        ! empty($navInfo['italic']) && $navInfo['font'] .= 'font-style:italic;';
        ! empty($navInfo['underline']) && $navInfo['font'] .= 'text-decoration:underline;';
        $this->_getNavType();
        $this->_navTab();
        $this->setOutput($this->_getRootNavOption($navInfo['parentid']), 'navOption');
        $this->setOutput($navInfo, 'navInfo');
    }

    /**
     * 导航修改处理器.
     */
    public function doeditAction()
    {
        $this->getRequest()->isPost() || $this->showError('operate.fail');

        $keys = ['navid', 'type', 'parentid', 'name', 'link', 'image', 'fontColor', 'fontBold', 'fontItalic', 'fontUnderline', 'alt', 'target', 'orderid', 'isshow'];
        list($navid, $type, $parentid, $name, $link, $image, $fontColor, $fontBold, $fontItalic, $fontUnderline, $alt, $target, $orderid, $isshow) = $this->getInput($keys, 'post');
        $router = Wind::getComponent('router')->getRoute('pw')->matchUrl($link);
        if (! $name || ! $type) {
            $this->showError('ADMIN:nav.add.fail.strlen.name');
        }
        Wekit::load('SRV:nav.dm.PwNavDm');
        $dm = new PwNavDm($navid);
        $dm->setType($type)
            ->setParentid($parentid)
            ->setName($name)
            ->setLink($link)
            ->setStyle($fontColor, $fontBold, $fontItalic, $fontUnderline)
            ->setAlt($alt)
            ->setImage($image)
            ->setTarget($target)
            ->setOrderid($orderid)
            ->setIsshow($isshow);
        if ($type != 'my') {
            $dm->setSign($router);
        }
        $resource = $this->_getNavDs()->updateNav($dm);
        if ($resource instanceof PwError) {
            $this->showError($resource->getError());
        }
        $this->_getNavService()->updateConfig();
        $this->showMessage('ADMIN:success');
    }

    /**
     * 导航删除处理器.
     */
    public function delAction()
    {
        $navid = $this->getInput('navid', 'post');
        if (! $navid) {
            $this->showError('operate.fail');
        }

        $resource = $this->_getNavDs()->delNav($navid);
        if ($resource instanceof PwError) {
            $this->showError($resource->getError());
        }
        $this->_getNavService()->updateConfig();
        $this->showMessage('ADMIN:success');
    }

    private function _getNavType()
    {
        $navType = $this->getInput('type', 'get');
        empty($navType) && $navType = 'main';
        $this->_navType = $navType;
    }

    private function _getNavDs()
    {
        return Wekit::load('SRV:nav.PwNav');
    }

    private function _getNavService()
    {
        return Wekit::load('SRV:nav.srv.PwNavService');
    }

    /**
     * 导航公共TAB切换器.
     */
    private function _navTab()
    {
        $navTypeList = $this->_getNavService()->getNavType();
        $this->setOutput($this->_navType, 'navType');
        $this->setOutput($navTypeList, 'navTypeList');
    }

    /**
     * 组装顶级导航下拉选项.
     *
     * @param int $select 当前选中的ID
     *
     * @return string
     */
    private function _getRootNavOption($select = '')
    {
        $option = '';
        $list = $this->_getNavDs()->getRootNav($this->_navType);
        foreach ($list as $value) {
            $option .= '<option value="'.$value['navid'].'"';
            $option .= ($select == $value['navid']) ? 'selected' : '';
            $option .= '>'.$value['name'].'</option>';
        }

        return $option;
    }
}
