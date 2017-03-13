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
class SpaceBaseController extends PwBaseController
{
    public $space;

    /**
     * (non-PHPdoc).
     *
     * @see src/library/base/PwBaseController::beforeAction()
     */
    public function beforeAction($handlerAdapter)
    {
        parent::beforeAction($handlerAdapter);
        $spaceUid = $this->getInput('uid', 'get');
        if ($spaceUid === '0') {
            $this->showError('SPACE:space.guest');
        }
        $spaceUid = intval($spaceUid);
        if ($spaceUid < 1) {
            if ($userName = $this->getInput('username', 'get')) {
                $user = Wekit::load('user.PwUser')->getUserByName($userName);
                $spaceUid = isset($user['uid']) ? $user['uid'] : 0;
            } elseif ($this->loginUser->uid > 0) {
                $spaceUid = $this->loginUser->uid;
            } else {
                $this->showError('SPACE:space.not.exist');
            }
        }

        $this->space = new PwSpaceModel($spaceUid);

        if (!$this->space->space['uid']) {
            $user = Wekit::load('user.PwUser')->getUserByUid($spaceUid);
            if ($user) {
                Wekit::load('space.dm.PwSpaceDm');
                $dm = new PwSpaceDm($spaceUid);
                $dm->setVisitCount(0);
                Wekit::load('space.PwSpace')->addInfo($dm);
                $this->space = new PwSpaceModel($spaceUid);
            } else {
                //$this->forwardRedirect(WindUrlHelper::createUrl('u/login/run/'));
                $this->showError('SPACE:space.not.exist');
            }
        }

        $this->space->setTome($spaceUid, $this->loginUser->uid);
        $this->space->setVisitUid($this->loginUser->uid);
        if (!$this->space->allowView('space')) {
            $this->forwardRedirect(WindUrlHelper::createUrl('space/ban/run', ['uid' => $spaceUid]));
        }
    }

    /**
     * (non-PHPdoc).
     *
     * @see src/library/base/PwBaseController::afterAction()
     */
    public function afterAction($handlerAdapter)
    {
        $this->setTheme('space', $this->space->space['space_style']);
        //$this->addCompileDir($this->space->space['space_style'] ? $this->space->space['space_style'] : Wekit::C('site', 'theme.space.default'));
        $host = $this->space->tome == PwSpaceModel::MYSELF ? '我' : 'Ta';
        $this->setOutput($this->space, 'space');
        $this->setOutput($host, 'host');
        $this->updateSpaceOnline();
        parent::afterAction($handlerAdapter);
    }

    /**
     * 更新在线状态
     */
    protected function updateSpaceOnline()
    {
        if ($this->loginUser->uid < 1) {
            return false;
        }
        $online = Wekit::load('online.srv.PwOnlineService');
        $createdTime = $online->spaceOnline($this->space->spaceUid);
        if (!$createdTime) {
            return false;
        }
        $dm = Wekit::load('online.dm.PwOnlineDm');
        $time = Pw::getTime();
        $dm->setUid($this->loginUser->uid)->setUsername($this->loginUser->username)->setModifytime($time)->setCreatedtime($createdTime)->setGid($this->loginUser->gid)->setRequest($this->_mca);
        Wekit::load('online.PwUserOnline')->replaceInfo($dm);

        //脚印
        $service = Wekit::load('space.srv.PwSpaceService');
        $service->signVisitor($this->space->spaceUid, $this->loginUser->uid);
        $service->signToVisitor($this->space->spaceUid, $this->loginUser->uid);
    }
}
