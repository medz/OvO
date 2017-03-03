<?php


/**
 * the last known user to change this file in the repository  <$LastChangedBy$>.
 *
 * @author $Author$ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id$
 */
class SourceController extends PwBaseController
{
    public function beforeAction($handlerAdapter)
    {
        parent::beforeAction($handlerAdapter);
        if ($this->loginUser->uid < 1) {
            $this->forwardRedirect(WindUrlHelper::createUrl('u/login/run/'));
        }
    }

    public function run()
    {
    }

    public function addlikeAction()
    {
        $this->getRequest()->isPost() || $this->showError('operate.fail');

        $fromid = (int) $this->getInput('fromid', 'post');
        $fromApp = $this->getInput('app', 'post');
        $subject = $this->getInput('subject', 'post');
        $url = $this->getInput('url', 'post');
        if ($fromid < 1 || empty($fromApp)) {
            $this->showError('BBS:like.fail');
        }
        $source = $this->_getLikeSourceDs()->getSourceByAppAndFromid($fromApp, $fromid);
        $newId = isset($source['sid']) ? (int) $source['sid'] : 0;

        if ($newId < 1) {
            $dm = new PwLikeSourceDm();
            $dm->setSubject($subject)
                ->setSourceUrl($url)
                ->setFromApp($fromApp)
                ->setFromid($fromid)
                ->setLikeCount(0);
            $newId = $this->_getLikeSourceDs()->addSource($dm);
        } else {
            $dm = new PwLikeSourceDm($source['sid']);
            $dm->setLikeCount($source['like_count']);
            $this->_getLikeSourceDs()->updateSource($dm);
        }

        $resource = $this->_getLikeService()->addLike($this->loginUser, 9, $newId);
        if ($resource instanceof PwError) {
            $this->showError($resource->getError());
        }
        $this->setOutput($resource, 'data');
        $this->showMessage('BBS:like.success');
    }

    private function _getLikeSourceDs()
    {
        return Wekit::load('like.PwLikeSource');
    }

    private function _getLikeService()
    {
        return Wekit::load('like.srv.PwLikeService');
    }
}
