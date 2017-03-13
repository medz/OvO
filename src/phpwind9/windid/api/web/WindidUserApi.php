<?php
/**
 * windid用户接口
 * the last known user to change this file in the repository  <$LastChangedBy: jieyin $>.
 *
 * @author $Author: jieyin $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: WindidUserApi.php 24943 2013-02-27 03:52:21Z jieyin $
 */
class WindidUserApi
{
    /**
     * 用户登录.
     *
     * @param string $userid
     * @param string $password
     * @param int    $type     $type 1-uid ,2-username 3-email
     * @param string $question
     * @param string $answer
     *
     * @return array
     */
    public function login($userid, $password, $type = 2, $ifcheck = false, $question = '', $answer = '')
    {
        $params = [
            'userid'   => $userid,
            'password' => $password,
            'type'     => $type,
            'ifcheck'  => $ifcheck,
            'question' => $question,
            'answer'   => $answer,
        ];

        return WindidApi::open('user/login', [], $params);
    }

    /**
     * 本地登录成功后同步登录通知.
     *
     * @param int $uid
     *
     * @return string
     */
    public function synLogin($uid)
    {
        $params = [
            'uid' => $uid,
        ];

        return WindidApi::open('user/synLogin', [], $params);
    }

    /**
     * 本地登出成功后同步登出.
     *
     * @param int    $uid
     * @param string $backurl
     *
     * @return string
     */
    public function synLogout($uid)
    {
        $params = [
            'uid' => $uid,
        ];

        return WindidApi::open('user/synLogout', [], $params);
    }

    /**
     * 检查用户提交的信息是否符合windid配置规范.
     *
     * @param string $input
     * @param int    $type  综合检查类型： 1-用户名, 2-密码,  3-邮箱
     * @param int    $uid
     *
     * @return bool
     */
    public function checkUserInput($input, $type, $username = '', $uid = 0)
    {
        $params = [
            'input'    => $input,
            'type'     => $type,
            'username' => $username,
            'uid'      => $uid,
        ];

        return WindidApi::open('user/checkInput', [], $params);
    }

    /**
     * 验证安全问题.
     *
     * @param int $uid
     * @param int $question
     * @param int $answer
     *
     * @return bool
     */
    public function checkQuestion($uid, $question, $answer)
    {
        $params = [
            'uid'      => $uid,
            'question' => $question,
            'answer'   => $answer,
        ];

        return WindidApi::open('user/checkQuestion', [], $params);
    }

    /**
     * 获取一个用户基本资料.
     *
     * @param multi $userid
     * @param int   $type      1-uid ,2-username 3-email
     * @param int   $fetchMode
     *
     * @return array
     */
    public function getUser($userid, $type = 1, $fetchMode = 1)
    {
        $params = [
            'userid' => $userid,
            'type'   => $type,
            'fetch'  => $fetchMode,
        ];

        return WindidApi::open('user/get', $params);
    }

    /**
     * 批量获取用户信息.
     *
     * @param array $uids/$username
     * @param int   $type           1-uid ,2-username
     * @param int   $fetchMode
     *
     * @return array
     */
    public function fecthUser($userids, $type = 1, $fetchMode = 1)
    {
        $params = [
            'userids' => $userids,
            'type'    => $type,
            'fetch'   => $fetchMode,
        ];

        return WindidApi::open('user/fecth', $params);
    }

    /**
     * 用户注册.
     *
     * @param string $username
     * @param string $email
     * @param string $password
     * @param string $question
     * @param string $answer
     * @param string $regip
     *
     * @return int
     */
    public function register($username, $email, $password, $question = '', $answer = '', $regip = '')
    {
        $params = [
            'username' => $username,
            'email'    => $email,
            'password' => $password,
            'question' => $question,
            'answer'   => $answer,
            'regip'    => $regip,
        ];

        return WindidApi::open('user/addUser', [], $params);
    }

    /**
     * 添加用户对象接口，使用前必须使用WidnidApi::getDm('user') 设置数据.
     *
     * @param WindidUserDm $dm
     *
     * @return int
     */
    public function addDmUser(WindidUserDm $dm)
    {
        /*
        if (($result = $dm->beforeAdd()) instanceof WindidError) {
            return $result->getCode();
        }
        */
        $params = $dm->getData();
        $params['password'] = $dm->password;
        unset($params['salt'], $params['safecv']);

        return WindidApi::open('user/addUser', [], $params);
    }

    /**
     * 修改用户基本信息.
     *
     * @param int    $uid
     * @param string $password 是否校验密码
     * @param array  $editInfo array('username', 'password', 'email', 'question', 'answer')
     */
    public function editUser($uid, $password, $editInfo)
    {
        $dm = $this->_getUserService()->getBaseUserDm($uid, $password, $editInfo);

        return $this->editDmUser($dm);
    }

    /**
     * 修改用户资料.
     *
     * @param int   $uid
     * @param array $editInfo
     */
    public function editInfo($uid, $editInfo)
    {
        $dm = $this->_getUserService()->getInfoUserDm($uid, $editInfo);

        return $this->editDmUser($dm);
    }

    public function editDmUser(WindidUserDm $dm)
    {
        /*
        if (($result = $dm->beforeUpdate()) instanceof WindidError) {
            return $result->getCode();
        }
        */
        $params = $dm->getData();
        $params['uid'] = $dm->uid;
        if (isset($params['password'])) {
            $params['password'] = $dm->password;
        }
        unset($params['salt'], $params['safecv']);

        return WindidApi::open('user/editUser', [], $params);
    }

    /**
     * 删除一个用户.
     *
     * @param int $uid
     */
    public function deleteUser($uid)
    {
        $params = [
            'uid' => $uid,
        ];

        return WindidApi::open('user/delete', [], $params);
    }

    /**
     * 删除多个用户.
     *
     * @param array $uids
     */
    public function batchDeleteUser($uids)
    {
        $params = [
            'uids' => $uids,
        ];

        return WindidApi::open('user/batchDelete', [], $params);
    }

    /**
     * 获取用户积分.
     *
     * @param int $uid
     */
    public function getUserCredit($uid)
    {
        $params = [
            'uid' => $uid,
        ];

        return WindidApi::open('user/getCredit', $params);
    }

    /**
     * 批量获取用户积分.
     *
     * @param array $uids
     *
     * @return array
     */
    public function fecthUserCredit($uids)
    {
        $params = [
            'uids' => $uids,
        ];

        return WindidApi::open('user/fecthCredit', $params);
    }

    /**
     * 更新用户积分.
     *
     * @param int $uid
     * @param int $cType (1-8)
     * @param int $value
     */
    public function editCredit($uid, $cType, $value, $isset = false)
    {
        $params = [
            'uid'   => $uid,
            'cType' => $cType,
            'value' => $value,
            'isset' => $isset,
        ];

        return WindidApi::open('user/editCredit', [], $params);
    }

    public function editDmCredit(WindidCreditDm $dm)
    {
        $params = [
            'uid' => $dm->uid,
            'set' => [],
            'add' => [],
        ];
        $data = $dm->getData();
        $increase = $dm->getIncreaseData();
        if ($data) {
            foreach ($data as $key => $value) {
                $params['set'][substr($key, 6)] = $value;
            }
        }
        if ($increase) {
            foreach ($increase as $key => $value) {
                $params['add'][substr($key, 6)] = $value;
            }
        }

        return WindidApi::open('user/editDmCredit', [], $params);
    }

    /**
     * 清空一个积分字段.
     *
     * @param int $num >8
     */
    public function clearCredit($num)
    {
        $params = [
            'num' => $num,
        ];

        return WindidApi::open('user/clearCredit', [], $params);
    }

    /**
     * 获取用户黑名单.
     *
     * @param int $uid
     *
     * @return array uids
     */
    public function getBlack($uid)
    {
        $params = [
            'uid' => $uid,
        ];

        return WindidApi::open('user/getBlack', $params);
    }

    public function fetchBlack($uids)
    {
        $params = [
            'uids' => $uids,
        ];

        return WindidApi::open('user/fetchBlack', $params);
    }

    /**
     * 增加黑名单.
     *
     * @param int $uid
     * @param int $blackUid
     */
    public function addBlack($uid, $blackUid)
    {
        $params = [
            'uid'      => $uid,
            'blackUid' => $blackUid,
        ];

        return WindidApi::open('user/addBlack', [], $params);
    }

    public function replaceBlack($uid, $blackList)
    {
        $params = [
            'uid'       => $uid,
            'blackList' => $blackList,
        ];

        return WindidApi::open('user/replaceBlack', [], $params);
    }

    /**
     * 删除某的黑名单 $blackUid为空删除所有.
     *
     * @param int $uid
     * @param int $blackUid
     */
    public function delBlack($uid, $blackUid = '')
    {
        $params = [
            'uid'      => $uid,
            'blackUid' => $blackUid,
        ];

        return WindidApi::open('user/delBlack', [], $params);
    }
}
