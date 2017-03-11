<?php
/**
 * 站点状态
 *
 * @author $Author: jinlong.panjl $ foxsee@aliyun.com
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwSiteStatusService.php 19357 2012-10-13 06:56:54Z jinlong.panjl $
 */
class PwSiteStatusService
{
    private $_user = array();

    private $_config = array();

    /**
     * 站点访问状态
     *
     * @param PwUserBo $user
     * @param array    $config
     */
    public function siteStatus($user, $config)
    {
        if (!$user instanceof PwUserBo) {
            return new PwError('SITE:source.error');
        }
        $this->_user = $user;
        $this->_config = $config;
        switch ($this->_config['visit.state']) {
            case '0':
                return true;
            case '1':
                return $this->protectVisit();
            case '2':
                return $this->founderVisit();
            default:

                return true;
        }
    }

    protected function founderVisit()
    {
        if (in_array($this->_user->uid, (array) $this->_config['founder'])) {
            return true;
        } else {
            return new PwError($this->_config['visit.message']);
        }
    }

    protected function protectVisit()
    {
        if (!empty($this->_config['visit.gid'])) {
            if (in_array($this->_user->gid, $this->_config['visit.gid'])) {
                return true;
            }
        }
        if (!empty($this->_config['visit.ip'])) {
            if ($this->formatAllowIP($this->_user->ip)) {
                return true;
            }
        }
        if (!empty($this->_config['visit.member'])) {
            if (in_array($this->_user->username, explode(',', $this->_config['visit.member']))) {
                return true;
            }
        }

        return new PwError($this->_config['visit.message']);
    }

    protected function formatAllowIP($ip = '')
    {
        if (empty($this->_config['visit.ip']) || empty($ip)) {
            return false;
        }
        $allowIPs = explode(',', $this->_config['visit.ip']);
        foreach ($allowIPs as $allowIP) {
            if (($pos = strpos($allowIP, '*')) !== false) {
                $_allowIP = substr($allowIP, 0, $pos - 1);
                $_pos = strpos($ip, '.');
                $_ip = substr($ip, 0, $_pos);
                if ($_allowIP == $_ip) {
                    return true;
                }
            } elseif ($ip == $allowIP) {
                return true;
            } else {
                return false;
            }
        }
    }
}
