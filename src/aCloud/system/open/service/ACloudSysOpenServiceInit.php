<?php

! defined('ACLOUD_PATH') && exit('Forbidden');
require_once Wind::getRealPath('ACLOUD:system.core.ACloudSysCoreHttp');
require_once Wind::getRealPath('ACLOUD:system.core.ACloudSysCoreVerify');

class ACloudSysOpenServiceInit
{
    public function initSecretKey($data)
    {
        if (! ACloudSysCoreS::isArray($data) || ! isset($data ['secure']) || ! $data ['secure']) {
            return array(false, 'apply_initkey_invalid_params');
        }
        $keysService = ACloudSysCoreCommon::loadSystemClass('keys', 'config.service');
        $key6 = $keysService->getKey6(2);
        if (! $key6 || strlen($key6) != 128) {
            return array(false, 'apply_initkey_invalid_key6');
        }
        unset($data ['a']);
        unset($data ['m']);
        unset($data ['c']);
        if (! ACloudSysCoreVerify::verifyWithOAuth($data, $key6)) {
            return array(false, 'apply_initkey_wrong_sign');
        }
        $keys = ACloudSysCoreCommon::buildSecutiryCode($data ['secure'], $key6, 'DECODE');
        list($key1, $key2, $key3) = explode('&', $keys);
        if (strlen($key1) != 128 || strlen($key2) != 128 || strlen($key3) != 128) {
            return array(false, 'apply_initkey_decodekey_fail');
        }
        if (! $keysService->updateKey123(1, $key1, $key2, $key3)) {
            return array(false, 'apply_initkey_fail');
        }

        return array(true, 'apply_initkey_success');
    }

    public function checkSecretKey($data)
    {
        if (! ACloudSysCoreS::isArray($data) || ! isset($data ['plaintext']) || ! $data ['plaintext'] || ! isset($data ['ciphertext']) || ! $data ['ciphertext']) {
            return array(false, 'apply_checkkey_invalid_params');
        }
        $keysService = ACloudSysCoreCommon::loadSystemClass('keys', 'config.service');
        $key6 = $keysService->getKey6(2);
        if (! $key6 || strlen($key6) != 128) {
            return array(false, 'apply_checkkey_invalid_key6');
        }
        unset($data ['a']);
        unset($data ['m']);
        unset($data ['c']);
        if (! ACloudSysCoreVerify::verifyWithOAuth($data, $key6)) {
            return array(false, 'apply_checkkey_wrong_sign');
        }
        if (! ACloudSysCoreVerify::verifyWithAES($data ['ciphertext'], $data ['plaintext'])) {
            return array(false, 'apply_checkkey_fail');
        }

        return array(true, 'apply_checkkey_success');
    }
}
