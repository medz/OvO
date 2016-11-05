<?php

! defined('ACLOUD_PATH') && exit('Forbidden');
class ACloudSysCoreVerify
{
    public function verifyWithAES($ciphertext, $plaintext)
    {
        if (! $ciphertext || ! $plaintext) {
            return false;
        }
        $keysService = ACloudSysCoreCommon::loadSystemClass('keys', 'config.service');
        $keys = $keysService->getKey123(1);
        if (! $keys || strlen($keys ['key1']) != 128 || strlen($keys ['key2']) != 128 || strlen($keys ['key3']) != 128) {
            return false;
        }
        require_once Wind::getRealPath('ACLOUD:.system.core.ACloudSysCoreAes');
        $aesService = new ACloudSysCoreAes();
        $key = $aesService->encrypt($keys ['key3'], $keys ['key2'], 256);
        if (! $key) {
            return false;
        }
        if ($plaintext === ($ciphertext = $aesService->strcode($ciphertext, $key, 'DECODE')) && strlen($plaintext) == strlen($ciphertext)) {
            return true;
        }

        return false;
    }

    public function verifyWithOAuth($data, $key)
    {
        if (! is_array($data) || ! isset($data ['sign']) || ! $data ['sign'] || strlen($data ['sign']) != 32 || strlen($key) != 128) {
            return false;
        }
        $source_sign = $data ['sign'];
        unset($data ['sign']);
        require_once Wind::getRealPath('ACLOUD:.system.core.ACloudSysCoreOauth');
        $verify_sign = ACloudSysCoreOauth::createHttpSign(ACloudSysCoreOauth::createHttpQuery($data).$key);
        if ($verify_sign === $source_sign && strlen($verify_sign) == strlen($source_sign)) {
            return true;
        }

        return false;
    }

    public static function createSignWithOAuth($data)
    {
        $keysService = ACloudSysCoreCommon::loadSystemClass('keys', 'config.service');
        $key1 = $keysService->getKey1(1);
        if (! $key1 || strlen($key1) != 128 || ! is_array($data) || count($data) < 4) {
            return '';
        }
        require_once Wind::getRealPath('ACLOUD:.system.core.ACloudSysCoreOauth');

        return ACloudSysCoreOauth::createHttpSign(ACloudSysCoreOauth::createHttpQuery($data).$key1);
    }
}
