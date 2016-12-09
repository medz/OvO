<?php

!defined('ACLOUD_PATH') && exit('Forbidden');
class ACloudSysCoreS
{
    public static function isArray($params)
    {
        return (!is_array($params) || !count($params)) ? false : true;
    }

    public static function inArray($param, $params)
    {
        return (!in_array((string) $param, (array) $params)) ? false : true;
    }

    public static function htmlEscape($param)
    {
        return trim(htmlspecialchars($param, ENT_QUOTES));
    }

    public static function stripTags($param)
    {
        return trim(strip_tags($param));
    }

    public static function gp($keys, $method = null, $cvtype = 1, $istrim = true)
    {
        !is_array($keys) && $keys = array($keys);
        $result = array();
        foreach ($keys as $key) {
            if ($key == 'GLOBALS') {
                continue;
            }
            $GLOBALS[$key] = null;
            if ($method != 'P' && isset($_GET[$key])) {
                $GLOBALS[$key] = $_GET[$key];
            } elseif ($method != 'G' && isset($_POST[$key])) {
                $GLOBALS[$key] = $_POST[$key];
            }
            if (isset($GLOBALS[$key]) && !empty($cvtype) || $cvtype == 2) {
                $GLOBALS[$key] = self::escapeChar($GLOBALS[$key], $cvtype == 2, $istrim);
            }
            $result[] = $GLOBALS[$key];
        }

        return $result;
    }

    public static function getGP($key, $method = null)
    {
        if ($method == 'G' || $method != 'P' && isset($_GET[$key])) {
            return $_GET[$key];
        }

        return $_POST[$key];
    }

    public static function escapePath($fileName, $ifCheck = true)
    {
        if (!self::_escapePath($fileName, $ifCheck)) {
            exit('Forbidden');
        }

        return $fileName;
    }

    private static function _escapePath($fileName, $ifCheck = true)
    {
        $tmpname = strtolower($fileName);
        $tmparray = array('://', "\0");
        $ifCheck && $tmparray[] = '..';
        if (str_replace($tmparray, '', $tmpname) != $tmpname) {
            return false;
        }

        return true;
    }

    public static function escapeDir($dir)
    {
        $dir = str_replace(array("'", '#', '=', '`', '$', '%', '&', ';'), '', $dir);

        return rtrim(preg_replace('/(\/){2,}|(\\\){1,}/', '/', $dir), '/');
    }

    public static function escapeChar($mixed, $isint = false, $istrim = false)
    {
        if (is_array($mixed)) {
            foreach ($mixed as $key => $value) {
                $mixed[$key] = self::escapeChar($value, $isint, $istrim);
            }
        } elseif ($isint) {
            $mixed = (int) $mixed;
        } elseif (!is_numeric($mixed) && ($istrim ? $mixed = trim($mixed) : $mixed) && $mixed) {
            $mixed = self::escapeStr($mixed);
        }

        return $mixed;
    }

    public static function escapeStr($string)
    {
        $string = str_replace(array("\0", '%00', "\r"), '', $string);
        $string = preg_replace(array('/[\\x00-\\x08\\x0B\\x0C\\x0E-\\x1F]/', '/&(?!(#[0-9]+|[a-z]+);)/is'), array('', '&amp;'), $string);
        $string = str_replace(array('%3C', '<'), '&lt;', $string);
        $string = str_replace(array('%3E', '>'), '&gt;', $string);
        $string = str_replace(array('"', "'", "\t", '  '), array('&quot;', '&#39;', '    ', '&nbsp;&nbsp;'), $string);

        return $string;
    }

    public static function checkVar(&$var)
    {
        if (is_array($var)) {
            foreach ($var as $key => $value) {
                self::checkVar($var[$key]);
            }
        } else {
            $var = str_replace(array('..', ')', '<', '='), array('&#46;&#46;', '&#41;', '&#60;', '&#61;'), $var);
        }
    }

    public static function slashes(&$array)
    {
        if (is_array($array)) {
            foreach ($array as $key => $value) {
                if (is_array($value)) {
                    self::slashes($array[$key]);
                } else {
                    $array[$key] = addslashes($value);
                }
            }
        }
    }

    public static function getServer($keys)
    {
        $server = array();
        $array = (array) $keys;
        foreach ($array as $key) {
            $server[$key] = null;
            if (isset($_SERVER[$key])) {
                $server[$key] = str_replace(array('<', '>', '"', "'", '%3C', '%3E', '%22', '%27', '%3c', '%3e'), '', $_SERVER[$key]);
            }
        }

        return is_array($keys) ? $server : $server[$keys];
    }

    public static function sqlEscape($var, $strip = true, $isArray = false)
    {
        if (is_array($var)) {
            if (!$isArray) {
                return " '' ";
            }
            foreach ($var as $key => $value) {
                $var[$key] = trim(self::sqlEscape($value, $strip));
            }

            return $var;
        } elseif (is_numeric($var)) {
            return " '".$var."' ";
        } else {
            return " '".addslashes($strip ? stripslashes($var) : $var)."' ";
        }
    }

    public static function sqlImplode($array, $strip = true)
    {
        return implode(',', self::sqlEscape($array, $strip, true));
    }

    public static function sqlSingle($array, $strip = true)
    {
        if (!self::isArray($array)) {
            return '';
        }
        $array = self::sqlEscape($array, $strip, true);
        $str = '';
        foreach ($array as $key => $val) {
            $str .= ($str ? ', ' : ' ').self::sqlMetadata($key).'='.$val;
        }

        return $str;
    }

    public static function sqlMulti($array, $strip = true)
    {
        if (!self::isArray($array)) {
            return '';
        }
        $str = '';
        foreach ($array as $val) {
            if (!empty($val) && self::isArray($val)) {
                $str .= ($str ? ', ' : ' ').'('.self::sqlImplode($val, $strip).') ';
            }
        }

        return $str;
    }

    public static function sqlLimit($start, $num = false)
    {
        return ' LIMIT '.($start <= 0 ? 0 : (int) $start).($num ? ','.abs($num) : '');
    }

    public static function sqlMetadata($data, $tlists = array())
    {
        if (empty($tlists) || !self::inArray($data, $tlists)) {
            $data = str_replace(array('`', ' '), '', $data);
        }

        return ' `'.$data.'` ';
    }
}
