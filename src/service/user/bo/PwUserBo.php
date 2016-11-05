<?php

defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 单个用户的业务对象
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwUserBo.php 24736 2013-02-19 09:24:40Z jieyin $
 * @package src.service.user.bo
 */
class PwUserBo
{
    public $uid;
    public $username;
    public $gid;
    public $groups = array();
    public $ip;
    public $info = array();

    private $_groupInfo = array();
    private $_permission = array();

    private static $_userBo = array();

    /**
     * 构造函数信息
     *
     * @param int $uid 用户ID
     */
    public function __construct($uid, $fetchAll = false)
    {
        $this->info = $uid ? $this->_getUserDs()->getUserByUid($uid, $fetchAll ? PwUser::FETCH_ALL : (PwUser::FETCH_MAIN | PwUser::FETCH_DATA)) : array();
        if ($this->info) {
            $this->uid = $uid;
            $this->username = $this->info['username'];
            $this->gid = ($this->info['groupid'] == 0) ? $this->info['memberid'] : $this->info['groupid'];
            $this->ip = $this->info['lastloginip'];
            if ($this->info['groups']) {
                $this->groups = explode(',', $this->info['groups']);
            }
            $this->groups[] = $this->gid;
        } else {
            $this->reset();
        }
    }

    /**
     * 获取一个 PwUserBo 对象实例，并缓存
     *
     * @param  int    $uid
     * @return object
     */
    public static function getInstance($uid)
    {
        if (!isset(self::$_userBo[$uid])) {
            self::$_userBo[$uid] = new self($uid);
        }

        return self::$_userBo[$uid];
    }

    public static function pushUser(PwUserBo $bo)
    {
        self::$_userBo[$bo->uid] = $bo;
    }

    /**
     * 判断是否存在用户信息
     *
     * @return bool
     */
    public function isExists()
    {
        return !empty($this->uid);
    }

    /**
     * 判断用户的用户组是否在指定组中
     *
     * @param  array $groups
     * @return bool
     */
    public function inGroup($groups)
    {
        if (!$groups || !is_array($groups)) {
            return false;
        }
        if (in_array($this->gid, $groups)) {
            return true;
        }
        if ($this->groups) {
            return !!array_intersect($this->groups, $groups);
        }

        return false;
    }

    /**
     * 获取用户某个权限点的权限值
     *
     * @param  string $key          权限键值<支持多级数组调用，例如:a.b.c 将调用$a['b']['c']>
     * @param  bool   $isBM         是否是版主<当所指定的权限是版块限定的权限时，需要指定当前是否是版主身份>
     * @param  mixed  $defaultValue
     * @return mixed
     */
    public function getPermission($key, $isBM = false, $defaultValue = '')
    {
        $this->_initGroup();
        $keys = explode('.', $key);
        $key = array_shift($keys);
        if (!isset($this->_permission[$key]) || $this->_permission[$key]['type'] == 'systemforum' && $this->gid == 5 && !$isBM) {
            return $defaultValue;
        }
        $result = $this->_permission[$key]['value'];
        if ($keys) {
            foreach ($keys as $_key) {
                if (!is_array($result) || !isset($result[$_key])) {
                    return $defaultValue;
                }
                $result = $result[$_key];
            }
        }

        return $result === '' ? $defaultValue : $result;
    }

    /**
     * 与指定用户比较权限等级
     *
     * @param  array $uids 用户id序列
     * @return bool
     */
    public function comparePermission($uids)
    {
        is_array($uids) || $uids = array($uids);
        $level = $this->getPermission('manage_level');
        $users = $this->_getUserDs()->fetchUserByUid($uids);
        if ($gids = array_diff(Pw::collectByKey($users, 'groupid'), array('0'))) {
            $array = $this->_getPermissionDs()->getPermissionByRkeyAndGids('manage_level', $gids);
            foreach ($array as $key => $value) {
                if ($value['rvalue'] && $level < $value['rvalue']) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * 获得用户某一类型的积分
     *
     * @param  int $creditType
     * @return int
     */
    public function getCredit($creditType)
    {
        return $this->info['credit'.$creditType];
    }

    /**
     * 获取用户组信息
     *
     * @return array
     */
    public function getGroupInfo($key = null)
    {
        $this->_initGroup();

        return $key ? $this->_groupInfo[$key] : $this->_groupInfo;
    }

    /**
     * 获得用户在线时间 (单位秒)
     *
     * @return int
     */
    public function getOnline()
    {
        $online = $this->info['online'];

        return $online + (time() - $this->info['lastvisit']);
    }

    /**
     * 展示用户的积分变更
     *
     * @return array
     */
    public function showCreditNotice()
    {
        return empty($this->info['last_credit_affect_log']) ? false : true;
    }

    /**
     * 用户重置操作
     *
     * @return bool
     */
    public function reset()
    {
        $this->uid = 0;
        $this->gid = 2;
        $this->username = '游客';
        $this->info = array(
            'lastpost' => Pw::getCookie('guest_lastpost'),
        );
    }

    /**
     * 导入新用户组信息和权限
     *
     * @param int $gid 用户组id
     */
    public function resetGid($gid)
    {
        $this->gid = $gid;
        $this->_groupInfo = array();
        $this->_initGroup();
    }

    /**
     * 初始化当前用户组信息及权限
     */
    private function _initGroup()
    {
        if ($this->_groupInfo) {
            return;
        }
        if (($group = Wekit::cache()->get('group', $this->gid)) === false) {
            $group = Wekit::cache()->get('group', 1);
        }
        $this->_groupInfo = array('name' => $group['name'], 'type' => $group['type'], 'image' => $group['image'], 'points' => $group['points']);
        $this->_permission = $group['permission'];
    }

    /**
     * 获取用户
     *
     * @return PwUser
     */
    private function _getUserDs()
    {
        return Wekit::load('user.PwUser');
    }

    private function _getPermissionDs()
    {
        return Wekit::load('usergroup.PwUserPermission');
    }
}
