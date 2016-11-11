<?php
defined('WEKIT_VERSION') or exit(403);

/**
 * 后台菜单添加
 *
 * @author 蝦米 <>
 * @copyright
 * @license
 */
class App_XmReplyForLz_ConfigDo
{

    /**
     * 获取回帖仅楼主可见后台菜单
     *
     * @param array $config
     * @return array
     */
    public function getAdminMenu($config)
    {
        $config += array(
            'ext_xmReplyForLz' => array('回帖仅楼主可见', 'app/manage/*?app=xmReplyForLz', '', '', 'appcenter'),
        );
        return $config;
    }

    protected $configKey = 'app_xmReplyForLz';
    protected $loginUser;

    public function __construct()
    {
        $this->loginUser = Wekit::getLoginUser();
    }

    public function getConfigByCurrentUser()
    {
        return (boolean)$this->_getUserBo()->getPermission($this->configKey);
    }

    /**
     *
     * 获取后台权限设置 - 权限类别  钩子调用 s_permissionCategoryConfig
     */
    public function getPermissionCategoryConfig($config)
    {
        if (!is_array($config) || empty($config))
            return false;
        $config['bbs']['sub']['thread']['items'][] = $this->configKey;
        return $config;
    }

    /**
     * 获取后台权限设置 - 具体权限 钩子调用 s_permissionConfig
     *
     */
    public function getPermissionConfig($config)
    {
        if (!is_array($config) || empty($config))
            return false;
        $config[$this->configKey] = array('radio', 'basic', '允许使用回帖仅楼主可见', '');
        return $config;
    }

    private function _getUserBo()
    {
        Wind::import('SRV:user.bo.PwUserBo');
        return PwUserBo::getInstance($this->loginUser->uid);
    }
}

?>