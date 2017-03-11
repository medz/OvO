<?php

Wind::import('APPCENTER:service.srv.helper.PwApplicationHelper');

/**
 * 阿里云短信平台.
 *
 * @author jinlong.panjl <jinlong.panjl@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id$
 */
class PwPlatAliyun
{
    public $platUrl = '';

    public function __construct()
    {
        $filePath = Wind::getRealPath('ADMIN:conf.openplatformurl.php', true);
        $openPlatformUrl = Wind::getComponent('configParser')->parse($filePath);
        $this->platUrl = $openPlatformUrl.'index.php?m=appcenter&c=SmsManage';
    }

    /**
     * 获取剩余短信数量.
     *
     * @return int
     */
    public function getRestMobileMessage()
    {
        $url = PwApplicationHelper::acloudUrl(
            array('a' => 'forward', 'do' => 'getSiteLastNum'));
        $info = PwApplicationHelper::requestAcloudData($url);
        if (!is_array($info)) {
            return new PwError('APPCENTER:center.connect.fail');
        }
        if ($info['code'] !== '0') {
            return new PwError($info['msg']);
        }

        return $info['info'];
    }

    /**
     * 发送短信
     *
     * @return bool
     */
    public function sendMobileMessage($mobile, $content)
    {
        $content = Pw::convert($content, 'UTF-8');
        $url = PwApplicationHelper::acloudUrl(
            array('a' => 'forward', 'do' => 'sendSms', 'mobile' => $mobile, 'content' => $content));
        $info = PwApplicationHelper::requestAcloudData($url);
        if (!is_array($info)) {
            return new PwError('APPCENTER:center.connect.fail');
        }
        if ($info['code'] !== '0') {
            return new PwError($info['msg']);
        }

        return true;
    }
}
