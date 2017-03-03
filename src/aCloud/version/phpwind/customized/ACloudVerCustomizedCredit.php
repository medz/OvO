<?php

!defined('ACLOUD_PATH') && exit('Forbidden');

class ACloudVerCustomizedCredit extends ACloudVerCustomizedBase
{
    public function fetchCreditType()
    {
        return $this->buildResponse(0, PwCreditBo::getInstance()->cType);
    }

    public function setCredit($uid, $ctype, $point, $appName)
    {
        $dm = new PwCreditDm($uid);
        $dm->addCredit($ctype, $point);
        $result = $this->_loadPwUserDS()->updateCredit($dm);
        if (!$result) {
            $user = new PwUserBo($uid);
            PwCreditBo::getInstance()->addLog('app_default', array($ctype => $point), $user, array('appname' => $appName));
            PwCreditBo::getInstance()->execute();

            return $this->buildResponse(0, $point);
        }

        return $this->buildResponse(-1, '设置积分失败');
    }

    private function _loadPwUserDS()
    {
        return Wekit::load('SRV:user.PwUser');
    }
}
