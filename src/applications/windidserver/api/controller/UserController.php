<?php

Wind::import('APPS:api.controller.OpenBaseController');

/**
 * windid用户接口
 * the last known user to change this file in the repository  <$LastChangedBy: jieyin $>.
 *
 * @author $Author: jieyin $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: UserController.php 24768 2013-02-20 11:03:35Z jieyin $
 */
class UserController extends OpenBaseController
{
    public function loginAction()
    {
        list($userid, $password, $type, $ifcheck, $question, $answer) = $this->getInput(array('userid', 'password', 'type', 'ifcheck', 'question', 'answer'), 'post');
        !$type && $type = 2;
        $ifcheck = (bool) $ifcheck;
        $result = $this->_getUserService()->login($userid, $password, $type, $ifcheck, $question, $answer);
        $this->output($result);
    }

    public function synLoginAction()
    {
        $uid = (int) $this->getInput('uid', 'post');
        $out = '';
        $result = $this->_getNotifyService()->syn('synLogin', $uid, $this->appid);
        foreach ($result as $val) {
            $out .= '<script type="text/javascript" src="'.$val.'"></script>';
        }
        $this->output($out);
    }

    public function synLogoutAction()
    {
        $uid = (int) $this->getInput('uid', 'post');
        $out = '';
        $result = $this->_getNotifyService()->syn('synLogout', $uid, $this->appid);
        foreach ($result as $val) {
            $out .= '<script type="text/javascript" src="'.$val.'"></script>';
        }
        $this->output($out);
    }

    public function checkInputAction()
    {
        list($input, $type, $username, $uid) = $this->getInput(array('input', 'type', 'username', 'uid'), 'post');
        $result = $this->_getUserService()->checkUserInput($input, $type, $username, $uid);
        $this->output(WindidUtility::result($result));
    }

    public function checkQuestionAction()
    {
        list($question, $answer, $uid) = $this->getInput(array('question', 'answer', 'uid'), 'post');
        $result = $this->_getUserService()->checkQuestion($uid, $question, $answer);
        $this->output(WindidUtility::result($result));
    }

    public function getAction()
    {
        list($userid, $type, $fetch) = $this->getInput(array('userid', 'type', 'fetch'), 'get');
        !$type && $type = 1;
        !$fetch && $fetch = 1;
        $result = $this->_getUserService()->getUser($userid, $type, $fetch);
        $this->output($result);
    }

    /**
     * 批量获取用户信息.
     */
    public function fecthAction()
    {
        list($userids, $type, $fetch) = $this->getInput(array('userids', 'type', 'fetch'), 'get');
        !$type && $type = 1;
        !$fetch && $fetch = 1;
        $result = $this->_getUserService()->fecthUser($userids, $type, $fetch);
        $this->output($result);
    }

    /**
     * 增加一个用户.
     */
    public function addUserAction()
    {
        list(
            $username, $password, $email, $question, $answer, $regip, $realname, $profile, $regdate, $gender,
            $byear, $bmonth, $bday, $hometown, $location, $homepage, $qq, $msn, $aliww, $mobile, $alipay, $messages
        ) = $this->getInput(array(
            'username', 'password', 'email', 'question', 'answer', 'regip', 'realname', 'profile', 'regdate', 'gender',
            'byear', 'bmonth', 'bday', 'hometown', 'location', 'homepage', 'qq', 'msn', 'aliww', 'mobile', 'alipay', 'messages',
        ), 'post');

        Wind::import('WSRV:user.dm.WindidUserDm');
        $dm = new WindidUserDm();
        $dm->setUsername($username);
        $dm->setPassword($password);
        $dm->setEmail($email);

        isset($question) && $dm->setQuestion($question);
        isset($answer) && $dm->setAnswer($answer);
        isset($regip) && $dm->setRegip($regip);
        isset($realname) && $dm->setRealname($realname);
        isset($profile) && $dm->setProfile($profile);
        isset($regdate) && $dm->setRegdate($regdate);

        isset($gender) && $dm->setGender($gender);
        isset($byear) && $dm->setByear($byear);
        isset($bmonth) && $dm->setBmonth($bmonth);
        isset($bday) && $dm->setBday($bday);
        isset($hometown) && $dm->setHometown($hometown);
        isset($location) && $dm->setLocation($location);
        isset($homepage) && $dm->setHomepage($homepage);
        isset($qq) && $dm->setQq($qq);
        isset($msn) && $dm->setMsn($msn);
        isset($aliww) && $dm->setAliww($aliww);
        isset($mobile) && $dm->setMobile($mobile);
        isset($alipay) && $dm->setAlipay($alipay);
        isset($messages) && $dm->setMessageCount($messages);

        $result = $this->_getUserDs()->addUser($dm);
        if ($result instanceof WindidError) {
            $this->output($result->getCode());
        }

        $uid = (int) $result;
        $this->_getUserService()->defaultAvatar($uid, 'face');
        $this->_getNotifyService()->send('addUser', array('uid' => $uid), $this->appid);
        $this->output($uid);
    }

    /**
     * 修改用户信息.
     */
    public function editUserAction()
    {
        list(
            $uid, $username, $password, $old_password, $email, $question, $answer, $regip, $realname, $profile, $regdate,
            $gender, $byear, $bmonth, $bday, $hometown, $location, $homepage, $qq, $msn, $aliww, $mobile, $alipay,
            $addmessages, $messages
        ) = $this->getInput(array(
            'uid', 'username', 'password', 'old_password', 'email', 'question', 'answer', 'regip', 'realname', 'profile', 'regdate',
            'gender', 'byear', 'bmonth', 'bday', 'hometown', 'location', 'homepage', 'qq', 'msn', 'aliww', 'mobile', 'alipay',
            'addmessages', 'messages',
        ), 'post');

        Wind::import('WSRV:user.dm.WindidUserDm');
        $dm = new WindidUserDm($uid);
        isset($username) && $dm->setUsername($username);
        isset($password) && $dm->setPassword($password);
        isset($old_password) && $dm->setOldpwd($old_password);
        isset($email) && $dm->setEmail($email);
        isset($question) && $dm->setQuestion($question);
        isset($answer) && $dm->setAnswer($answer);
        isset($regip) && $dm->setRegip($regip);
        isset($realname) && $dm->setRealname($realname);
        isset($profile) && $dm->setProfile($profile);
        isset($regdate) && $dm->setRegdate($regdate);

        isset($gender) && $dm->setGender($gender);
        isset($byear) && $dm->setByear($byear);
        isset($bmonth) && $dm->setBmonth($bmonth);
        isset($bday) && $dm->setBday($bday);
        isset($hometown) && $dm->setHometown($hometown);
        isset($location) && $dm->setLocation($location);
        isset($homepage) && $dm->setHomepage($homepage);
        isset($qq) && $dm->setQq($qq);
        isset($msn) && $dm->setMsn($msn);
        isset($aliww) && $dm->setAliww($aliww);
        isset($mobile) && $dm->setMobile($mobile);
        isset($alipay) && $dm->setAlipay($alipay);

        isset($addmessages) && $dm->addMessages($addmessages);
        isset($messages) && $dm->setMessageCount($messages);

        $result = $this->_getUserDs()->editUser($dm);
        if ($result instanceof WindidError) {
            $this->output($result->getCode());
        }
        $this->_getNotifyService()->send('editUser', array('uid' => $uid, 'changepwd' => $dm->password ? 1 : 0), $this->appid);
        $this->output(WindidUtility::result(true));
    }

    /**
     * 删除一个用户.
     */
    public function deleteAction()
    {
        $uid = $this->getInput('uid', 'post');
        $result = false;
        if ($this->_getUserDs()->deleteUser($uid)) {
            $this->_getNotifyService()->send('deleteUser', array('uid' => $uid), $this->appid);
            $result = true;
        }
        $this->output(WindidUtility::result($result));
    }

    /**
     * 删除多个用户.
     */
    public function batchDeleteAction()
    {
        $uids = $this->getInput('uids', 'post');
        $result = false;
        if ($this->_getUserDs()->batchDeleteUser($uids)) {
            foreach ($uids as $uid) {
                $this->_getNotifyService()->send('deleteUser', array('uid' => $uid), $this->appid);
            }
            $result = true;
        }
        $this->output(WindidUtility::result($result));
    }

    /**
     * 获取用户积分.
     *
     * @param int $uid
     */
    public function getCreditAction()
    {
        $result = $this->_getUserService()->getUserCredit($this->getInput('uid', 'get'));
        $this->output($result);
    }

    /**
     * 批量获取用户积分.
     *
     * @param array $uids
     *
     * @return array
     */
    public function fecthCreditAction()
    {
        $uids = $this->getInput('uids', 'get');
        $result = $this->_getUserService()->fecthUserCredit($uids);
        $this->output($result);
    }

    /**
     * 更新用户积分.
     *
     * @param int $uid
     * @param int $cType (1-8)
     * @param int $value
     */
    public function editCreditAction()
    {
        $uid = (int) $this->getInput('uid', 'post');
        $cType = (int) $this->getInput('cType', 'post');
        $value = (int) $this->getInput('value', 'post');
        $isset = (bool) $this->getInput('isset', 'post');

        $result = $this->_getUserService()->editCredit($uid, $cType, $value, $isset);
        if ($result instanceof WindidError) {
            $this->output($result->getCode());
        }
        if ($result) {
            $this->_getNotifyService()->send('editCredit', array('uid' => $uid), $this->appid);
        }
        $this->output(WindidUtility::result($result));
    }

    public function editDmCreditAction()
    {
        $uid = (int) $this->getInput('uid', 'post');
        list($set, $add) = $this->getInput(array('set', 'add'), 'post');

        Wind::import('WSRV:user.dm.WindidCreditDm');
        $dm = new WindidCreditDm($uid);
        if ($set && is_array($set)) {
            foreach ($set as $key => $value) {
                $dm->setCredit($key, $value);
            }
        }
        if ($add && is_array($add)) {
            foreach ($add as $key => $value) {
                $dm->addCredit($key, $value);
            }
        }
        $result = $this->_getUserDs()->updateCredit($dm);
        if ($result instanceof WindidError) {
            $this->output($result->getCode());
        }
        if ($result) {
            $this->_getNotifyService()->send('editCredit', array('uid' => $uid), $this->appid);
        }
        $this->output(WindidUtility::result($result));
    }

    /**
     * 清空一个积分字段.
     *
     * @param int $num >8
     */
    public function clearCreditAction()
    {
        $result = $this->_getUserDs()->clearCredit($this->getInput('num', 'post'));
        $this->output(WindidUtility::result($result));
    }

    /**
     * 获取用户黑名单.
     *
     * @param int $uid
     *
     * @return array uids
     */
    public function getBlackAction()
    {
        $result = $this->_getUserBlackDs()->getBlacklist($this->getInput('uid', 'get'));
        $this->output($result);
    }

    public function fetchBlackAction()
    {
        $uids = $this->getInput('uids', 'get');
        $result = $this->_getUserBlackDs()->fetchBlacklist($uids);
        $this->output($result);
    }

    /**
     * 增加黑名单.
     *
     * @param int $uid
     * @param int $blackUid
     */
    public function addBlackAction()
    {
        $result = $this->_getUserBlackDs()->addBlackUser($this->getInput('uid', 'post'), $this->getInput('blackUid', 'post'));
        $this->output(WindidUtility::result($result));
    }

    public function replaceBlackAction()
    {
        $uid = $this->getInput('uid', 'post');
        $blackList = $this->getInput('blackList', 'post');
        $result = $this->_getUserBlackDs()->setBlacklist($uid, $blackList);
        $this->output(WindidUtility::result($result));
    }

    /**
     * 删除某的黑名单 $blackUid为空删除所有.
     *
     * @param int $uid
     * @param int $blackUid
     */
    public function delBlackAction()
    {
        $result = $this->_getUserService()->delBlack($this->getInput('uid', 'post'), $this->getInput('blackUid', 'post'));
        $this->output(WindidUtility::result($result));
    }

    protected function _getUserDs()
    {
        return Wekit::load('WSRV:user.WindidUser');
    }

    protected function _getUserService()
    {
        return Wekit::load('WSRV:user.srv.WindidUserService');
    }

    private function _getNotifyService()
    {
        return Wekit::load('WSRV:notify.srv.WindidNotifyService');
    }

    protected function _getUserBlackDs()
    {
        return Wekit::load('WSRV:user.WindidUserBlack');
    }
}
