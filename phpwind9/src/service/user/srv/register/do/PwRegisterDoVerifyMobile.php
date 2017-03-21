<?php


/**
 * 注册 - 手机.
 *
 * @author jinlong.panjl <jinlong.panjl@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id$
 */
class PwRegisterDoVerifyMobile extends PwRegisterDoBase
{
    /**
     * 构造函数.
     *
     * @param PwRegisterService $pwUserRegister
     * @param string            $code
     */
    public function __construct(PwRegisterService $pwUserRegister)
    {
        parent::__construct($pwUserRegister);
    }

    /* (non-PHPdoc)
     * @see PwRegisterDoBase::afterRegister()
     */
    public function afterRegister(PwUserInfoDm $userDm)
    {
        if (($result = $this->_check($userDm)) !== true) {
            return false;
        }
        $mobile = $userDm->getField('mobile');
        $this->_getDs()->replaceMobile($userDm->uid, $mobile);

        return true;
    }

    /* (non-PHPdoc)
     * @see PwRegisterDoBase::afterRegister()
     */
    protected function _check(PwUserInfoDm $userDm)
    {
        if (! $userDm->uid) {
            return false;
        }
        $config = Wekit::C('register');
        if (! $config['active.phone']) {
            return false;
        }
        $mobile = $userDm->getField('mobile');
        $mobileCode = $userDm->getField('mobileCode');
        if (($mobileCheck = Wekit::load('mobile.srv.PwMobileService')->checkVerify($mobile, $mobileCode)) instanceof PwError) {
            return false;
        }

        return true;
    }

    /**
     * @return PwUserMobile
     */
    protected function _getDs()
    {
        return Wekit::load('user.PwUserMobile');
    }
}
