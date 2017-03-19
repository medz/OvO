<?php

Wind::import('APPS:windid.admin.WindidBaseController');
/**
 * the last known user to change this file in the repository  <$LastChangedBy: jieyin $>.
 *
 * @author $Author: jieyin $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: RegistController.php 24408 2013-01-30 03:55:08Z jieyin $
 */
class RegistController extends WindidBaseController
{
    public function run()
    {
        $config = Wekit::C()->getValues('reg');
        is_array($config['security.password']) || $config['security.password'] = [];
        //$config['security.ban.username'] = implode(',', $config['security.ban.username']);
        $this->setOutput($config, 'config');
    }

    public function doregistAction()
    {
        $username_max = abs($this->getInput('securityUsernameMax', 'post'));
        $username_min = abs($this->getInput('securityUsernameMin', 'post'));
        $username_max = max([$username_max, $username_min]);
        $username_max > 15 && $username_max = 15;
        $username_min = min([$username_max, $username_min]);
        $username_min < 1 && $username_min = 1;
        $password_max = abs($this->getInput('securityPasswordMax', 'post'));
        $password_min = abs($this->getInput('securityPasswordMin', 'post'));
        $password_max = max([$password_max, $password_min]);
        $password_min = min([$password_max, $password_min]);
        $password_min < 1 && $password_min = 1;
        $password_security = $this->getInput('securityPassword', 'post');

        $ipTime = ceil($this->getInput('securityIp', 'post'));
        if ($ipTime < 0) {
            $ipTime = 1;
        }

        $config = new PwConfigSet('reg');
        $config->set('security.username.max', $username_max)
            ->set('security.username.min', $username_min)
            ->set('security.password', $password_security)
            ->set('security.password.max', $password_max)
            ->set('security.password.min', $password_min)
            ->set('security.ban.username', trim($this->getInput('securityBanUsername', 'post')))
        ->flush();
        $this->showMessage('WINDID:success');
    }
}
