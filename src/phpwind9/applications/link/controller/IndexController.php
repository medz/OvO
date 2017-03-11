<?php


/**
 * 友情链接.
 *
 * @author jinlong.panjl <jinlong.panjl@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id$
 */
class IndexController extends PwBaseController
{
    public function run()
    {
    }

    public function doaddAction()
    {
        $this->getRequest()->isPost() || $this->showError('operate.fail');

        list($name, $url, $descrip, $logo, $ifcheck, $contact) = $this->getInput(array('name', 'url', 'descrip', 'logo', 'ifcheck', 'contact'), 'post');

        Wind::import('SRC:service.link.dm.PwLinkDm');
        $linkDm = new PwLinkDm();
        $linkDm->setName($name);
        $linkDm->setUrl($url);
        $linkDm->setDescrip($descrip);
        $linkDm->setLogo($logo);
        $linkDm->setIfcheck(0);
        $linkDm->setContact($contact);
        $logo && $linkDm->setIflogo(1);
        if (($result = $this->_getLinkDs()->addLink($linkDm)) instanceof PwError) {
            $this->showError($result->getError());
        }
        $this->showMessage('operate.success');
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
