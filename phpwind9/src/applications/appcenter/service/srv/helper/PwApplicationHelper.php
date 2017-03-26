<?php
/**
 * 帮助服务
 *
 * @author Qiong Wu <papa0924@gmail.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 *
 * @version $Id: PwApplicationHelper.php 24585 2013-02-01 04:02:37Z jieyin $
 */
class PwApplicationHelper
{
    /**
     * 解析sql语句，并返回解析后的结果.
     *
     * @param string $strSQL
     * @param string $charset
     * @param string $dbprefix
     *
     * @return array($sqlStatement,$sqlOptions)
     */
    public static function sqlParser($strSQL, $charset, $dbprefix, $engine)
    {
        if (empty($strSQL)) {
            return [];
        }

        $dataSQL = [];
        $strSQL = str_replace(["\r", "\n", "\r\n"], "\n", $strSQL);
        $arrSQL = explode("\n", $strSQL);
        $query = '';
        foreach ($arrSQL as $value) {
            $value = trim($value, " \t");
            if (! $value || substr($value, 0, 2) === '--') {
                continue;
            }
            $query .= $value;
            if (substr($query, -1) != ';') {
                continue;
            }
            $sql_key = strtoupper(substr($query, 0, strpos($query, ' ')));
            preg_match(
                '/(DROP\s+TABLE\s+IF\s+EXISTS|CREATE\s+TABLE(\s+IF\s+NOT\s+EXISTS)?|INSERT\s+INTO|REPLACE\s+INTO|ALTER\s+TABLE)\s+`?(\w+)`?/is',
                $query, $matches);
            $tablename = $matches['3'];
            $_tablename = preg_replace('/(pw_)(.*?)/i', $dbprefix.'\2', $tablename);
            if ($sql_key == 'CREATE') {
                $query = preg_replace(
                    ['/CREATE\s+TABLE(\s+IF\s+NOT\s+EXISTS)?/i', '/\)([\w\s=\x7f-\xff\']*);/i'],
                    [
                        'CREATE TABLE IF NOT EXISTS',
                        ')ENGINE='.$engine.' DEFAULT CHARSET='.$charset, ], $query);
            }
            $query = str_replace($tablename, $_tablename, $query);
            $dataSQL[$sql_key][$_tablename] = trim($query, ';');
            $query = '';
        }

        return $dataSQL;
    }

    /**
     * @param string $logfile
     *
     * @return array
     */
    public static function readInstallLog($logfile, $key = '')
    {
        static $log = [];
        if (! isset($log[$logfile])) {
            $log[$logfile] = is_file($logfile) ? @include $logfile : [];
        }

        return $key ? (isset($log[$logfile][$key]) ? $log[$logfile][$key] : '') : $log[$logfile];
    }

    /**
     * 写log.
     *
     * @param string $logfile
     * @param array  $data
     * @param bool   $additional
     */
    public static function writeInstallLog($logfile, $data, $additional = false)
    {
        if ($additional) {
            $_data = self::readInstallLog($logfile);
            if ($_data) {
                $data = array_merge($_data, $data);
            }
        }

        return WindFile::savePhpData($logfile, $data);
    }

    /**
     * 获取在线应用中心访问地址
     *
     * @param array $args
     *
     * @return string
     */
    public static function acloudUrl($args)
    {
        require_once Wind::getRealPath('ACLOUD:aCloud');
        $_extrasService = ACloudSysCoreCommon::loadSystemClass('extras', 'config.service');
        ACloudSysCoreCommon::setGlobal('g_ips',
            explode('|', ACloudSysCoreDefine::ACLOUD_APPLY_IPS));
        ACloudSysCoreCommon::setGlobal('g_siteurl',
            ACloudSysCoreDefine::ACLOUD_APPLY_SITEURL ? ACloudSysCoreDefine::ACLOUD_APPLY_SITEURL : $_extrasService->getExtra(
                'ac_apply_siteurl'));
        ACloudSysCoreCommon::setGlobal('g_charset',
            ACloudSysCoreDefine::ACLOUD_APPLY_CHARSET ? ACloudSysCoreDefine::ACLOUD_APPLY_CHARSET : $_extrasService->getExtra(
                'ac_apply_charset'));
        Wind::import('ACLOUD:system.bench.service.ACloudSysBenchServiceAdministor');
        $administor = new ACloudSysBenchServiceAdministor();

        return $administor->getLink($args);
    }

    /**
     * 使用socket请求
     *
     * @param unknown_type $url
     * @param unknown_type $tmpdir
     *
     * @return Ambigous <multitype:boolean string , mixed, boolean, NULL, string, string>
     */
    public static function requestAcloudUseSocket($url, $tmpdir = '')
    {
        $http = new WindHttpSocket($url);
        if ($tmpdir !== '') {
            WindFolder::mkRecur($tmpdir);
            $_tmp = $tmpdir.'/tmp.'.Pw::getTime();
            $data = $http->send();
            WindFile::write($_tmp, $data);
            $realname = basename($url);
            $http->close();
            chmod($_tmp, 0766);
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                copy($_tmp, $tmpdir.DIRECTORY_SEPARATOR.$realname);
            } else {
                rename($_tmp, $tmpdir.DIRECTORY_SEPARATOR.$realname);
            }
            $result = [true, $tmpdir.'/'.$realname];
        } else {
            $http->setRedirects(true);
            $result = $http->send();
            $result = trim(stristr($result, "\r\n"), "\r\n");
            if (false !== ($pos = strpos($result, "\r\n"))) {
                $result = substr($result, 0, $pos);
            }
            $result && $result = WindJson::decode($result);
        }

        return $result;
    }

    /**
     * 请求一个Aclude数据信息.
     *
     * @param array  $args
     * @param string $tmpdir
     */
    public static function requestAcloudData($url, $tmpdir = '', $useSocket = false)
    {
        if ($useSocket) {
            return self::requestAcloudUseSocket($url, $tmpdir);
        }
        $http = new WindHttpCurl($url);
        if ($tmpdir !== '') {
            WindFolder::mkRecur($tmpdir);
            $_tmp = $tmpdir.'/tmp.'.Pw::getTime();
            $fp = fopen($_tmp, 'w');
            $opt = [
                CURLOPT_FILE           => $fp,
                CURLOPT_HEADER         => 0,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false, ];
            $http->send('GET', $opt);
            if ($error = $http->getError()) {
                return [false, $error];
            }
            $info = $http->getInfo();
            $realname = basename($info['url']);
            $http->close();
            fclose($fp);
            chmod($_tmp, 0766);
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                copy($_tmp, $tmpdir.DIRECTORY_SEPARATOR.$realname);
            } else {
                rename($_tmp, $tmpdir.DIRECTORY_SEPARATOR.$realname);
            }
            $result = [true, $tmpdir.'/'.$realname];
        } else {
            $result = $http->send('GET', [CURLOPT_FOLLOWLOCATION => true]);
            if (function_exists('json_decode')) {
                $result = json_decode($result, true);
            } else {
                ini_set('pcre.backtrack_limit', 1000000);
                $result && $result = WindJson::decode($result);
            }
        }

        return $result;
    }

    /**
     * 将原安装包中的文件目录，移动到指定位置.
     *
     * 将原安装包中的文件目录移动到指定位置
     *
     * @param 原位置    $source
     * @param 目标位置 $target
     *
     * @return bool PwError
     */
    public static function mvSourcePack($source, $target)
    {
        return self::copyRecursive($source, $target);
    }

    /**
     * 复制文件夹及文件夹内容.
     *
     * @param string $source
     * @param string $target
     *
     * @return bool
     */
    public static function copyRecursive($source, $target, $ignore = [])
    {
        if (is_dir($source)) {
            WindFolder::mkRecur($target);
            $objects = WindFolder::read($source);
            foreach ($objects as $file) {
                if ('.' === $file || '..' === $file) {
                    continue;
                }
                if (in_array($file, $ignore)) {
                    continue;
                }
                if (is_dir($source.DIRECTORY_SEPARATOR.$file)) {
                    self::copyRecursive($source.DIRECTORY_SEPARATOR.$file,
                        $target.DIRECTORY_SEPARATOR.$file);
                } else {
                    @copy($source.DIRECTORY_SEPARATOR.$file,
                        $target.DIRECTORY_SEPARATOR.$file);
                }
            }

            return true;
        } elseif (is_file($source)) {
            return @copy($source, $target);
        } else {
            return false;
        }
    }

    /**
     * 从应用平台下载安装包到本地,.
     *
     * @param string $url
     * @param string $tmpdir
     *
     * @return string
     */
    public static function download($url, $tmpdir)
    {
        WindFolder::mkRecur($tmpdir);
        $_tmp = $tmpdir.'/tmp.'.Pw::getTime();
        $fp = fopen($_tmp, 'w');
        $curl = new WindHttpCurl($url);
        $curl->send('GET', [CURLOPT_FOLLOWLOCATION => true, CURLOPT_FILE => $fp]);
        $info = $curl->getInfo();
        $realname = basename($info['url']);
        $curl->close();
        fclose($fp);
        chmod($_tmp, 0766);
        rename($_tmp, $tmpdir.'/'.$realname);

        return $tmpdir.'/'.$realname;
    }

    /**
     * 解压压缩包,将源文件解压至目标文件
     * 目前只支持zip文件的解压，返回解后包文件绝对路径地址
     *
     * @param string $source
     * @param string $target
     *
     * @return string
     */
    public static function extract($source, $target)
    {
        Wind::import('APPCENTER:service.srv.helper.PwExtractZip');
        $zip = new PwExtractZip();
        if (! $data = $zip->extract($source)) {
            return false;
        }
        $_tmp = '';
        foreach ($data as $value) {
            if ($_tmp === '') {
                list($_tmp) = explode('/', $value['filename'], 2);
            }
            $filename = $target.'/'.$value['filename'];
            WindFolder::mkRecur(dirname($filename));
            WindFile::write($filename, $value['data']);
        }

        return $_tmp ? $target.'/'.$_tmp : false;
    }

    public static function zip($dir, $target)
    {
        $files = self::readRecursive($dir);

        $zip = new PwZip();
        $dir_len = strlen(dirname($dir)) + 1;
        foreach ($files as $v) {
            $zip->addFile(WindFile::read($v), substr($v, $dir_len));
        }

        return WindFile::write($target, $zip->getCompressedFile());
    }

    public static function readRecursive($dir)
    {
        static $files = [];
        $objects = WindFolder::read($dir);
        foreach ($objects as $v) {
            if ($v[0] == '.') {
                continue;
            }
            $object = $dir.'/'.$v;
            if (is_dir($object)) {
                self::readRecursive($dir.'/'.$v);
            } else {
                $files[] = $object;
            }
        }

        return $files;
    }
}
