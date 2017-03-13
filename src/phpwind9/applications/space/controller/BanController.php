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
class BanController extends PwBaseController
{
    public $space;

    public function beforeAction($handlerAdapter)
    {
        parent::beforeAction($handlerAdapter);
        $spaceUid = (int) $this->getInput('uid', 'get');
        if ($spaceUid < 1) {
            $userName = $this->getInput('username', 'get');
            $user = Wekit::load('user.PwUser')->getUserByName($userName);
            $spaceUid = isset($user['uid']) ? $user['uid'] : 0;
        }
        if ($spaceUid < 1) {
            $this->forwardRedirect(WindUrlHelper::createUrl('u/login/run/'));
        }
        $this->space = new PwSpaceBo($spaceUid);
        if (!$this->space->space['uid']) {
            $user = Wekit::load('user.PwUser')->getUserByUid($spaceUid);
            if ($user) {
                Wekit::load('space.dm.PwSpaceDm');
                $dm = new PwSpaceDm($spaceUid);
                $dm->setVisitCount(0);
                Wekit::load('space.PwSpace')->addInfo($dm);
                $this->space = new PwSpaceBo($spaceUid);
            } else {
                $this->forwardRedirect(WindUrlHelper::createUrl('u/login/run/'));
            }
        }
        $this->space->setTome($spaceUid, $this->loginUser->uid);
        $this->space->setVisitUid($this->loginUser->uid);
        $this->setTheme('space', null);
        if ($this->space->allowView('space')) {
            $this->forwardRedirect(WindUrlHelper::createUrl('space/index/run', ['uid' => $spaceUid]));
        }
    }

    public function run()
    {
        $this->setOutput($this->space, 'space');
    }
}
