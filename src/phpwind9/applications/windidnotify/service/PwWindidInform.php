<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>.
 *
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwWindidInform.php 29745 2013-06-28 09:07:39Z gao.wanggao $
 */
class PwWindidInform
{
    /**
     * 用于通讯测试，传递参数test
     * Enter description here ...
     *
     * @param unknown_type $params
     */
    public function test($testdata)
    {
        return $testdata ? true : false;
    }

    public function synLogin($uid)
    {
        Wind::import('SRC:service.user.bo.PwUserBo');
        Wind::import('SRC:service.user.srv.PwLoginService');
        $userBo = new PwUserBo($uid);
        if ($userBo->isExists() && !Pw::getstatus($userBo->info['status'], PwUser::STATUS_UNACTIVE)) {
            $srv = new PwLoginService();
            $ip = Wind::getApp()->getRequest()->getClientIp();
            $srv->setLoginCookie($userBo, $ip, 1);
        }
        exit;
        //return true;
    }

    public function synLogout($uid)
    {
        Wind::import('SRC:service.user.srv.PwUserService');
        $srv = new PwUserService();
        $srv->logout();
        exit;
        //return true;
    }

    public function addUser($uid)
    {
        Wind::import('SRC:service.user.srv.PwRegisterService');
        $srv = new PwRegisterService();
        $result = $srv->sysUser($uid);
        if ($result instanceof PwError) {
            return $result->getError();
        }

        return true;
    }

    public function editUser($uid, $changepwd = 0)
    {
        $result = $this->_getUserDs()->synEditUser($uid, $changepwd);

        return $result;
    }

    public function editUserInfo($uid)
    {
        $result = $this->_getUserDs()->synEditUser($uid);

        return $result;
    }

    public function uploadAvatar($uid)
    {
        PwSimpleHook::getInstance('update_avatar')->runDo($uid);

        return true;
    }

    public function editCredit($uid)
    {
        $result = $this->_getUserDs()->synEditUser($uid);

        return $result;
    }

    public function editMessageNum($uid)
    {
        $result = Wekit::load('message.srv.PwMessageService')->synEditUser($uid);

        return $result;
    }

    public function deleteUser($uid)
    {
        $userSer = new PwClearUserService($uid);
        $clear = $userSer->getClearTypes();
        $std = PwWindidStd::getInstance('user');
        $std->setMethod('deleteUser', $uid);
        $result = $userSer->run(array_keys($clear));
        if ($result instanceof PwError) {
            return false;
        }

        return true;
    }

    public function setCredits()
    {
        $wcredit = WindidApi::api('config')->getValues('credit');
        $wcredits = $wcredit['credits'];

        $credit = Wekit::C()->getValues('credit');
        $credits = $credit['credits'];

        foreach ($wcredits as $key => $value) {
            isset($credits[$key]) || $credits[$key] = [];
            $credits[$key]['name'] = $value['name'];
            $credits[$key]['unit'] = $value['unit'];
        }
        foreach ($credits as $key => $value) {
            if (!isset($wcredits[$key])) {
                unset($credits[$key]);
            }
        }
        Wekit::load('credit.srv.PwCreditSetService')->setLocalCredits($credits);

        return true;
    }

    public function alterAvatarUrl()
    {
        Wekit::C()->setConfig('site', 'avatarUrl', WindidApi::api('avatar')->getAvatarUrl());

        return true;
    }

    private function _getUserDs()
    {
        return Wekit::load('user.PwUser');
    }
}
