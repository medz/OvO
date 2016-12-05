<?php

!defined('ACLOUD_PATH') && exit('Forbidden');
require_once Wind::getRealPath('ACLOUD:system.core.ACloudSysCoreDefine');
require_once Wind::getRealPath('ACLOUD:system.core.ACloudSysCoreS');
class ACloudSysCoreCommon
{
    public static function getGlobal($key, $default = null)
    {
        return (isset($GLOBALS[$key])) ? $GLOBALS[$key] : $default;
    }

    public static function setGlobal($key, $value)
    {
        $GLOBALS[$key] = $value;
    }

    public static function getSiteSign()
    {
        $sign = self::parseDomainName(self::getSiteUnique());

        return substr(md5($sign), 8, 8);
    }

    public static function getSiteUnique()
    {
        return isset($_SERVER['SERVER_NAME']) ? trim($_SERVER['SERVER_NAME']) : trim(ACloudSysCoreDefine::ACLOUD_V);
    }

    public static function showError($message)
    {
        echo $message;
        exit();
    }

    public static function randCode($length = 32)
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890';
        $chars_length = (strlen($chars) - 1);
        $string = $chars[rand(0, $chars_length)];
        for ($i = 1; $i < $length; $i = strlen($string)) {
            $r = $chars[rand(0, $chars_length)];
            if ($r != $string[$i - 1]) {
                $string .= $r;
            }
        }

        return $string;
    }

    public static function getIp()
    {
        static $ip = null;
        if (!$ip) {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] && $_SERVER['REMOTE_ADDR']) {
                if (strstr($_SERVER['HTTP_X_FORWARDED_FOR'], ',')) {
                    $x = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                    $_SERVER['HTTP_X_FORWARDED_FOR'] = trim(end($x));
                }
                if (preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_X_FORWARDED_FOR'])) {
                    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
                }
            } elseif (isset($_SERVER['HTTP_CLIENT_IP']) && $_SERVER['HTTP_CLIENT_IP'] && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_CLIENT_IP'])) {
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            }
            if (!$ip && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['REMOTE_ADDR'])) {
                $ip = $_SERVER['REMOTE_ADDR'];
            }
            !$ip && $ip = 'Unknown';
        }

        return $ip;
    }

    public static function getDirName($path = null)
    {
        if (!empty($path)) {
            if (strpos($path, '\\') !== false) {
                return substr($path, 0, strrpos($path, '\\')).'/';
            } elseif (strpos($path, '/') !== false) {
                return substr($path, 0, strrpos($path, '/')).'/';
            }
        }

        return './';
    }

    public static function parseDomainName($url)
    {
        return ($url) ? trim(str_replace(array('http://', 'https://'), array(''), $url), '/') : '';
    }

    public static function listDir($path)
    {
        static $result = array();
        $path = rtrim($path, '/').'/';
        $folder_handle = opendir($path);
        while (false !== ($filename = readdir($folder_handle))) {
            if (strpos($filename, '.') !== 0) {
                if (is_dir($path.$filename.'/')) {
                    self::listDir($path.$filename.'/');
                } else {
                    $result[] = $path.$filename;
                }
            }
        }
        closedir($folder_handle);

        return $result;
    }

    public static function getSiteUserAgent()
    {
        list($key, $ua) = array('_ac_app_ua', '');
        if (!$_COOKIE || !isset($_COOKIE[$key]) || !$_COOKIE[$key]) {
            $ua = substr(md5($_SERVER['HTTP_USER_AGENT'].'\t'.rand(1000, 9999).'\t'.time()), 8, 18);
            setcookie($key, $ua, time() + 86400 * 365 * 5);
        }
        $ua = $ua ? $ua : $_COOKIE[$key];

        return (strlen($ua) == 18) ? ACloudSysCoreS::escapeChar($ua) : '';
    }

    public static function simpleResponse($code, $info = null)
    {
        $info = self::convert($info, 'UTF-8', self::getGlobal('g_charset'));

        return self::jsonEncode(array('code' => $code, 'info' => $info));
    }

    public static function jsonEncode($data)
    {
        if (function_exists('json_encode')) {
            return json_encode($data);
        }
        require_once Wind::getRealPath('ACLOUD:system.core.ACloudSysCoreJson');
        $jsonClass = new Services_JSON();

        return $jsonClass->encode($data);
    }

    public static function jsonDecode($data)
    {
        if (function_exists('json_decode')) {
            return json_decode($data);
        }
        require_once Wind::getRealPath('ACLOUD:system.core.ACloudSysCoreJson');
        $jsonClass = new Services_JSON();

        return $jsonClass->decode($data);
    }

    public static function loadSystemClass($className, $module = 'core')
    {
        static $classes = array();
        $className = str_replace(' ', '', sprintf('ACloudSys%s', ucwords(str_replace('.', ' ', $module.' '.$className))));
        if (isset($classes[$className])) {
            return $classes[$className];
        }
        $class = Wind::import(sprintf('ACLOUD:system.%s.%s', $module, $className));
        if (!class_exists($className)) {
            self::showError('cann`t find class');
        }
        $classes[$className] = new $className ();

        return $classes[$className];
    }

    public static function loadAppClass($appName)
    {
        static $classes = array();
        $appName = strtolower($appName);
        $class = sprintf('ACloudApp%sGuiding', ucfirst($appName));
        if (isset($classes[$class])) {
            return $classes[$class];
        }
        $class = Wind::import(sprintf('ACLOUD:app.%s.%s', $appName, $class));
        if (!class_exists($class)) {
            self::showError('cann`t find class');
        }
        $classes[$class] = new $class ();

        return $classes[$class];
    }

    public static function loadApps($page)
    {
        require_once Wind::getRealPath('ACLOUD:app.ACloudAppRouter');
        $router = new ACloudAppRouter();
        $apps = $router->getAppsByPage($page);
        foreach ($apps as $appname) {
            $appClass = self::loadAppClass($appname);
            $appClass->execute();
        }

        return $apps;
    }

    public static function arrayCombination($array, $ik, $vk)
    {
        if (!is_array($array)) {
            return array();
        }
        $tmp = array();
        foreach ($array as $a) {
            (isset($a[$ik]) && isset($a[$vk])) && $tmp[$a[$ik]] = $a[$vk];
        }

        return $tmp;
    }

    public static function arrayIntersectAssoc($array1, $array2)
    {
        if (!is_array($array1) || !is_array($array2)) {
            return array();
        }
        $tmp = array();
        if (!function_exists('array_intersect_assoc')) {
            $tmp = array_intersect_assoc($array1, $array2);
        } else {
            foreach ($array1 as $k => $v) {
                if (!isset($array2[$k]) || $array2[$k] != $v) {
                    continue;
                }
                $tmp[$k] = $v;
            }
        }

        return $tmp;
    }

    public static function refresh($url)
    {
        echo '<meta http-equiv="expires" content="0">';
        echo '<meta http-equiv="Pragma" content="no-cache">';
        echo '<meta http-equiv="Cache-Control" content="no-cache">';
        echo "<meta http-equiv='refresh' content='0;url=$url'>";
        exit();
    }

    public static function getMicrotime()
    {
        $t_array = explode(' ', microtime());

        return $t_array[0] + $t_array[1];
    }

    public static function convertToUTF8($str)
    {
        $charset = self::getGlobal('g_charset', 'gbk');

        return self::convert($str, 'utf-8', $charset);
    }

    public static function convertFromUTF8($str)
    {
        $charset = self::getGlobal('g_charset', 'gbk');

        return self::convert($str, $charset, 'utf-8');
    }

    public static function convert($str, $toEncoding, $fromEncoding, $ifMb = true)
    {
        if (strtolower($toEncoding) == strtolower($fromEncoding)) {
            return $str;
        }
        is_object($str) && $str = get_object_vars($str);
        if (is_array($str)) {
            foreach ($str as $key => $value) {
                is_object($value) && $value = get_object_vars($value);
                $str[$key] = self::convert($value, $toEncoding, $fromEncoding, $ifMb);
            }

            return $str;
        } else {
            if (function_exists('mb_convert_encoding') && $ifMb) {
                return mb_convert_encoding($str, $toEncoding, $fromEncoding);
            } else {
                static $sConvertor = null;
                !$toEncoding && $toEncoding = 'GBK';
                !$fromEncoding && $fromEncoding = 'GBK';
                if (!isset($sConvertor) && !is_object($sConvertor)) {
                    require_once Wind::getRealPath('ACLOUD:system.core.ACloudSysCoreCharset');
                    $sConvertor = new ACloudSysCoreCharset();
                }

                return $sConvertor->Convert($str, $fromEncoding, $toEncoding, !$ifMb);
            }
        }
    }

    public static function buildSecutiryCode($string, $key, $action = 'ENCODE')
    {
        $action != 'ENCODE' && $string = base64_decode($string);
        $code = '';
        $key = substr(md5($key), 8, 18);
        $keyLen = strlen($key);
        $strLen = strlen($string);
        for ($i = 0; $i < $strLen; $i++) {
            $k = $i % $keyLen;
            $code .= $string[$i] ^ $key[$k];
        }

        return $action != 'DECODE' ? base64_encode($code) : $code;
    }
}
