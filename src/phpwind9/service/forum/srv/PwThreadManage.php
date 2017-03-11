<?php

/**
 * 帖子发布流程.
 *
 * -> 1.check 检查帖子发布运行环境
 * -> 2.appendDo(*) 增加帖子发布时的行为动作,例:投票、附件等(可选)
 * -> 3.execute 发布
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwThreadManage.php 22328 2012-12-21 08:46:57Z xiaoxia.xuxx $
 */
class PwThreadManage extends PwBaseHookService
{
    public $ds;
    public $data;
    public $user;

    protected $permission = null;
    protected $_fids = null;

    public function __construct(iPwDataSource $ds, PwUserBo $user)
    {
        $this->ds = $ds;
        $this->data = $ds->getData();
        $this->user = $user;
    }

    public function check()
    {
        if (!$this->data) {
            return new PwError('BBS:manage.error.empty.threads');
        }
        if (empty($this->_do)) {
            return new PwError('BBS:manage.undefined.thread.manage');
        }
        if (!$permission = $this->getPermission()) {
            return new PwError('BBS:manage.permission.deny');
        }
        if (($result = $this->runWithVerified('check', $permission)) !== true) {
            if ($result instanceof PwError) {
                return $result;
            }

            return new PwError('BBS:manage.permission.deny');
        }

        return true;
    }

    public function execute()
    {
        foreach ($this->data as $key => $value) {
            $this->runDo('gleanData', $value);
        }
        $this->runDo('run');

        return true;
    }

    public function getPermission()
    {
        if (!is_null($this->permission)) {
            return $this->permission;
        }
        if (!$this->permission = $this->user->getPermission('operate_thread', false, array())) {
            if (($fids = $this->getFids()) && $this->isBM($fids)) {
                $this->permission = $this->user->getPermission('operate_thread', true, array());
            }
        }

        return $this->permission;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getFids()
    {
        if (is_null($this->_fids)) {
            $fids = array();
            foreach ($this->data as $key => $value) {
                $fids[$value['fid']] = 1;
            }
            $this->_fids = array_keys($fids);
        }

        return $this->_fids;
    }

    public function isBM($fids)
    {
        $forums = Wekit::load('forum.PwForum')->fetchForum($fids);
        foreach ($forums as $key => $value) {
            if (!$this->_checkBM($this->user->username, $value['manager'], $value['uppermanager'])) {
                return false;
            }
        }

        return true;
    }

    protected function _checkBM($username, $manager, $uppermanager)
    {
        if ($manager && strpos($manager, ",$username,") !== false) {
            return true;
        }
        if ($uppermanager && strpos($uppermanager, ",$username,") !== false) {
            return true;
        }

        return false;
    }

    protected function _getInterfaceName()
    {
        return 'PwThreadManageDo';
    }
}
